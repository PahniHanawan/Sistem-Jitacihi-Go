<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ HANYA 4 KRITERIA (C1-C4)
        $criteria = [
            [
                'criterion_code' => 'C1',
                'criterion_name' => 'Harga Produk',
                'type' => 'cost',
                'status' => 'aktif',
            ],
            [
                'criterion_code' => 'C2',
                'criterion_name' => 'Kecepatan Perputaran',
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
                'criterion_name' => 'Lama Penyimpanan Stok',
                'type' => 'benefit',
                'status' => 'aktif',
            ],
        ];

        foreach ($criteria as $item) {
            Criterion::updateOrCreate(
                ['criterion_code' => $item['criterion_code']],
                $item
            );
        }

        $this->command->info('✅ 4 Kriteria berhasil di-seed (C1-C4)');
    }
}
