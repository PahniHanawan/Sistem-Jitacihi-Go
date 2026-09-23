<?php

namespace App\Http\Controllers;

use App\Models\AhpResult;
use App\Models\AssessmentPeriod;
use App\Models\RankingResult;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;
        $ahpResults = collect();
        $rankings = collect();

        if ($activePeriod) {
            $ahpResults = AhpResult::with('criterion')
                ->where('period_id', $activePeriod->period_id)
                ->get();

            $rankings = RankingResult::with(['productAssessment.product', 'promotionDecision.decidedBy'])
                ->where('period_id', $activePeriod->period_id)
                ->orderBy('rank', 'asc')
                ->get();
        }

        $reportsHistory = Report::with('generatedBy')->orderBy('generated_at', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'periods',
            'activePeriod',
            'ahpResults',
            'rankings',
            'reportsHistory'
        ));
    }

    public function print(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
        ]);

        $period = AssessmentPeriod::findOrFail($request->period_id);

        $ahpResults = AhpResult::with('criterion')
            ->where('period_id', $period->period_id)
            ->get();

        $rankings = RankingResult::with(['productAssessment.product', 'promotionDecision.decidedBy'])
            ->where('period_id', $period->period_id)
            ->orderBy('rank', 'asc')
            ->get();

        Report::create([
            'generated_by' => auth()->id(),
            'generated_at' => now(),
            'file_path' => null,
        ]);

        return view('reports.print', compact('period', 'ahpResults', 'rankings'));
    }
}