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
     * Compute SAW Normalization and Ranking
     * ✅ 4 KRITERIA (C1-C4)
     */
    public function calculateAndSave(int $periodId): array
    {
        // 1. Ambil Bobot AHP Valid (4 kriteria)
        $ahpResults = AhpResult::where('period_id', $periodId)
            ->where('is_valid', 1)
            ->get();

        if ($ahpResults->isEmpty()) {
            throw new \Exception('❌ Bobot AHP belum dihitung atau tidak konsisten (CR > 0.10).');
        }

        $weights = $ahpResults->pluck('weight', 'criterion_id')->toArray();

        // 2. Ambil Kriteria & Penilaian Produk
        $criteria = Criterion::whereIn('criterion_id', array_keys($weights))
            ->where('status', 'aktif')
            ->get();

        $assessments = ProductAssessment::with(['product', 'period'])
            ->where('period_id', $periodId)
            ->where('data_status', 'layak')
            ->get();

        if ($assessments->isEmpty()) {
            throw new \Exception('❌ Tidak ada data penilaian produk yang layak.');
        }

        // 3. Ekstrak Nilai Mentah (Raw Values X_ij) — 4 KRITERIA
        $rawValues = [];
        foreach ($assessments as $ast) {
            foreach ($criteria as $crit) {
                $val = 0.0;
                switch (strtoupper($crit->criterion_code)) {
                    case 'C1': // Harga Produk (Cost)
                        $val = (float) $ast->selling_price;
                        break;
                    case 'C2': // Kecepatan Perputaran (Cost)
                        $avgStock = ($ast->initial_stock + $ast->final_stock) / 2;
                        $entryDate = \Carbon\Carbon::parse($ast->entry_date);
                        $endDate = \Carbon\Carbon::parse($ast->period->end_date);
                        $days = $entryDate->diffInDays($endDate);
                        $months = $days / 30.44;
                        if ($avgStock > 0 && $months > 0) {
                            $val = $ast->units_sold / ($avgStock * $months);
                        } else {
                            $val = 0.0;
                        }
                        break;
                    case 'C3': // Margin Keuntungan (Benefit)
                        $val = ($ast->selling_price > 0)
                            ? (($ast->selling_price - $ast->cost_price) / $ast->selling_price)
                            : 0.0;
                        break;
                    case 'C4': // Lama Penyimpanan Stok (Benefit) → HARUS DALAM BULAN
                        $entryDate = \Carbon\Carbon::parse($ast->entry_date);
                        $endDate = \Carbon\Carbon::parse($ast->period->end_date);
                        $days = (float) $entryDate->diffInDays($endDate);
                        $val = $days / 30.44; // ✅ KONVERSI KE BULAN
                        break;
                    default:
                        $val = 0.0;
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
                    if (strtoupper($crit->criterion_code) === 'C2') {
                        // ✅ Penanganan khusus untuk C2 (Kecepatan Perputaran)
                        if ($x == 0) {
                            $r = 1.0;
                        } else {
                            $positives = array_filter(array_column($rawValues, $cid), function($v) { return $v > 0; });
                            $minPos = count($positives) > 0 ? min($positives) : $x;
                            $r = $minPos / $x;
                        }
                    } else {
                        $r = ($x > 0) ? ($minValues[$cid] / $x) : 1.0;
                    }
                }

                $normalizedValues[$aid][$cid] = $r;
                $w = $weights[$cid] ?? 0.0;
                $vi += ($r * $w);
            }

            $preferenceScores[$aid] = $vi;
        }

        // 6. Urutkan Peringkat (Ranking) menggunakan Aturan Tie-Breaking
        $sortedAssessments = $assessments->all();
        usort($sortedAssessments, function($a, $b) use ($preferenceScores, $rawValues, $criteria) {
            $aidA = $a->assessment_id;
            $aidB = $b->assessment_id;

            // 1. Preferensi V_i (DESC)
            if (abs($preferenceScores[$aidA] - $preferenceScores[$aidB]) > 0.00001) {
                return $preferenceScores[$aidB] <=> $preferenceScores[$aidA];
            }

            // ✅ Tie-breaking: C2 (Perputaran) < C4 (Lama Simpan) > C3 (Margin)
            $c2Id = $criteria->firstWhere('criterion_code', 'C2')?->criterion_id;
            $c4Id = $criteria->firstWhere('criterion_code', 'C4')?->criterion_id;
            $c3Id = $criteria->firstWhere('criterion_code', 'C3')?->criterion_id;

            // 2. C2 (Kecepatan Perputaran) → lebih rendah lebih baik (Cost)
            if ($c2Id && abs($rawValues[$aidA][$c2Id] - $rawValues[$aidB][$c2Id]) > 0.00001) {
                return $rawValues[$aidA][$c2Id] <=> $rawValues[$aidB][$c2Id]; // ASC
            }
            // 3. C4 (Lama Penyimpanan) → lebih lama lebih baik (Benefit)
            if ($c4Id && abs($rawValues[$aidA][$c4Id] - $rawValues[$aidB][$c4Id]) > 0.00001) {
                return $rawValues[$aidB][$c4Id] <=> $rawValues[$aidA][$c4Id]; // DESC
            }
            // 4. C3 (Margin) → lebih tinggi lebih baik (Benefit)
            if ($c3Id && abs($rawValues[$aidA][$c3Id] - $rawValues[$aidB][$c3Id]) > 0.00001) {
                return $rawValues[$aidB][$c3Id] <=> $rawValues[$aidA][$c3Id]; // DESC
            }
            // 5. Product Code ASC
            return strcmp($a->product->product_code, $b->product->product_code);
        });

        // Rekonstruksi array preferenceScores
        $orderedPreferenceScores = [];
        foreach ($sortedAssessments as $ast) {
            $orderedPreferenceScores[$ast->assessment_id] = $preferenceScores[$ast->assessment_id];
        }
        $preferenceScores = $orderedPreferenceScores;

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
