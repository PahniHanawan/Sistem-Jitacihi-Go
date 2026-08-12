<?php

namespace App\Http\Controllers;

use App\Models\AhpResult;
use App\Models\AssessmentPeriod;
use App\Models\Criterion;
use App\Models\PairwiseComparison;
use App\Services\AhpService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    protected AhpService $ahpService;

    public function __construct(AhpService $ahpService)
    {
        $this->ahpService = $ahpService;
    }

    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;
        $criteria = Criterion::where('status', 'aktif')->orderBy('criterion_code')->get();

        $existingComparisons = [];
        $ahpResults = [];
        $consistencyData = null;

        if ($activePeriod) {
            $comps = PairwiseComparison::where('period_id', $activePeriod->period_id)->get();
            foreach ($comps as $c) {
                $existingComparisons["{$c->criterion_a_id}_{$c->criterion_b_id}"] = (float) $c->value;
            }

            $ahpResults = AhpResult::with('criterion')
                ->where('period_id', $activePeriod->period_id)
                ->get();

            if ($ahpResults->isNotEmpty()) {
                $first = $ahpResults->first();
                $consistencyData = [
                    'lambda_max' => $first->lambda_max,
                    'ci' => $first->ci,
                    'cr' => $first->cr,
                    'is_valid' => $first->is_valid,
                ];
            }
        }

        return view('ahp.index', compact(
            'periods',
            'activePeriod',
            'criteria',
            'existingComparisons',
            'ahpResults',
            'consistencyData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
            'comparisons' => 'required|array',
        ]);

        try {
            $result = $this->ahpService->calculateAndSave(
                (int) $request->period_id,
                $request->comparisons,
                (int) auth()->id()
            );

            if ($result['is_valid']) {
                return redirect()->route('ahp.index', ['period_id' => $request->period_id])
                    ->with('success', "Pembobotan AHP berhasil dihitung! Nilai CR = {$result['cr']} (Konsisten \le 0.10).");
            } else {
                return redirect()->route('ahp.index', ['period_id' => $request->period_id])
                    ->with('warning', "Perhitungan AHP selesai tetapi TIDAK KONSISTEN (CR = {$result['cr']} > 0.10). Silakan tinjau kembali nilai perbandingan!");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
