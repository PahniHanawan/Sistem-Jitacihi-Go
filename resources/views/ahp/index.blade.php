<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Perhitungan AHP') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Pilih Periode -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Evaluasi Matriks AHP</h3>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">Pilih periode penilaian untuk melakukan perbandingan berpasangan.</p>
                </div>
            </div>
            <form method="GET" action="{{ route('ahp.index') }}" class="flex items-center space-x-2 shrink-0 w-full md:w-auto">
                <select name="period_id" onchange="this.form.submit()" class="w-full md:w-64 px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm font-bold text-slate-700 transition-all bg-slate-50">
                    @foreach($periods as $p)
                        <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                            {{ $p->period_name }} ({{ strtoupper($p->status) }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($activePeriod && count($criteria) >= 2)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Form Pairwise Matrix -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Matriks Perbandingan Berpasangan</h3>
                    <p class="text-xs font-medium text-slate-500 mt-1">
                        Gunakan Skala Saaty (1-9) untuk membandingkan prioritas antar kriteria.
                        <span class="block text-violet-600 font-bold mt-1">✅ 4 Kriteria (C1-C4) → 6 pasang perbandingan</span>
                    </p>
                </div>

                <div class="p-6">
                    <form action="{{ route('ahp.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $activePeriod->period_id }}">

                        <div class="flex justify-end mb-4">
                            <button type="button" onclick="fillThesisData()" class="text-xs font-bold px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition-colors flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                Isi Otomatis Matriks Skripsi (4 Kriteria)
                            </button>
                        </div>

                        <!-- ✅ 6 PASANG PERBANDINGAN (4 KRITERIA) -->
                        <div class="space-y-4">
                            @php
                                $pairs = [
                                    [0, 1, 'C1', 'C2'],
                                    [0, 2, 'C1', 'C3'],
                                    [0, 3, 'C1', 'C4'],
                                    [1, 2, 'C2', 'C3'],
                                    [1, 3, 'C2', 'C4'],
                                    [2, 3, 'C3', 'C4'],
                                ];
                            @endphp

                            @foreach($pairs as $pair)
                                @php
                                    $cA = $criteria[$pair[0]];
                                    $cB = $criteria[$pair[1]];
                                    $key = "{$cA->criterion_id}_{$cB->criterion_id}";
                                    $currentVal = $existingComparisons[$key] ?? 1;
                                @endphp
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-violet-200 transition-colors">
                                    <div class="flex justify-between items-center text-sm font-bold mb-3">
                                        <span class="text-slate-800 px-3 py-1 bg-white rounded-lg border border-slate-200 shadow-sm">
                                            {{ $cA->criterion_name }} <span class="text-violet-600 ml-1">[{{ $cA->criterion_code }}]</span>
                                        </span>
                                        <span class="text-slate-400 text-xs px-2">VS</span>
                                        <span class="text-slate-800 px-3 py-1 bg-white rounded-lg border border-slate-200 shadow-sm">
                                            {{ $cB->criterion_name }} <span class="text-indigo-600 ml-1">[{{ $cB->criterion_code }}]</span>
                                        </span>
                                    </div>
                                    <select name="comparisons[{{ $key }}]" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm font-medium text-slate-700 transition-all bg-white cursor-pointer">
                                        <option value="1" {{ $currentVal == 1 ? 'selected' : '' }}>1 - Sama Penting</option>
                                        <option value="2" {{ $currentVal == 2 ? 'selected' : '' }}>2 - Berdekatan Sedikit Lebih Penting</option>
                                        <option value="3" {{ $currentVal == 3 ? 'selected' : '' }}>3 - Sedikit Lebih Penting</option>
                                        <option value="4" {{ $currentVal == 4 ? 'selected' : '' }}>4 - Berdekatan Lebih Penting</option>
                                        <option value="5" {{ $currentVal == 5 ? 'selected' : '' }}>5 - Lebih Penting</option>
                                        <option value="6" {{ $currentVal == 6 ? 'selected' : '' }}>6 - Berdekatan Sangat Penting</option>
                                        <option value="7" {{ $currentVal == 7 ? 'selected' : '' }}>7 - Sangat Penting</option>
                                        <option value="8" {{ $currentVal == 8 ? 'selected' : '' }}>8 - Berdekatan Mutlak Lebih Penting</option>
                                        <option value="9" {{ $currentVal == 9 ? 'selected' : '' }}>9 - Mutlak Lebih Penting</option>
                                        <option value="0.5" {{ abs($currentVal - 0.5) < 0.01 ? 'selected' : '' }}>1/2 - Kebalikan (2)</option>
                                        <option value="0.3333" {{ abs($currentVal - 0.3333) < 0.01 ? 'selected' : '' }}>1/3 - Kebalikan (3)</option>
                                        <option value="0.25" {{ abs($currentVal - 0.25) < 0.01 ? 'selected' : '' }}>1/4 - Kebalikan (4)</option>
                                        <option value="0.2" {{ abs($currentVal - 0.2) < 0.01 ? 'selected' : '' }}>1/5 - Kebalikan (5)</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="w-full mt-4 py-3.5 px-4 bg-violet-600 hover:bg-violet-700 active:bg-violet-800 text-white font-bold rounded-xl shadow-lg shadow-violet-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            Hitung Bobot AHP & Cek Konsistensi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hasil Bobot & Konsistensi -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-fit sticky top-6">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Hasil Bobot Prioritas & Konsistensi</h3>
                    <p class="text-xs font-medium text-slate-500 mt-1">
                        Syarat matriks dikatakan konsisten adalah jika nilai CR <= 0.10.
                        <span class="block text-violet-600 font-bold mt-1">✅ RI = 0.90 (untuk n=4)</span>
                    </p>
                </div>

                <div class="p-6">
                    @if($consistencyData)
                        <div class="p-5 rounded-2xl mb-6 {{ $consistencyData['is_valid'] ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }}">
                            <div class="flex items-center justify-between mb-4">
                                <span class="font-bold text-sm {{ $consistencyData['is_valid'] ? 'text-emerald-800' : 'text-rose-800' }}">Status Konsistensi (CR)</span>
                                @if($consistencyData['is_valid'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm shadow-emerald-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                        KONSISTEN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-rose-600 text-white shadow-sm shadow-rose-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                        TIDAK KONSISTEN
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="bg-white/60 backdrop-blur p-3 rounded-xl border {{ $consistencyData['is_valid'] ? 'border-emerald-200' : 'border-rose-200' }}">
                                    <span class="block text-xs font-bold text-slate-500 mb-1">λ max</span>
                                    <span class="font-extrabold text-slate-800 text-lg">{{ number_format($consistencyData['lambda_max'], 4) }}</span>
                                </div>
                                <div class="bg-white/60 backdrop-blur p-3 rounded-xl border {{ $consistencyData['is_valid'] ? 'border-emerald-200' : 'border-rose-200' }}">
                                    <span class="block text-xs font-bold text-slate-500 mb-1">CI</span>
                                    <span class="font-extrabold text-slate-800 text-lg">{{ number_format($consistencyData['ci'], 4) }}</span>
                                </div>
                                <div class="bg-white/60 backdrop-blur p-3 rounded-xl border {{ $consistencyData['is_valid'] ? 'border-emerald-200' : 'border-rose-200' }}">
                                    <span class="block text-xs font-bold text-slate-500 mb-1">CR</span>
                                    <span class="font-extrabold text-lg {{ $consistencyData['is_valid'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($consistencyData['cr'], 4) }}</span>
                                </div>
                            </div>

                            @if(!$consistencyData['is_valid'])
                                <div class="mt-4 text-xs font-medium text-rose-700 bg-white/50 p-3 rounded-xl">
                                    <strong>Peringatan:</strong> Nilai CR melebihi batas yang diizinkan (0.10). Harap tinjau kembali perbandingan berpasangan Anda agar hasilnya lebih konsisten.
                                </div>
                            @endif
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 rounded-tl-2xl">Kriteria</th>
                                        <th scope="col" class="px-5 py-3 text-right rounded-tr-2xl">Bobot Prioritas (W)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($ahpResults as $res)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-mono text-xs font-bold">{{ optional($res->criterion)->criterion_code }}</span>
                                                    <span class="font-bold text-slate-800">{{ optional($res->criterion)->criterion_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-50 text-slate-700 font-mono font-bold text-sm tracking-wide border border-slate-200">
                                                    {{ number_format($res->weight, 4) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ============================================================ -->
                        <!-- DETAIL PERHITUNGAN AHP (4 KRITERIA)                          -->
                        <!-- ============================================================ -->
                        @if($normalizedMatrix && $consistencyVector)
                        <div class="mt-6 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 cursor-pointer flex justify-between items-center"
                                 onclick="document.getElementById('ahpDetail').classList.toggle('hidden')">
                                <div>
                                    <h3 class="text-md font-bold text-slate-800">📐 Detail Perhitungan AHP (4 Kriteria)</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Klik untuk toggle detail</p>
                                </div>
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <div id="ahpDetail" class="p-6 hidden">
                                <!-- 1. Matriks Normalisasi (4x4) -->
                                <div class="mb-6">
                                    <h4 class="font-bold text-sm text-slate-700 mb-3">1. Matriks Normalisasi (Rij) — 4×4</h4>
                                    <p class="text-xs text-slate-500 mb-2">Rumus: r_ij = a_ij / Σ a_ij (per kolom)</p>
                                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                                        <table class="w-full text-left text-xs whitespace-nowrap">
                                            <thead class="bg-slate-50 text-slate-500 font-bold">
                                                <tr>
                                                    <th class="px-3 py-2">Kriteria</th>
                                                    @foreach($criteria as $c)
                                                        <th class="px-3 py-2 text-center">{{ $c->criterion_code }}</th>
                                                    @endforeach
                                                    <th class="px-3 py-2 text-center">Bobot (W)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($criteria as $i => $c)
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium text-slate-700">{{ $c->criterion_code }}</td>
                                                        @foreach($criteria as $j => $c2)
                                                            @php
                                                                $val = $normalizedMatrix[$i][$j] ?? '-';
                                                            @endphp
                                                            <td class="px-3 py-2 text-center font-mono">{{ is_numeric($val) ? number_format($val, 4) : $val }}</td>
                                                        @endforeach
                                                        @php
                                                            $weight = $ahpResults->firstWhere('criterion_id', $c->criterion_id)?->weight ?? 0;
                                                        @endphp
                                                        <td class="px-3 py-2 text-center font-mono font-bold text-indigo-600">{{ number_format($weight, 4) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 2. Consistency Vector -->
                                <div class="mb-6">
                                    <h4 class="font-bold text-sm text-slate-700 mb-3">2. Consistency Vector (CVi)</h4>
                                    <p class="text-xs text-slate-500 mb-2">Rumus: CVi = (A × w)i / wi</p>
                                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                                        <table class="w-full text-left text-xs whitespace-nowrap">
                                            <thead class="bg-slate-50 text-slate-500 font-bold">
                                                <tr>
                                                    <th class="px-3 py-2">Kriteria</th>
                                                    <th class="px-3 py-2 text-center">(A × w)i</th>
                                                    <th class="px-3 py-2 text-center">wi</th>
                                                    <th class="px-3 py-2 text-center">CVi = (A×w)i / wi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($criteria as $c)
                                                    @php
                                                        $aw = $consistencyVector[$c->criterion_id]['aw'] ?? '-';
                                                        $w = $consistencyVector[$c->criterion_id]['w'] ?? 0;
                                                        $cv = $consistencyVector[$c->criterion_id]['cv'] ?? '-';
                                                    @endphp
                                                    <tr>
                                                        <td class="px-3 py-2 font-medium text-slate-700">{{ $c->criterion_code }}</td>
                                                        <td class="px-3 py-2 text-center font-mono">{{ is_numeric($aw) ? number_format($aw, 4) : $aw }}</td>
                                                        <td class="px-3 py-2 text-center font-mono">{{ is_numeric($w) ? number_format($w, 4) : $w }}</td>
                                                        <td class="px-3 py-2 text-center font-mono font-bold {{ is_numeric($cv) && $cv > 0 ? 'text-indigo-600' : '' }}">{{ is_numeric($cv) ? number_format($cv, 4) : $cv }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-slate-50">
                                                <tr>
                                                    <td colspan="3" class="px-3 py-2 text-right font-bold text-slate-700">λmax = Σ CVi / n</td>
                                                    <td class="px-3 py-2 text-center font-mono font-bold text-violet-600">{{ number_format($consistencyData['lambda_max'], 4) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- 3. Uji Konsistensi (n=4, RI=0.90) -->
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm text-slate-700 mb-3">3. Uji Konsistensi (n=4, RI=0.90)</h4>
                                    <div class="space-y-2 text-sm font-mono">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-bold text-slate-600">CI = (λmax - n) / (n - 1)</span>
                                            <span class="text-slate-400">=</span>
                                            <span>({{ number_format($consistencyData['lambda_max'], 4) }} - {{ count($criteria) }}) / {{ count($criteria) - 1 }}</span>
                                            <span class="text-slate-400">=</span>
                                            <span class="font-bold text-indigo-600">{{ number_format($consistencyData['ci'], 4) }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-bold text-slate-600">CR = CI / RI</span>
                                            <span class="text-slate-400">=</span>
                                            <span>{{ number_format($consistencyData['ci'], 4) }} / <span class="font-bold text-violet-600">0.90</span></span>
                                            <span class="text-slate-400">=</span>
                                            <span class="font-bold {{ $consistencyData['is_valid'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($consistencyData['cr'], 4) }}</span>
                                            <span class="text-slate-400 ml-2">
                                                {{ $consistencyData['is_valid'] ? '✅ ≤ 0.1 (KONSISTEN)' : '❌ > 0.1 (TIDAK KONSISTEN)' }}
                                            </span>
                                        </div>
                                        <div class="mt-2 p-2 bg-violet-50 rounded-lg border border-violet-200 text-xs text-violet-700">
                                            <span class="font-bold">ℹ️ Keterangan:</span> RI = 0.90 untuk n=4 (jumlah kriteria)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- END DETAIL PERHITUNGAN AHP -->

                    @else
                        <div class="flex flex-col items-center justify-center py-10 px-4 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700">Belum Ada Perhitungan</h4>
                            <p class="text-xs font-medium text-slate-400 mt-1 max-w-[250px]">Silakan atur nilai perbandingan di matriks dan klik tombol hitung.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @else
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Persyaratan Belum Terpenuhi</h3>
                <p class="text-slate-500 text-sm max-w-md">
                    Pastikan Anda telah memiliki <strong>periode aktif</strong> dan minimal <strong>2 kriteria berstatus aktif</strong> di Master Kriteria sebelum dapat melakukan perhitungan AHP.
                </p>
                <div class="mt-6 flex items-center gap-4">
                    <a href="{{ route('criteria.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Cek Kriteria</a>
                    <a href="{{ route('periods.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Cek Periode</a>
                </div>
            </div>
        @endif

    </div>

    <!-- ✅ SCRIPT ISI OTOMATIS 4 KRITERIA -->
    @if($activePeriod && count($criteria) == 4)
    <script>
        function fillThesisData() {
            const data = {
                @php
                    $criteriaIds = $criteria->pluck('criterion_id')->toArray();
                @endphp
                "{{ $criteriaIds[0] }}_{{ $criteriaIds[1] }}": "0.25",
                "{{ $criteriaIds[0] }}_{{ $criteriaIds[2] }}": "0.5",
                "{{ $criteriaIds[0] }}_{{ $criteriaIds[3] }}": "0.5",
                "{{ $criteriaIds[1] }}_{{ $criteriaIds[2] }}": "2",
                "{{ $criteriaIds[1] }}_{{ $criteriaIds[3] }}": "2",
                "{{ $criteriaIds[2] }}_{{ $criteriaIds[3] }}": "1"
            };

            for (const [key, value] of Object.entries(data)) {
                const elements = document.getElementsByName("comparisons[" + key + "]");
                if (elements.length > 0) {
                    elements[0].value = value;
                    elements[0].dispatchEvent(new Event('change'));
                }
            }

            Swal.fire({
                title: 'Berhasil!',
                text: 'Nilai matriks berhasil diisi otomatis sesuai Skripsi (4 Kriteria)!',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        }
    </script>
    @endif
</x-app-layout>
