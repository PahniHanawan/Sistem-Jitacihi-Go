<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\PromotionDecision;
use App\Models\RankingResult;
use Illuminate\Http\Request;

class PromotionDecisionController extends Controller
{
    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', optional($periods->firstWhere('status', 'aktif'))->period_id ?? optional($periods->first())->period_id);

        $activePeriod = $selectedPeriodId ? AssessmentPeriod::find($selectedPeriodId) : null;

        $rankings = collect();
        if ($activePeriod) {
            $rankings = RankingResult::with(['productAssessment.product', 'promotionDecision.decidedBy'])
                ->where('period_id', $activePeriod->period_id)
                ->orderBy('rank', 'asc')
                ->get();
        }

        return view('decisions.index', compact('periods', 'activePeriod', 'rankings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ranking_id' => 'required|exists:ranking_results,ranking_id',
            'discount_type' => 'required|string|max:50',
            'reason' => 'nullable|string',
        ]);

        PromotionDecision::updateOrCreate(
            ['ranking_id' => $validated['ranking_id']],
            [
                'discount_type' => $validated['discount_type'],
                'reason' => $validated['reason'],
                'decided_by' => auth()->id(),
                'decided_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Keputusan promosi/diskon berhasil disimpan.');
    }
}
