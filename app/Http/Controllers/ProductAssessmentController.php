<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Product;
use App\Models\ProductAssessment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        
        // Ambil period_id dari query param, atau default ke periode aktif, atau periode terbaru
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;
        $products = Product::where('status', 'aktif')->orderBy('product_code')->get();

        // 1. STATS DATA PERIODE TERPILIH
        $allAssessments = collect();
        $stats = [
            'total' => 0,
            'layak' => 0,
            'belum_memadai' => 0,
        ];

        if ($activePeriod) {
            $allAssessments = ProductAssessment::with('product')
                ->where('period_id', $activePeriod->period_id)
                ->get();

            $stats = [
                'total' => $allAssessments->count(),
                'layak' => $allAssessments->where('data_status', 'layak')->count(),
                'belum_memadai' => $allAssessments->where('data_status', 'belum memadai')->count(),
            ];
        }

        // 2. DATA TABEL DENGAN PAGINATION
        $assessments = collect();
        if ($activePeriod) {
            $assessments = ProductAssessment::with('product')
                ->where('period_id', $activePeriod->period_id)
                ->orderBy('assessment_id', 'desc')
                ->paginate(10);
        }

        // 3. MAP DATA STOK DARI PERIODE SEBELUMNYA UNTUK AUTO-FILL FE
        $previousAssessmentMap = [];
        if ($activePeriod) {
            $previousPeriod = AssessmentPeriod::where('end_date', '<', $activePeriod->start_date)
                ->orderBy('end_date', 'desc')
                ->first();

            if ($previousPeriod) {
                $previousAssessmentMap = ProductAssessment::where('period_id', $previousPeriod->period_id)
                    ->get()
                    ->keyBy('product_id')
                    ->map(function ($item) {
                        return [
                            'final_stock' => $item->final_stock,
                            'selling_price' => $item->selling_price,
                            'cost_price' => $item->cost_price,
                        ];
                    })
                    ->toArray();
            }
        }

        return view('assessments.index', compact(
            'periods',
            'activePeriod',
            'products',
            'assessments',
            'stats',
            'previousAssessmentMap',
            'allAssessments'
        ));
    }

    /**
     * Store a newly created or updated resource in storage (Upsert Logic).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
            'product_id' => 'required|exists:products,product_id',
            'initial_stock' => 'required|integer|min:0',
            'final_stock' => 'required|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
        ]);

        $period = AssessmentPeriod::findOrFail($validated['period_id']);

        // LOGIKA RESTRIKSI: PERIODE BERAKHIR / NONAKTIF
        if ($period->status !== 'aktif') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak dapat menambah/mengubah data penilaian pada periode yang sudah berakhir atau nonaktif.');
        }

        // LOGIKA KONSISTENSI STOK
        if ($validated['final_stock'] > $validated['initial_stock']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['final_stock' => 'Stok akhir tidak boleh melebihi stok awal.']);
        }

        // LOGIKA KONSISTENSI HARGA
        if ($validated['cost_price'] > $validated['selling_price']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['cost_price' => 'Harga pokok tidak boleh melebihi harga jual.']);
        }

        // LOGIKA OTOMATIS: HITUNG TERJUAL
        $unitsSold = max(0, $validated['initial_stock'] - $validated['final_stock']);

        // LOGIKA STATUS DATA (LAYAK VS BELUM MEMADAI)
        $entryDate = Carbon::parse($validated['entry_date']);
        $endDate = Carbon::parse($period->end_date);
        $observationDays = $entryDate->diffInDays($endDate);

        $dataStatus = ($validated['initial_stock'] > 0 && $unitsSold >= 0 && $observationDays >= 60)
            ? 'layak'
            : 'belum memadai';

        // CEK APAKAH SUDAH ADA DATA SEBELUMNYA (FOR USER FEEDBACK)
        $existing = ProductAssessment::where('period_id', $validated['period_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data penilaian untuk produk ini pada periode tersebut sudah ada. Gunakan menu Edit pada tabel untuk mengubahnya.');
        }

        ProductAssessment::create(
            array_merge($validated, [
                'units_sold' => $unitsSold,
                'data_status' => $dataStatus
            ])
        );

        $message = 'Data penilaian produk berhasil ditambahkan.';

        return redirect()->route('assessments.index', ['period_id' => $validated['period_id']])
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAssessment $assessment)
    {
        $assessment->load(['product', 'period']);
        return view('assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAssessment $assessment)
    {
        $assessment->load(['product', 'period']);
        return view('assessments.edit', compact('assessment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAssessment $assessment)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'initial_stock' => 'required|integer|min:0',
            'final_stock' => 'required|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
        ]);

        $period = AssessmentPeriod::findOrFail($assessment->period_id);

        // LOGIKA RESTRIKSI: PERIODE BERAKHIR / NONAKTIF
        if ($period->status !== 'aktif') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak dapat mengubah data penilaian pada periode yang sudah berakhir atau nonaktif.');
        }

        if ($validated['final_stock'] > $validated['initial_stock']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['final_stock' => 'Stok akhir tidak boleh melebihi stok awal.']);
        }

        if ($validated['cost_price'] > $validated['selling_price']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['cost_price' => 'Harga pokok tidak boleh melebihi harga jual.']);
        }

        // LOGIKA OTOMATIS: HITUNG TERJUAL
        $unitsSold = max(0, $validated['initial_stock'] - $validated['final_stock']);

        $entryDate = Carbon::parse($validated['entry_date']);
        $endDate = Carbon::parse($period->end_date);
        $observationDays = $entryDate->diffInDays($endDate);

        $dataStatus = ($validated['initial_stock'] > 0 && $unitsSold >= 0 && $observationDays >= 60)
            ? 'layak'
            : 'belum memadai';

        $assessment->update(array_merge($validated, [
            'units_sold' => $unitsSold,
            'data_status' => $dataStatus
        ]));

        return redirect()->route('assessments.index', ['period_id' => $assessment->period_id])
            ->with('success', 'Data penilaian produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAssessment $assessment)
    {
        $period = AssessmentPeriod::find($assessment->period_id);
        if ($period && $period->status !== 'aktif') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus data pada periode yang sudah berakhir/nonaktif.');
        }

        $assessment->delete();

        return redirect()->back()->with('success', 'Data penilaian berhasil dihapus.');
    }
}
