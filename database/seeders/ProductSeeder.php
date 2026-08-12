<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\AssessmentPeriod;
use App\Models\ProductAssessment;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $period = AssessmentPeriod::firstOrCreate(
            ['period_name' => 'Periode 2025-2026'],
            [
                'start_date' => '2025-11-01',
                'end_date' => '2026-12-31',
                'status' => 'aktif'
            ]
        );

        $rawData = [
            ['name' => 'Doll Fan Made', 'category' => 'Doll', 'sell' => 130000, 'cost' => 70000, 'initial' => 40, 'final' => 38, 'sold' => 2, 'date' => '2026-07-03'],
            ['name' => 'Album', 'category' => 'Album', 'sell' => 100000, 'cost' => 50000, 'initial' => 30, 'final' => 4, 'sold' => 26, 'date' => '2026-05-15'],
            ['name' => 'PhotoCard Mark', 'category' => 'Photocard', 'sell' => 50000, 'cost' => 20000, 'initial' => 30, 'final' => 20, 'sold' => 10, 'date' => '2026-03-20'],
            ['name' => 'PC Girl Group', 'category' => 'Photocard', 'sell' => 70000, 'cost' => 30000, 'initial' => 80, 'final' => 55, 'sold' => 25, 'date' => '2026-02-05'],
            ['name' => 'PC WAYV', 'category' => 'Photocard', 'sell' => 40000, 'cost' => 15000, 'initial' => 2, 'final' => 2, 'sold' => 0, 'date' => '2026-07-03'],
            ['name' => 'PC Riize', 'category' => 'Photocard', 'sell' => 70000, 'cost' => 30000, 'initial' => 3, 'final' => 3, 'sold' => 0, 'date' => '2026-07-03'],
            ['name' => 'PC NCT 127', 'category' => 'Photocard', 'sell' => 50000, 'cost' => 20000, 'initial' => 35, 'final' => 26, 'sold' => 9, 'date' => '2026-03-20'],
            ['name' => 'PC NCT Wish', 'category' => 'Photocard', 'sell' => 60000, 'cost' => 30000, 'initial' => 100, 'final' => 60, 'sold' => 40, 'date' => '2026-03-15'],
            ['name' => 'PC NCT Dream', 'category' => 'Photocard', 'sell' => 50000, 'cost' => 20000, 'initial' => 120, 'final' => 70, 'sold' => 50, 'date' => '2026-03-15'],
            ['name' => 'PC Boy Group', 'category' => 'Photocard', 'sell' => 30000, 'cost' => 15000, 'initial' => 24, 'final' => 21, 'sold' => 3, 'date' => '2025-11-27'],
            ['name' => 'Merchandise', 'category' => 'Merchandise', 'sell' => 200000, 'cost' => 100000, 'initial' => 8, 'final' => 6, 'sold' => 2, 'date' => '2026-03-15'],
            ['name' => 'Keychain Wings', 'category' => 'Merchandise', 'sell' => 40000, 'cost' => 15000, 'initial' => 20, 'final' => 3, 'sold' => 17, 'date' => '2026-03-15'],
            ['name' => 'PC Xnghaen & Xoul', 'category' => 'Photocard', 'sell' => 60000, 'cost' => 20000, 'initial' => 25, 'final' => 6, 'sold' => 19, 'date' => '2026-02-05'],
            ['name' => 'Mission Card', 'category' => 'Merchandise', 'sell' => 20000, 'cost' => 5000, 'initial' => 24, 'final' => 7, 'sold' => 17, 'date' => '2025-11-27'],
            ['name' => 'NCT Zone', 'category' => 'Merchandise', 'sell' => 25000, 'cost' => 3000, 'initial' => 24, 'final' => 5, 'sold' => 19, 'date' => '2026-02-05'],
        ];

        foreach ($rawData as $i => $data) {
            $productCode = 'PRD-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            $product = Product::firstOrCreate(
                ['product_code' => $productCode],
                [
                    'product_name' => $data['name'],
                    'category' => $data['category'],
                    'status' => 'aktif'
                ]
            );

            ProductAssessment::updateOrCreate(
                [
                    'product_id' => $product->product_id,
                    'period_id' => $period->period_id,
                ],
                [
                    'initial_stock' => $data['initial'],
                    'final_stock' => $data['final'],
                    'units_sold' => $data['sold'],
                    'selling_price' => $data['sell'],
                    'cost_price' => $data['cost'],
                    'entry_date' => $data['date'],
                    'data_status' => 'layak',
                ]
            );
        }
    }
}
