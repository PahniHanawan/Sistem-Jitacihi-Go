<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Edit Penilaian Produk') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Breadcrumb / Navigasi -->
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('assessments.index', ['period_id' => $assessment->period_id]) }}"
               class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Kembali ke Daftar Penilaian
            </a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('assessments.show', $assessment->assessment_id) }}"
               class="text-indigo-600 hover:text-indigo-800 font-medium">
                Lihat Detail
            </a>
        </div>

        @php
            $isLayak = $assessment->data_status == 'layak';
        @endphp

        <!-- Form Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Edit Penilaian</h3>
                        <p class="text-sm font-medium text-slate-500">
                            Produk: <strong class="text-slate-700">{{ optional($assessment->product)->product_name }}</strong>
                            ({{ optional($assessment->product)->product_code }})
                        </p>
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

            <form action="{{ route('assessments.update', $assessment->assessment_id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <!-- Periode (Readonly) -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Periode Penilaian</label>
                    <input type="text" value="{{ $assessment->period->period_name }} ({{ $assessment->period->start_date }} - {{ $assessment->period->end_date }})"
                           disabled
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm">
                    <p class="text-xs text-slate-400 mt-1">Periode tidak dapat diubah.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Produk (Readonly, hanya tampil) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Produk</label>
                        <input type="text" value="{{ optional($assessment->product)->product_name }} ({{ optional($assessment->product)->product_code }})"
                               disabled
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm">
                        <input type="hidden" name="product_id" value="{{ $assessment->product_id }}">
                        <p class="text-xs text-slate-400 mt-1">Produk tidak dapat diubah.</p>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Masuk</label>
                        <input type="date" name="entry_date" value="{{ $assessment->entry_date }}"
                               required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('entry_date') border-rose-500 @enderror">
                        @error('entry_date')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Awal -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Stok Awal</label>
                        <input type="number" name="initial_stock" min="0" value="{{ old('initial_stock', $assessment->initial_stock) }}"
                               required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('initial_stock') border-rose-500 @enderror">
                        @error('initial_stock')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Akhir -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Stok Akhir</label>
                        <input type="number" name="final_stock" min="0" value="{{ old('final_stock', $assessment->final_stock) }}"
                               required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('final_stock') border-rose-500 @enderror">
                        @error('final_stock')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Terjual -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Terjual</label>
                        <input type="number" name="units_sold" min="0" value="{{ old('units_sold', $assessment->units_sold) }}"
                               required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('units_sold') border-rose-500 @enderror">
                        @error('units_sold')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold">Rp</span>
                            <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $assessment->selling_price) }}"
                                   placeholder="25000" required class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('selling_price') border-rose-500 @enderror">
                        </div>
                        @error('selling_price')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Pokok -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Harga Pokok</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold">Rp</span>
                            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $assessment->cost_price) }}"
                                   placeholder="15000" required class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('cost_price') border-rose-500 @enderror">
                        </div>
                        @error('cost_price')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status Data (Info) -->
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-slate-600">Status data akan dihitung otomatis oleh sistem berdasarkan:</span>
                    </div>
                    <div class="mt-2 text-xs text-slate-500 pl-8 space-y-1">
                        <div>✓ Stok awal &gt; 0</div>
                        <div>✓ Unit terjual ≥ 0</div>
                        <div>✓ Usia observasi ≥ 60 hari (tanggal masuk vs tanggal akhir periode)</div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('assessments.index', ['period_id' => $assessment->period_id]) }}"
                       class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Data
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
