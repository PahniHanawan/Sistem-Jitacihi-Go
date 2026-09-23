<?php

namespace App\Http\Controllers;

use App\Models\AhpResult;
use App\Models\AssessmentPeriod;
use App\Models\Criterion;
use App\Models\NormalizationResult;
use App\Models\RankingResult;
use App\Services\SawService;
use Illuminate\Http\Request;

class SawController extends Controller
{
    protected SawService $sawService;

    public function __construct(SawService $sawService)
    {
        $this->sawService = $sawService;
    }

    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;

        // ✅ 4 KRITERIA (C1-C4)
        $criteria = Criterion::whereIn('criterion_code', ['C1', 'C2', 'C3', 'C4'])
            ->where('status', 'aktif')
            ->orderBy('criterion_code')
            ->get();

        $ahpResults = [];
        $rankings = collect();
        $normalizations = collect();

        if ($activePeriod) {
            $ahpResults = AhpResult::with('criterion')
                ->where('period_id', $activePeriod->period_id)
                ->get();

            $rankings = RankingResult::with(['productAssessment.product', 'promotionDecision'])
                ->where('period_id', $activePeriod->period_id)
                ->orderBy('rank', 'asc')
                ->get();

            $assessmentIds = $rankings->pluck('assessment_id')->toArray();

            $normalizations = NormalizationResult::with('criterion')
                ->whereIn('assessment_id', $assessmentIds)
                ->get()
                ->groupBy('assessment_id');
        }

        return view('saw.index', compact(
            'periods',
            'activePeriod',
            'criteria',
            'ahpResults',
            'rankings',
            'normalizations'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
        ]);

        try {
            $result = $this->sawService->calculateAndSave((int) $request->period_id);

            return redirect()->route('saw.index', ['period_id' => $request->period_id])
                ->with('success', "✅ Perhitungan SAW selesai! Berhasil memeringkatkan {$result['count']} produk.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
