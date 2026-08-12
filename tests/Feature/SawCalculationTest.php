<?php

namespace Tests\Feature;

use App\Models\AhpResult;
use App\Models\AssessmentPeriod;
use App\Models\Criterion;
use App\Models\Product;
use App\Models\ProductAssessment;
use App\Models\RankingResult;
use App\Services\SawService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SawCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_saw_calculation_matches_thesis_expected_results()
    {
        $period = AssessmentPeriod::create([
            'period_name' => 'Evaluasi SAW',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
            'status' => 'aktif'
        ]);

        $c1 = Criterion::create(['criterion_code' => 'C1', 'criterion_name' => 'Harga', 'type' => 'cost', 'status' => 'aktif']);
        $c2 = Criterion::create(['criterion_code' => 'C2', 'criterion_name' => 'Rasio Stok', 'type' => 'benefit', 'status' => 'aktif']);
        $c3 = Criterion::create(['criterion_code' => 'C3', 'criterion_name' => 'Perputaran', 'type' => 'cost', 'status' => 'aktif']);
        $c4 = Criterion::create(['criterion_code' => 'C4', 'criterion_name' => 'Margin', 'type' => 'benefit', 'status' => 'aktif']);
        $c5 = Criterion::create(['criterion_code' => 'C5', 'criterion_name' => 'Lama Simpan', 'type' => 'benefit', 'status' => 'aktif']);

        // Insert bobot AHP valid dari skripsi
        AhpResult::create(['period_id' => $period->period_id, 'criterion_id' => $c1->criterion_id, 'weight' => 0.0828, 'lambda_max' => 5, 'ci' => 0, 'cr' => 0, 'is_valid' => 1]);
        AhpResult::create(['period_id' => $period->period_id, 'criterion_id' => $c2->criterion_id, 'weight' => 0.3113, 'lambda_max' => 5, 'ci' => 0, 'cr' => 0, 'is_valid' => 1]);
        AhpResult::create(['period_id' => $period->period_id, 'criterion_id' => $c3->criterion_id, 'weight' => 0.2946, 'lambda_max' => 5, 'ci' => 0, 'cr' => 0, 'is_valid' => 1]);
        AhpResult::create(['period_id' => $period->period_id, 'criterion_id' => $c4->criterion_id, 'weight' => 0.1556, 'lambda_max' => 5, 'ci' => 0, 'cr' => 0, 'is_valid' => 1]);
        AhpResult::create(['period_id' => $period->period_id, 'criterion_id' => $c5->criterion_id, 'weight' => 0.1556, 'lambda_max' => 5, 'ci' => 0, 'cr' => 0, 'is_valid' => 1]);

        $prodA = Product::create(['product_code' => 'P001', 'product_name' => 'Produk A', 'category' => 'Test', 'status' => 'aktif']);
        $prodB = Product::create(['product_code' => 'P002', 'product_name' => 'Produk B', 'category' => 'Test', 'status' => 'aktif']);
        $prodC = Product::create(['product_code' => 'P003', 'product_name' => 'Produk C', 'category' => 'Test', 'status' => 'aktif']);

        $endDate = Carbon::parse($period->end_date);

        // Produk A
        ProductAssessment::create([
            'period_id' => $period->period_id,
            'product_id' => $prodA->product_id,
            'initial_stock' => 100,
            'final_stock' => 80,
            'units_sold' => 887,
            'selling_price' => 50000,
            'cost_price' => 35000,
            'entry_date' => $endDate->copy()->subDays(60)->format('Y-m-d'),
            'data_status' => 'layak'
        ]);

        // Produk B
        ProductAssessment::create([
            'period_id' => $period->period_id,
            'product_id' => $prodB->product_id,
            'initial_stock' => 100,
            'final_stock' => 50,
            'units_sold' => 739,
            'selling_price' => 75000,
            'cost_price' => 56250,
            'entry_date' => $endDate->copy()->subDays(30)->format('Y-m-d'),
            'data_status' => 'layak'
        ]);

        // Produk C
        ProductAssessment::create([
            'period_id' => $period->period_id,
            'product_id' => $prodC->product_id,
            'initial_stock' => 100,
            'final_stock' => 90,
            'units_sold' => 843,
            'selling_price' => 100000,
            'cost_price' => 60000,
            'entry_date' => $endDate->copy()->subDays(90)->format('Y-m-d'),
            'data_status' => 'layak'
        ]);

        $service = new SawService();
        $result = $service->calculateAndSave($period->period_id);

        $this->assertEquals(3, $result['count']);

        $rankings = RankingResult::where('period_id', $period->period_id)->orderBy('rank')->get();

        // 1st Rank = Produk C
        $this->assertEquals($prodC->product_id, $rankings[0]->assessment_id);
        // 2nd Rank = Produk A
        $this->assertEquals($prodA->product_id, $rankings[1]->assessment_id);
        // 3rd Rank = Produk B
        $this->assertEquals($prodB->product_id, $rankings[2]->assessment_id);

        // Check Preference values with small delta due to C3 integer approximation
        $this->assertEqualsWithDelta(0.9585, $rankings[0]->preference_value, 0.05);
        $this->assertEqualsWithDelta(0.7577, $rankings[1]->preference_value, 0.05);
        $this->assertEqualsWithDelta(0.4668, $rankings[2]->preference_value, 0.05);
    }
}
