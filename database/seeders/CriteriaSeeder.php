<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'criterion_code' => 'C1',
                'criterion_name' => 'Jumlah Terjual',
                'type' => 'benefit',
                'status' => 'aktif',
            ],
            [
                'criterion_code' => 'C2',
                'criterion_name' => 'Sisa Stok',
                'type' => 'cost',
                'status' => 'aktif',
            ],
            [
                'criterion_code' => 'C3',
                'criterion_name' => 'Margin Keuntungan',
                'type' => 'benefit',
                'status' => 'aktif',
            ],
            [
                'criterion_code' => 'C4',
                'criterion_name' => 'Persentase Penjualan',
                'type' => 'benefit',
                'status' => 'aktif',
            ],
        ];

        foreach ($criteria as $item) {
            Criterion::firstOrCreate(['criterion_code' => $item['criterion_code']], $item);
        }
    }
}
