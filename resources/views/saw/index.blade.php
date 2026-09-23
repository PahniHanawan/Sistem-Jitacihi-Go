<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Pemeringkatan SAW') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Pilih Periode & Aksi -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Ranking Prioritas Promosi</h3>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">Metode Simple Additive Weighting (SAW)</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <form method="GET" action="{{ route('saw.index') }}" class="flex-1 sm:w-64">
                    <select name="period_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-bold text-slate-700 transition-all bg-slate-50">
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ strtoupper($p->status) }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($activePeriod)
                <form method="POST" action="{{ route('saw.store') }}">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $activePeriod->period_id }}">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Hitung SAW
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if($activePeriod)
            @if(count($ahpResults) == 0)
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-start gap-3 shadow-sm">
                    <svg class="w-6 h-6 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <div>
                        <strong class="font-bold">Peringatan Syarat AHP Belum Terpenuhi:</strong>
                        <p class="text-sm font-medium mt-0.5">Bobot AHP belum dihitung atau tidak konsisten pada periode ini. Silakan atur bobot AHP terlebih dahulu di menu <strong>AHP (Bobot)</strong> sebelum memproses SAW.</p>
                    </div>
                </div>
            @else
                <!-- Hasil Pemeringkatan SAW -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Hasil Pemeringkatan Nilai Preferensi (Vi)</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Produk dengan nilai tertinggi direkomendasikan sebagai prioritas promosi atau diskon.
                        </p>
                    </div>

                    <div class="p-6">
                        @if($rankings->isEmpty())
                            <div class="flex flex-col items-center justify-center py-10 px-4 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Belum Ada Hasil Pemeringkatan</h4>
                                <p class="text-xs font-medium text-slate-400 mt-1 max-w-[300px]">Klik tombol "Hitung SAW" di atas untuk menjalankan kalkulasi normalisasi dan pemeringkatan produk.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 rounded-tl-2xl w-24">Peringkat</th>
                                            <th scope="col" class="px-6 py-4">Kode Produk</th>
                                            <th scope="col" class="px-6 py-4">Nama Produk</th>
                                            <th scope="col" class="px-6 py-4 text-right">Nilai (Vi)</th>
                                            <th scope="col" class="px-6 py-4 text-center rounded-tr-2xl">Keputusan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach($rankings as $rank)
                                            <tr class="hover:bg-slate-50/80 transition-colors {{ $rank->rank == 1 ? 'bg-amber-50/30' : '' }}">
                                                <td class="px-6 py-4">
                                                    @if($rank->rank == 1)
                                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gradient-to-tr from-amber-400 to-amber-200 text-amber-900 font-extrabold shadow-sm ring-4 ring-amber-50">
                                                            1
                                                        </div>
                                                    @elseif($rank->rank == 2)
                                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-slate-300 to-slate-100 text-slate-700 font-bold shadow-sm ring-4 ring-slate-50 ml-0.5">
                                                            2
                                                        </div>
                                                    @elseif($rank->rank == 3)
                                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-orange-400 to-orange-200 text-orange-900 font-bold shadow-sm ring-4 ring-orange-50 ml-0.5">
                                                            3
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold ml-0.5">
                                                            {{ $rank->rank }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs font-bold tracking-wide">
                                                        {{ optional($rank->productAssessment->product)->product_code }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 font-bold text-slate-800">
                                                    {{ optional($rank->productAssessment->product)->product_name }}
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full {{ $rank->rank <= 3 ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-slate-50 text-slate-700 border-slate-200' }} font-bold text-sm tracking-wide border">
                                                        {{ number_format($rank->preference_value, 4) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if(optional($rank->promotionDecision)->discount_type)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200 shadow-sm">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                            {{ $rank->promotionDecision->discount_type }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center text-slate-400 font-medium text-xs italic">
                                                            <svg class="w-4 h-4 mr-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                            Menunggu
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- DETAIL NORMALISASI SAW (DITAMBAHKAN)                         -->
                <!-- ============================================================ -->
                @if($normalizations->isNotEmpty() && $rankings->isNotEmpty())
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 cursor-pointer flex justify-between items-center" 
                         onclick="document.getElementById('sawDetail').classList.toggle('hidden')">
                        <div>
                            <h3 class="text-md font-bold text-slate-800">📐 Detail Normalisasi SAW (Rij)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Klik untuk toggle detail</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    
                    <div id="sawDetail" class="p-6 hidden">
                        <!-- Rumus Normalisasi -->
                        <div class="mb-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                            <h4 class="font-bold text-sm text-indigo-800 mb-2">📝 Rumus Normalisasi</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="p-3 bg-white rounded-lg border border-indigo-200">
                                    <span class="font-bold text-indigo-700">Benefit (C2, C4, C5):</span>
                                    <span class="font-mono ml-2">r_ij = x_ij / max(x_ij)</span>
                                </div>
                                <div class="p-3 bg-white rounded-lg border border-indigo-200">
                                    <span class="font-bold text-indigo-700">Cost (C1, C3):</span>
                                    <span class="font-mono ml-2">r_ij = min(x_ij) / x_ij</span>
                                </div>
                            </div>
                            <div class="mt-3 p-2 bg-yellow-50 rounded-lg border border-yellow-200 text-xs text-yellow-800">
                                <span class="font-bold">⚠️ Catatan:</span> Untuk kriteria C3 (Kecepatan Perputaran), jika x = 0 maka r = 1 (nilai maksimum).
                            </div>
                        </div>

                        <!-- Tabel Normalisasi -->
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full text-left text-xs whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-500 font-bold">
                                    <tr>
                                        <th class="px-3 py-2">Produk</th>
                                        @foreach($criteria as $crit)
                                            <th class="px-3 py-2 text-center">
                                                {{ $crit->criterion_code }}
                                                <span class="block text-[10px] font-normal text-slate-400">({{ $crit->type }})</span>
                                            </th>
                                        @endforeach
                                        <th class="px-3 py-2 text-center font-bold text-indigo-600">Vi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($rankings as $rank)
                                        @php
                                            $astId = $rank->assessment_id;
                                            $norms = $normalizations->get($astId) ?? collect();
                                            $criteriaIds = $criteria->pluck('criterion_id')->toArray();
                                        @endphp
                                        <tr class="{{ $rank->rank == 1 ? 'bg-amber-50/50' : '' }}">
                                            <td class="px-3 py-2 font-medium text-slate-700">
                                                {{ optional($rank->productAssessment->product)->product_code }}
                                            </td>
                                            @foreach($criteriaIds as $cid)
                                                @php
                                                    $norm = $norms->firstWhere('criterion_id', $cid);
                                                    $val = $norm ? $norm->normalized_value : '-';
                                                    $raw = $norm ? $norm->raw_value : '-';
                                                @endphp
                                                <td class="px-3 py-2 text-center font-mono">
                                                    @if($val !== '-')
                                                        <span class="block font-bold">{{ number_format($val, 4) }}</span>
                                                        <span class="text-[9px] text-slate-400 block">({{ number_format($raw, 2) }})</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="px-3 py-2 text-center font-mono font-bold text-indigo-600">
                                                {{ number_format($rank->preference_value, 4) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Contoh Perhitungan -->
                        @if($rankings->isNotEmpty())
                        @php
                            $top = $rankings->first();
                            $astId = $top->assessment_id;
                            $norms = $normalizations->get($astId) ?? collect();
                        @endphp
                        <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-sm text-slate-700 mb-2">🔢 Contoh Perhitungan (Ranking #1)</h4>
                            <p class="text-xs text-slate-500 mb-2">Produk: <strong>{{ optional($top->productAssessment->product)->product_name }}</strong></p>
                            <div class="text-xs font-mono space-y-1">
                                <div>Vi = Σ(w_j × r_ij)</div>
                                <div class="flex flex-wrap gap-1 items-center">
                                    @foreach($criteria as $crit)
                                        @php
                                            $w = $ahpResults->firstWhere('criterion_id', $crit->criterion_id)->weight ?? 0;
                                            $norm = $norms->firstWhere('criterion_id', $crit->criterion_id);
                                            $r = $norm ? $norm->normalized_value : 0;
                                            $contrib = $w * $r;
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 bg-white rounded border border-slate-200 text-[10px]">
                                            ({{ number_format($w, 4) }} × {{ number_format($r, 4) }}) = {{ number_format($contrib, 4) }}
                                        </span>
                                        @if(!$loop->last)
                                            <span class="text-slate-400 text-xs">+</span>
                                        @endif
                                    @endforeach
                                    <span class="text-slate-400 text-xs">=</span>
                                    <span class="font-bold text-indigo-600">{{ number_format($top->preference_value, 4) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!-- END DETAIL NORMALISASI SAW -->

            @endif
        @else
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Pilih Periode Penilaian</h3>
                <p class="text-slate-500 text-sm max-w-md">
                    Anda belum memilih periode aktif. Silakan buat atau pilih periode aktif terlebih dahulu di Master Periode.
                </p>
                <div class="mt-6">
                    <a href="{{ route('periods.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Kelola Periode</a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>