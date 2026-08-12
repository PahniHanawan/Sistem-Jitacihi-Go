<?php

namespace App\Services;

use App\Models\AhpResult;
use App\Models\Criterion;
use App\Models\PairwiseComparison;
use Illuminate\Support\Facades\DB;

class AhpService
{
    private array $riTable = [
        1 => 0.0,
        2 => 0.0,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
    ];

    /**
     * Compute AHP Weights and Consistency Ratio from Pairwise Comparison inputs.
     *
     * @param int $periodId
     * @param array $comparisonValues  Format: ['critA_critB' => float_val, ...]
     * @param int $userId
     * @return array ['weights' => [...], 'lambda_max' => float, 'ci' => float, 'cr' => float, 'is_valid' => bool]
     */
    public function calculateAndSave(int $periodId, array $comparisonValues, int $userId): array
    {
        $criteria = Criterion::where('status', 'aktif')->orderBy('criterion_id')->get();
        $n = $criteria->count();

        if ($n === 0) {
            throw new \Exception('Tidak ada kriteria aktif untuk dihitung.');
        }

        $criteriaIds = $criteria->pluck('criterion_id')->toArray();
        $matrix = [];

        // 1. Inisialisasi Matriks A (1.0 untuk diagonal)
        foreach ($criteriaIds as $idA) {
            foreach ($criteriaIds as $idB) {
                if ($idA === $idB) {
                    $matrix[$idA][$idB] = 1.0;
                }
            }
        }

        // 2. Isi nilai dari input user & simpan ke pairwise_comparisons
        DB::transaction(function () use ($periodId, $criteriaIds, $comparisonValues, $userId, &$matrix) {
            // Hapus perbandingan lama untuk periode ini
            PairwiseComparison::where('period_id', $periodId)->delete();

            for ($i = 0; $i < count($criteriaIds); $i++) {
                for ($j = $i + 1; $j < count($criteriaIds); $j++) {
                    $idA = $criteriaIds[$i];
                    $idB = $criteriaIds[$j];
                    $key = "{$idA}_{$idB}";

                    $val = isset($comparisonValues[$key]) ? (float) $comparisonValues[$key] : 1.0;
                    if ($val <= 0) {
                        $val = 1.0;
                    }

                    $matrix[$idA][$idB] = $val;
                    $matrix[$idB][$idA] = 1.0 / $val;

                    PairwiseComparison::create([
                        'period_id' => $periodId,
                        'criterion_a_id' => $idA,
                        'criterion_b_id' => $idB,
                        'value' => $val,
                        'created_by' => $userId,
                    ]);
                }
            }
        });

        // 3. Hitung Jumlah Kolom S_j
        $colSums = [];
        foreach ($criteriaIds as $idB) {
            $sum = 0.0;
            foreach ($criteriaIds as $idA) {
                $sum += $matrix[$idA][$idB];
            }
            $colSums[$idB] = $sum;
        }

        // 4. Normalisasi Matriks & Hitung Bobot Prioritas W_i
        $normalizedMatrix = [];
        $weights = [];
        foreach ($criteriaIds as $idA) {
            $rowSum = 0.0;
            foreach ($criteriaIds as $idB) {
                $normVal = $matrix[$idA][$idB] / $colSums[$idB];
                $normalizedMatrix[$idA][$idB] = $normVal;
                $rowSum += $normVal;
            }
            $weights[$idA] = $rowSum / $n;
        }

        // 5. Hitung Vektor A * W
        $awVector = [];
        foreach ($criteriaIds as $idA) {
            $sum = 0.0;
            foreach ($criteriaIds as $idB) {
                $sum += $matrix[$idA][$idB] * $weights[$idB];
            }
            $awVector[$idA] = $sum;
        }

        // 6. Hitung Lambda Max
        $lambdaSum = 0.0;
        foreach ($criteriaIds as $idA) {
            $lambdaSum += $awVector[$idA] / $weights[$idA];
        }
        $lambdaMax = $lambdaSum / $n;

        // 7. Hitung CI dan CR
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0.0;
        $ri = $this->riTable[$n] ?? 1.49;
        $cr = ($ri > 0) ? ($ci / $ri) : 0.0;
        $isValid = ($cr <= 0.10);

        // 8. Simpan Hasil AHP ke tabel ahp_results
        DB::transaction(function () use ($periodId, $criteriaIds, $weights, $lambdaMax, $ci, $cr, $isValid) {
            AhpResult::where('period_id', $periodId)->delete();

            foreach ($criteriaIds as $criterionId) {
                AhpResult::create([
                    'period_id' => $periodId,
                    'criterion_id' => $criterionId,
                    'weight' => round($weights[$criterionId], 4),
                    'lambda_max' => round($lambdaMax, 4),
                    'ci' => round($ci, 4),
                    'cr' => round($cr, 4),
                    'is_valid' => $isValid ? 1 : 0,
                ]);
            }
        });

        return [
            'weights' => $weights,
            'lambda_max' => round($lambdaMax, 4),
            'ci' => round($ci, 4),
            'cr' => round($cr, 4),
            'is_valid' => $isValid,
        ];
    }
}
