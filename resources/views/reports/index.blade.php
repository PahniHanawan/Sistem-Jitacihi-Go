<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Laporan Hasil SPK') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Pilih Periode & Print Button -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 leading-tight">Cetak Laporan Pemeringkatan</h3>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">Pilih periode untuk melihat atau mengunduh laporan akhir.</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0 w-full md:w-auto">
                <form method="GET" action="{{ route('reports.index') }}" class="flex-1 sm:w-64">
                    <select name="period_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-slate-700 transition-all bg-slate-50">
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ strtoupper($p->status) }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($activePeriod && $rankings->isNotEmpty())
                    <a href="{{ route('reports.print', ['period_id' => $activePeriod->period_id]) }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                @endif
            </div>
        </div>

        @if($activePeriod)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative">
                
                <!-- Decorative background -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-60 pointer-events-none -mt-20 -mr-20"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none -mb-20 -ml-20"></div>

                <div class="p-8 sm:p-12 relative z-10">

                    <!-- ✅ PERBAIKAN: HAPUS LOGO EKSTERNAL, PAKAI INISIAL JG -->
                    <div class="text-center mb-10 pb-8 border-b-2 border-dashed border-slate-200">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-200 border border-indigo-100 mb-4">
                            <span class="text-white font-extrabold text-2xl">JG</span>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-800 uppercase tracking-widest leading-tight">Laporan Keputusan Promosi</h2>
                        <h3 class="text-xl font-bold text-indigo-600 mt-2 tracking-wide">TOKO JITANICHI GO</h3>
                        <div class="mt-4 inline-flex items-center px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-sm font-bold tracking-wide">
                            Periode: {{ $activePeriod->period_name }} &bull; {{ \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') }}
                        </div>
                    </div>

                    @if($rankings->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 px-4 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak ada data</h4>
                            <p class="text-sm font-medium text-slate-400 mt-1">Belum ada hasil pemeringkatan untuk periode ini yang bisa dilaporkan.</p>
                        </div>
                    @else
                        <!-- Bobot AHP -->
                        <div class="mb-10">
                            <div class="flex items-center gap-2 mb-5">
                                <span class="w-6 h-6 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">1</span>
                                <h4 class="text-lg font-extrabold text-slate-800 tracking-tight">Bobot Prioritas Kriteria (AHP)</h4>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($ahpResults as $res)
                                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between h-full relative overflow-hidden group hover:border-indigo-300 transition-colors">
                                        <div class="absolute bottom-0 right-0 w-16 h-16 bg-indigo-50 rounded-tl-full opacity-50 transition-transform group-hover:scale-110"></div>
                                        <div class="relative z-10">
                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ optional($res->criterion)->criterion_code }}</p>
                                            <h5 class="text-sm font-bold text-slate-800 leading-snug mb-3">{{ optional($res->criterion)->criterion_name }}</h5>
                                        </div>
                                        <div class="text-2xl font-black text-indigo-600 relative z-10 mt-auto">{{ number_format($res->weight * 100, 1) }}%</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tabel Ranking -->
                        <div>
                            <div class="flex items-center gap-2 mb-5">
                                <span class="w-6 h-6 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">2</span>
                                <h4 class="text-lg font-extrabold text-slate-800 tracking-tight">Rekomendasi Pemeringkatan &amp; Keputusan Final (SAW)</h4>
                            </div>
                            <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-xs font-bold border-b border-slate-200">
                                        <tr>
                                            <th class="px-5 py-4 border-r border-slate-200 text-center w-20">Rank</th>
                                            <th class="px-5 py-4 border-r border-slate-200">Kode &amp; Nama Produk</th>
                                            <th class="px-5 py-4 border-r border-slate-200 text-center">Nilai (Vi)</th>
                                            <th class="px-5 py-4">Keputusan Final</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @foreach($rankings as $rank)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="px-5 py-4 border-r border-slate-200 text-center">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $rank->rank <= 3 ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600' }} font-bold text-sm">
                                                        {{ $rank->rank }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 border-r border-slate-200">
                                                    <div class="font-bold text-slate-900 text-base">{{ optional($rank->productAssessment->product)->product_name }}</div>
                                                    <div class="text-xs font-mono font-bold text-slate-500 mt-0.5">{{ optional($rank->productAssessment->product)->product_code }}</div>
                                                </td>
                                                <td class="px-5 py-4 border-r border-slate-200 text-center">
                                                    <span class="font-mono font-bold text-indigo-600 text-base">{{ number_format($rank->preference_value, 4) }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    @if(optional($rank->promotionDecision)->discount_type)
                                                        <div class="inline-flex items-center px-3 py-1 rounded border border-slate-200 bg-slate-50 text-slate-800 font-bold text-sm">
                                                            {{ $rank->promotionDecision->discount_type }}
                                                        </div>
                                                        @if($rank->promotionDecision->reason)
                                                            <div class="text-[11px] font-medium text-slate-500 mt-2 flex items-start gap-1 max-w-xs whitespace-normal">
                                                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <span class="italic leading-snug">{{ $rank->promotionDecision->reason }}</span>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="inline-flex items-center text-slate-400 text-xs font-medium italic">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-1.5"></span>
                                                            Belum ditetapkan
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Pilih Periode Laporan</h3>
                <p class="text-slate-500 text-sm max-w-md">
                    Anda belum memilih periode aktif untuk menampilkan laporan.
                </p>
            </div>
        @endif

    </div>
</x-app-layout>