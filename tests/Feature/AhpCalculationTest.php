<?php

namespace Tests\Feature;

use App\Models\AssessmentPeriod;
use App\Models\Criterion;
use App\Models\User;
use App\Models\Role;
use App\Services\AhpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AhpCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ahp_calculation_matches_thesis_expected_results()
    {
        $role = Role::create(['role_name' => 'owner']);
        $user = User::create([
            'username' => 'owner',
            'password' => bcrypt('password'),
            'role_id' => $role->role_id,
            'full_name' => 'Owner',
            'status' => 'aktif'
        ]);

        $period = AssessmentPeriod::create([
            'period_name' => 'Q1 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
            'status' => 'aktif'
        ]);

        $criteria = [
            Criterion::create(['criterion_code' => 'C1', 'criterion_name' => 'Harga', 'type' => 'cost', 'status' => 'aktif']),
            Criterion::create(['criterion_code' => 'C2', 'criterion_name' => 'Rasio Stok', 'type' => 'benefit', 'status' => 'aktif']),
            Criterion::create(['criterion_code' => 'C3', 'criterion_name' => 'Perputaran', 'type' => 'cost', 'status' => 'aktif']),
            Criterion::create(['criterion_code' => 'C4', 'criterion_name' => 'Margin', 'type' => 'benefit', 'status' => 'aktif']),
            Criterion::create(['criterion_code' => 'C5', 'criterion_name' => 'Lama Simpan', 'type' => 'benefit', 'status' => 'aktif']),
        ];

        // Input 10 values for upper triangle based on issue.md
        // C1_C2 = 0.25, C1_C3 = 0.3333, C1_C4 = 0.5, C1_C5 = 0.5
        // C2_C3 = 1, C2_C4 = 2, C2_C5 = 2
        // C3_C4 = 2, C3_C5 = 2
        // C4_C5 = 1
        $c1 = $criteria[0]->criterion_id;
        $c2 = $criteria[1]->criterion_id;
        $c3 = $criteria[2]->criterion_id;
        $c4 = $criteria[3]->criterion_id;
        $c5 = $criteria[4]->criterion_id;

        $comparisonValues = [
            "{$c1}_{$c2}" => 0.25,
            "{$c1}_{$c3}" => 0.3333,
            "{$c1}_{$c4}" => 0.5,
            "{$c1}_{$c5}" => 0.5,
            "{$c2}_{$c3}" => 1.0,
            "{$c2}_{$c4}" => 2.0,
            "{$c2}_{$c5}" => 2.0,
            "{$c3}_{$c4}" => 2.0,
            "{$c3}_{$c5}" => 2.0,
            "{$c4}_{$c5}" => 1.0,
        ];

        $service = new AhpService();
        $result = $service->calculateAndSave($period->period_id, $comparisonValues, $user->user_id);

        $this->assertTrue($result['is_valid']);
        $this->assertEqualsWithDelta(5.0100, $result['lambda_max'], 0.005);
        $this->assertEqualsWithDelta(0.0025, $result['ci'], 0.001);
        $this->assertEqualsWithDelta(0.0022, $result['cr'], 0.001);

        $this->assertEqualsWithDelta(0.0828, $result['weights'][$c1], 0.001);
        $this->assertEqualsWithDelta(0.3113, $result['weights'][$c2], 0.001);
        $this->assertEqualsWithDelta(0.2946, $result['weights'][$c3], 0.001);
        $this->assertEqualsWithDelta(0.1556, $result['weights'][$c4], 0.001);
        $this->assertEqualsWithDelta(0.1556, $result['weights'][$c5], 0.001);
    }
}
