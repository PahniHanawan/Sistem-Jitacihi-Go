<?php

namespace App\Http\Controllers;

use App\Models\AhpResult;
use App\Models\AssessmentPeriod;
use App\Models\Criterion;
use App\Models\PairwiseComparison;
use App\Services\AhpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Tambahkan ini

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

        // ✅ 4 KRITERIA (C1-C4)
        $criteria = Criterion::whereIn('criterion_code', ['C1', 'C2', 'C3', 'C4'])
            ->where('status', 'aktif')
            ->orderBy('criterion_code')
            ->get();

        $existingComparisons = [];
        $ahpResults = collect();
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
                    'is_valid' => (bool) $first->is_valid,
                ];
            }
        }

        // ✅ Detail Perhitungan AHP
        $normalizedMatrix = [];
        $consistencyVector = [];

        if ($activePeriod && $criteria->count() >= 2) {
            $pairs = PairwiseComparison::where('period_id', $activePeriod->period_id)->get();
            $criteriaIds = $criteria->pluck('criterion_id')->toArray();
            $n = count($criteriaIds);
            $matrix = array_fill(0, $n, array_fill(0, $n, 1));

            foreach ($pairs as $pair) {
                $i = array_search($pair->criterion_a_id, $criteriaIds);
                $j = array_search($pair->criterion_b_id, $criteriaIds);
                if ($i !== false && $j !== false) {
                    $matrix[$i][$j] = (float) $pair->value;
                    $matrix[$j][$i] = 1 / (float) $pair->value;
                }
            }

            // Normalisasi Matriks
            $colSums = array_fill(0, $n, 0);
            for ($j = 0; $j < $n; $j++) {
                for ($i = 0; $i < $n; $i++) {
                    $colSums[$j] += $matrix[$i][$j];
                }
            }

            $normalizedMatrix = [];
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $normalizedMatrix[$i][$j] = $colSums[$j] > 0 ? round($matrix[$i][$j] / $colSums[$j], 4) : 0;
                }
            }

            // Bobot
            $weights = [];
            for ($i = 0; $i < $n; $i++) {
                $weights[$criteriaIds[$i]] = round(array_sum($normalizedMatrix[$i]) / $n, 4);
            }

            // Consistency Vector
            $consistencyVector = [];
            for ($i = 0; $i < $n; $i++) {
                $sum = 0;
                for ($j = 0; $j < $n; $j++) {
                    $sum += $matrix[$i][$j] * $weights[$criteriaIds[$j]];
                }
                $aw = round($sum, 4);
                $w = $weights[$criteriaIds[$i]];
                $cv = $w > 0 ? round($aw / $w, 4) : 0;
                $consistencyVector[$criteriaIds[$i]] = [
                    'aw' => $aw,
                    'w' => $w,
                    'cv' => $cv,
                ];
            }
        }

        return view('ahp.index', compact(
            'periods',
            'activePeriod',
            'criteria',
            'existingComparisons',
            'ahpResults',
            'consistencyData',
            'normalizedMatrix',
            'consistencyVector'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:assessment_periods,period_id',
            'comparisons' => 'required|array|size:6', // ✅ 4 kriteria → 6 pasang
            'comparisons.*' => 'required|numeric|between:0.1111,9',
        ]);

        try {
            // ✅ PERBAIKAN: Gunakan Auth::id() atau auth()->id()
            $userId = Auth::id(); // atau auth()->id()

            if (!$userId) {
                return redirect()->back()->with('error', '❌ Anda harus login terlebih dahulu.');
            }

            $result = $this->ahpService->calculateAndSave(
                (int) $request->period_id,
                $request->comparisons,
                (int) $userId
            );

            if ($result['is_valid']) {
                return redirect()->route('ahp.index', ['period_id' => $request->period_id])
                    ->with('success', "✅ Pembobotan AHP berhasil! CR = {$result['cr']} (Konsisten ≤ 0.10)");
            } else {
                return redirect()->route('ahp.index', ['period_id' => $request->period_id])
                    ->with('warning', "⚠️ Perhitungan AHP selesai tetapi TIDAK KONSISTEN (CR = {$result['cr']} > 0.10). Silakan perbaiki nilai perbandingan!");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghitung AHP: ' . $e->getMessage());
        }
    }
}
