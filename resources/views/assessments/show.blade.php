<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Detail Penilaian Produk') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Breadcrumb / Navigasi -->
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('assessments.index', ['period_id' => $assessment->period_id]) }}"
               class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Kembali ke Daftar Penilaian
            </a>
        </div>

        @php
            $margin = $assessment->selling_price > 0
                ? round((($assessment->selling_price - $assessment->cost_price) / $assessment->selling_price) * 100, 2)
                : 0;
            $isLayak = $assessment->data_status == 'layak';
            $stockRatio = $assessment->initial_stock > 0
                ? round(($assessment->final_stock / $assessment->initial_stock) * 100, 0)
                : 0;
            $entryDate = \Carbon\Carbon::parse($assessment->entry_date);
            $endDate = \Carbon\Carbon::parse($assessment->period->end_date);
            $observationDays = $entryDate->diffInDays($endDate);
        @endphp

        <!-- Header Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ optional($assessment->product)->product_name }}</h3>
                        <p class="text-sm font-medium text-slate-500">{{ optional($assessment->product)->product_code }}</p>
                    </div>
                </div>
                <div>
                    @if($isLayak)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            LAYAK
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                            Belum Memadai
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informasi Periode -->
            <div class="px-6 py-3 bg-slate-50/50 border-b border-slate-100 flex flex-wrap items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-slate-600">Periode:</span>
                    <span class="font-bold text-slate-800">{{ $assessment->period->period_name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-slate-600">Lama Observasi:</span>
                    <span class="font-bold text-slate-800">{{ $observationDays }} hari</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-medium text-slate-600">Status Data:</span>
                    @if($isLayak)
                        <span class="font-bold text-emerald-600">✓ Layak (≥ 60 hari)</span>
                    @else
                        <span class="font-bold text-amber-600">⚠ Belum Memadai (&lt; 60 hari)</span>
                    @endif
                </div>
            </div>

            <!-- Detail Data -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom Kiri -->
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider">Nama Produk</label>
                            <p class="text-base font-bold text-slate-800">{{ optional($assessment->product)->product_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider">Kode Produk</label>
                            <p class="text-base font-mono font-bold text-slate-800">{{ optional($assessment->product)->product_code }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider">Kategori</label>
                            <p class="text-base font-medium text-slate-800">{{ optional($assessment->product)->category ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tanggal Masuk</label>
                            <p class="text-base font-medium text-slate-800">{{ \Carbon\Carbon::parse($assessment->entry_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Awal</label>
                                <p class="text-xl font-extrabold text-slate-800">{{ $assessment->initial_stock }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Akhir</label>
                                <p class="text-xl font-extrabold {{ $stockRatio > 70 ? 'text-rose-600' : ($stockRatio > 40 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $assessment->final_stock }}
                                </p>
                                <div class="w-full h-1 rounded-full bg-slate-200 overflow-hidden mt-1">
                                    <div class="h-full rounded-full {{ $stockRatio > 70 ? 'bg-rose-400' : ($stockRatio > 40 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                         style="width: {{ min($stockRatio, 100) }}%"></div>
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $stockRatio }}% tersisa</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terjual</label>
                                <p class="text-xl font-extrabold text-indigo-600">{{ $assessment->units_sold }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Jual</label>
                                <p class="text-lg font-bold text-slate-800">Rp{{ number_format($assessment->selling_price, 0) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Pokok</label>
                                <p class="text-lg font-bold text-slate-800">Rp{{ number_format($assessment->cost_price, 0) }}</p>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-3 text-center">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Margin Keuntungan</label>
                            <p class="text-2xl font-extrabold
                                {{ $margin >= 50 ? 'text-emerald-600' : ($margin >= 25 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $margin }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('assessments.index', ['period_id' => $assessment->period_id]) }}"
                   class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                    Kembali
                </a>
                <a href="{{ route('assessments.edit', $assessment->assessment_id) }}"
                   class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
