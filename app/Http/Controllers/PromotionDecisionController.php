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

        // ================================================================
        // FILTER & PAGINATION
        // ================================================================
        $perPage = $request->get('per_page', 10); // 5, 10, 25, 50, 100
        $search = $request->get('search', '');
        $rankFilter = $request->get('rank_filter', 'all'); // all, top5, top10, top20

        $rankings = collect();
        if ($activePeriod) {
            $query = RankingResult::with([
                'productAssessment.product',
                'promotionDecision.decidedBy'
            ])
                ->where('period_id', $activePeriod->period_id)
                ->orderBy('rank', 'asc');

            // Filter berdasarkan ranking
            if ($rankFilter === 'top5') {
                $query->where('rank', '<=', 5);
            } elseif ($rankFilter === 'top10') {
                $query->where('rank', '<=', 10);
            } elseif ($rankFilter === 'top20') {
                $query->where('rank', '<=', 20);
            }

            // Filter pencarian (nama produk atau kode)
            if (!empty($search)) {
                $query->whereHas('productAssessment.product', function ($q) use ($search) {
                    $q->where('product_name', 'LIKE', "%{$search}%")
                        ->orWhere('product_code', 'LIKE', "%{$search}%");
                });
            }

            $rankings = $query->paginate($perPage);
        }

        return view('decisions.index', compact('periods', 'activePeriod', 'rankings', 'search', 'rankFilter', 'perPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ranking_id' => 'required|exists:ranking_results,ranking_id',
            'discount_type' => 'required|string|max:50',
            'reason' => 'nullable|string|max:1000',
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

        return redirect()->back()->with('success', '✅ Keputusan promosi/diskon berhasil disimpan.');
    }
}