<?php

namespace App\Services;

use App\Models\AhpResult;
use App\Models\Criterion;
use App\Models\NormalizationResult;
use App\Models\ProductAssessment;
use App\Models\RankingResult;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Compute SAW Normalization and Ranking for a given period.
     *
     * @param int $periodId
     * @return array
     */
    public function calculateAndSave(int $periodId): array
    {
        // 1. Ambil Bobot AHP Valid
        $ahpResults = AhpResult::where('period_id', $periodId)
            ->where('is_valid', 1)
            ->get();

        if ($ahpResults->isEmpty()) {
            throw new \Exception('Bobot AHP belum dihitung atau tidak konsisten (CR > 0.10) pada periode ini.');
        }

        $weights = $ahpResults->pluck('weight', 'criterion_id')->toArray();

        // 2. Ambil Kriteria & Penilaian Produk
        $criteria = Criterion::whereIn('criterion_id', array_keys($weights))
            ->where('status', 'aktif')
            ->get();

        $assessments = ProductAssessment::with('product')
            ->where('period_id', $periodId)
            ->where('data_status', 'layak')
            ->get();

        if ($assessments->isEmpty()) {
            throw new \Exception('Tidak ada data penilaian produk yang layak untuk dihitung pada periode ini.');
        }

        // 3. Ekstrak Nilai Mentah (Raw Values X_ij)
        $rawValues = []; // [$assessmentId][$criterionId] = float
        foreach ($assessments as $ast) {
            foreach ($criteria as $crit) {
                $val = 0.0;
                switch (strtoupper($crit->criterion_code)) {
                    case 'C1': // Jumlah Terjual
                        $val = (float) $ast->units_sold;
                        break;
                    case 'C2': // Sisa Stok
                        $val = (float) $ast->final_stock;
                        break;
                    case 'C3': // Margin Keuntungan
                        $val = (float) ($ast->selling_price - $ast->cost_price);
                        break;
                    case 'C4': // Persentase Penjualan
                        $val = ($ast->initial_stock > 0)
                            ? ($ast->units_sold / $ast->initial_stock) * 100
                            : 0.0;
                        break;
                    default:
                        $val = (float) $ast->units_sold;
                        break;
                }
                $rawValues[$ast->assessment_id][$crit->criterion_id] = $val;
            }
        }

        // 4. Cari Nilai Max dan Min per Kriteria
        $maxValues = [];
        $minValues = [];
        foreach ($criteria as $crit) {
            $cid = $crit->criterion_id;
            $critColumn = array_column($rawValues, $cid);
            $maxValues[$cid] = count($critColumn) > 0 ? max($critColumn) : 1.0;
            $minValues[$cid] = count($critColumn) > 0 ? min($critColumn) : 0.0;
        }

        // 5. Normalisasi Matriks SAW R_ij & Hitung Preferensi V_i
        $normalizedValues = [];
        $preferenceScores = [];

        foreach ($assessments as $ast) {
            $aid = $ast->assessment_id;
            $vi = 0.0;

            foreach ($criteria as $crit) {
                $cid = $crit->criterion_id;
                $x = $rawValues[$aid][$cid];
                $r = 0.0;

                if ($crit->type === 'benefit') {
                    $r = ($maxValues[$cid] > 0) ? ($x / $maxValues[$cid]) : 0.0;
                } else { // cost
                    $r = ($x > 0) ? ($minValues[$cid] / $x) : 1.0;
                }

                $normalizedValues[$aid][$cid] = $r;
                $w = $weights[$cid] ?? 0.0;
                $vi += ($r * $w);
            }

            $preferenceScores[$aid] = $vi;
        }

        // 6. Urutkan Peringkat (Ranking) dari V_i Terbesar ke Terkecil
        arsort($preferenceScores);

        // 7. Simpan Hasil Normalisasi dan Ranking ke Database
        DB::transaction(function () use ($periodId, $assessments, $rawValues, $normalizedValues, $preferenceScores) {
            $assessmentIds = $assessments->pluck('assessment_id')->toArray();

            NormalizationResult::whereIn('assessment_id', $assessmentIds)->delete();
            RankingResult::where('period_id', $periodId)->delete();

            foreach ($rawValues as $aid => $crits) {
                foreach ($crits as $cid => $rawVal) {
                    NormalizationResult::create([
                        'assessment_id' => $aid,
                        'criterion_id' => $cid,
                        'raw_value' => round($rawVal, 4),
                        'normalized_value' => round($normalizedValues[$aid][$cid], 4),
                    ]);
                }
            }

            $rank = 1;
            foreach ($preferenceScores as $aid => $vi) {
                RankingResult::create([
                    'assessment_id' => $aid,
                    'period_id' => $periodId,
                    'preference_value' => round($vi, 4),
                    'rank' => $rank++,
                ]);
            }
        });

        return [
            'preference_scores' => $preferenceScores,
            'count' => count($preferenceScores),
        ];
    }
}
