<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['product_code' => 'PRD-001', 'product_name' => 'Ichigo Mochi Strawberry', 'category' => 'Makanan Ringan', 'status' => 'aktif'],
            ['product_code' => 'PRD-002', 'product_name' => 'Dorayaki Chocolate Lava', 'category' => 'Makanan Ringan', 'status' => 'aktif'],
            ['product_code' => 'PRD-003', 'product_name' => 'Matcha Latte Bottle 250ml', 'category' => 'Minuman', 'status' => 'aktif'],
            ['product_code' => 'PRD-004', 'product_name' => 'Takoyaki Deluxe Set 8pcs', 'category' => 'Makanan Berat', 'status' => 'aktif'],
            ['product_code' => 'PRD-005', 'product_name' => 'Ramen Tonkotsu Instant', 'category' => 'Makanan Berat', 'status' => 'aktif'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['product_code' => $p['product_code']], $p);
        }
    }
}
