<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Product;
use App\Models\ProductAssessment;
use Illuminate\Http\Request;

class ProductAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;
        $products = Product::where('status', 'aktif')->orderBy('product_code')->get();

        $assessments = collect();
        if ($activePeriod) {
            $assessments = ProductAssessment::with('product')
                ->where('period_id', $activePeriod->period_id)
                ->get();
        }

        return view('assessments.index', compact('periods', 'activePeriod', 'products', 'assessments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
            'product_id' => 'required|exists:products,product_id',
            'initial_stock' => 'required|integer|min:0',
            'final_stock' => 'required|integer|min:0',
            'units_sold' => 'required|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
        ]);

        $dataStatus = ($validated['initial_stock'] > 0 && $validated['units_sold'] >= 0) ? 'layak' : 'belum memadai';

        ProductAssessment::updateOrCreate(
            [
                'period_id' => $validated['period_id'],
                'product_id' => $validated['product_id'],
            ],
            array_merge($validated, ['data_status' => $dataStatus])
        );

        return redirect()->route('assessments.index', ['period_id' => $validated['period_id']])
            ->with('success', 'Data penilaian produk berhasil disimpan.');
    }

    public function destroy(ProductAssessment $assessment)
    {
        $periodId = $assessment->period_id;
        $assessment->delete();

        return redirect()->route('assessments.index', ['period_id' => $periodId])
            ->with('success', 'Data penilaian produk berhasil dihapus.');
    }
}
