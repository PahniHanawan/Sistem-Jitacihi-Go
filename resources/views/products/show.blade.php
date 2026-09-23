<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Detail & Riwayat Produk') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Kembali ke Master Produk
            </a>
        </div>

        <!-- Kartu Identitas Produk -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800">{{ $product->product_name }}</h3>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[10px] font-bold tracking-wider">
                            {{ $product->product_code }}
                        </span>
                        <span class="text-sm font-medium text-slate-500">{{ $product->category }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($product->status == 'aktif')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs tracking-wide border border-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 font-bold text-xs tracking-wide border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-400 mr-2"></span> Nonaktif
                    </span>
                @endif
                <a href="{{ route('products.edit', $product->product_id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-amber-200">
                    Edit Produk
                </a>
            </div>
        </div>

        <!-- Tabel Riwayat Penilaian -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Riwayat Penilaian Produk</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3">Periode</th>
                            <th class="px-6 py-3 text-center">Stok Awal</th>
                            <th class="px-6 py-3 text-center">Terjual</th>
                            <th class="px-6 py-3 text-center">Sisa Stok</th>
                            <th class="px-6 py-3 text-right">Harga Jual</th>
                            <th class="px-6 py-3 text-center">Status Data</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($product->productAssessments->sortByDesc('period.start_date') as $ast)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3 font-bold text-slate-700">
                                    {{ optional($ast->period)->period_name ?? 'Periode Terhapus' }}
                                </td>
                                <td class="px-6 py-3 text-center font-medium">{{ $ast->initial_stock }}</td>
                                <td class="px-6 py-3 text-center font-bold text-indigo-600">{{ $ast->units_sold }}</td>
                                <td class="px-6 py-3 text-center font-medium">{{ $ast->final_stock }}</td>
                                <td class="px-6 py-3 text-right font-medium text-slate-600">
                                    Rp{{ number_format($ast->selling_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($ast->data_status == 'layak')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                            LAYAK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">
                                            TDK MEMADAI
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('assessments.show', $ast->assessment_id) }}" class="text-blue-500 hover:text-blue-700 font-bold text-xs" title="Lihat Laporan Lengkap">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-slate-400 font-medium text-sm">Belum ada riwayat penilaian untuk produk ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
