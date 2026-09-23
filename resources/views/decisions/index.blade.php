<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Keputusan Promosi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Pilih Periode -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Keputusan Promosi</h3>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">Pilih produk yang akan dipromosikan berdasarkan ranking SAW.</p>
                </div>
            </div>
            <form method="GET" action="{{ route('decisions.index') }}" class="flex items-center gap-2 shrink-0 w-full md:w-auto">
                <select name="period_id" onchange="this.form.submit()" class="w-full md:w-64 px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-bold text-slate-700 transition-all bg-slate-50">
                    @foreach($periods as $p)
                        <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                            {{ $p->period_name }} ({{ strtoupper($p->status) }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($activePeriod)
            @if($rankings->isEmpty())
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Ranking</h3>
                    <p class="text-slate-500 text-sm max-w-md">
                        Silakan jalankan perhitungan SAW terlebih dahulu.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('saw.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Menuju SAW</a>
                    </div>
                </div>
            @else
                <!-- Tabel Produk -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Daftar Produk Berdasarkan Ranking</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Pilih produk yang akan dipromosikan</p>
                    </div>

                    <div class="p-6">
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Rank</th>
                                        <th class="px-4 py-3">Kode</th>
                                        <th class="px-4 py-3">Nama Produk</th>
                                        <th class="px-4 py-3 text-center">Nilai (Vi)</th>
                                        <th class="px-4 py-3 text-center">Stok Tersisa</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($rankings as $rank)
                                        @php
                                            $product = $rank->productAssessment->product;
                                            $assessment = $rank->productAssessment;

                                            $entryDate = \Carbon\Carbon::parse($assessment->entry_date);
                                            $endDate = \Carbon\Carbon::parse($activePeriod->end_date);
                                            $lamaSimpanBulan = $entryDate->diffInDays($endDate) / 30.44;

                                            $rasioStok = $assessment->initial_stock > 0 
                                                ? ($assessment->final_stock / $assessment->initial_stock) * 100 
                                                : 0;

                                            $rankColors = [
                                                1 => 'bg-amber-100 text-amber-800 border-amber-300',
                                                2 => 'bg-slate-100 text-slate-700 border-slate-300',
                                                3 => 'bg-orange-100 text-orange-800 border-orange-300',
                                            ];
                                            $rankColor = $rankColors[$rank->rank] ?? 'bg-white text-slate-600';
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition-colors {{ $rank->rank <= 3 ? 'bg-amber-50/30' : '' }}">
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $rankColor }} font-bold text-sm border">
                                                    {{ $rank->rank }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs font-bold">
                                                    {{ $product->product_code }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-bold text-slate-800">{{ $product->product_name }}</td>
                                            <td class="px-4 py-3 text-center font-mono font-bold text-indigo-600">{{ number_format($rank->preference_value, 4) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="font-medium text-slate-700">{{ $assessment->final_stock }}</span>
                                                <span class="text-[10px] text-slate-400 block">({{ number_format($rasioStok, 1) }}%)</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if(optional($rank->promotionDecision)->discount_type)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                                        ✅ {{ $rank->promotionDecision->discount_type }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center text-slate-400 text-xs font-medium">
                                                        ⏳ Menunggu
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button onclick="document.getElementById('form-{{ $rank->ranking_id }}').classList.toggle('hidden')" 
                                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all text-xs">
                                                    {{ optional($rank->promotionDecision)->discount_type ? 'Ubah' : 'Pilih' }}
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Form Keputusan (tersembunyi) -->
                                        <tr id="form-{{ $rank->ranking_id }}" class="hidden bg-slate-50/80">
                                            <td colspan="7" class="px-4 py-3">
                                                <form action="{{ route('decisions.store') }}" method="POST" class="flex flex-wrap items-center gap-3">
                                                    @csrf
                                                    <input type="hidden" name="ranking_id" value="{{ $rank->ranking_id }}">

                                                    <span class="text-sm font-bold text-slate-700 min-w-[100px]">{{ $product->product_name }}</span>

                                                    <select name="discount_type" class="px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition-all bg-white shadow-sm">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Diskon 10%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 10%' ? 'selected' : '' }}>Diskon 10%</option>
                                                        <option value="Diskon 20%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 20%' ? 'selected' : '' }}>Diskon 20%</option>
                                                        <option value="Diskon 30%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 30%' ? 'selected' : '' }}>Diskon 30%</option>
                                                        <option value="Diskon 50%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 50%' ? 'selected' : '' }}>Diskon 50%</option>
                                                        <option value="Buy 1 Get 1" {{ optional($rank->promotionDecision)->discount_type == 'Buy 1 Get 1' ? 'selected' : '' }}>Buy 1 Get 1</option>
                                                        <option value="Tidak Ada Promosi" {{ optional($rank->promotionDecision)->discount_type == 'Tidak Ada Promosi' ? 'selected' : '' }}>❌ Tidak</option>
                                                    </select>

                                                    <input type="text" name="reason" value="{{ optional($rank->promotionDecision)->reason }}" placeholder="Alasan (opsional)" class="flex-1 min-w-[150px] px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all shadow-sm">

                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all text-sm">
                                                        💾 Simpan
                                                    </button>

                                                    <button type="button" onclick="document.getElementById('form-{{ $rank->ranking_id }}').classList.add('hidden')" 
                                                            class="px-3 py-2 text-slate-400 hover:text-slate-600 transition-colors">
                                                        ✕
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Info tambahan -->
                        <div class="mt-4 p-3 bg-indigo-50 rounded-xl border border-indigo-100 text-xs text-indigo-700 flex items-start gap-2">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>
                                <strong>📌 Catatan:</strong> 
                                Produk dengan ranking tertinggi adalah yang paling direkomendasikan untuk promosi. 
                                Status <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold text-[10px]">✅ Diskon</span> berarti keputusan sudah dibuat.
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Pilih Periode Penilaian</h3>
                <p class="text-slate-500 text-sm max-w-md">
                    Anda belum memilih periode aktif.
                </p>
            </div>
        @endif

    </div>
</x-app-layout>