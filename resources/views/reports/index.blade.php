<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Hasil SPK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Pilih Periode & Print Button -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Laporan Pemeringkatan & Keputusan Promosi</h3>
                    <p class="text-sm text-gray-500">Pilih periode untuk melihat laporan akhir yang siap cetak.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex items-center space-x-2">
                        <select name="period_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach($periods as $p)
                                <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                    {{ $p->period_name }} ({{ $p->status }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if($activePeriod && $rankings->isNotEmpty())
                        <a href="{{ route('reports.print', ['period_id' => $activePeriod->period_id]) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak Laporan
                        </a>
                    @endif
                </div>
            </div>

            @if($activePeriod)
                <!-- Laporan Preview -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="text-center mb-8 border-b border-gray-200 pb-6">
                        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-wider">Laporan Hasil Keputusan Promosi</h2>
                        <h3 class="text-lg font-bold text-gray-700 mt-1">Toko Jitanichi Go</h3>
                        <p class="text-sm text-gray-500 mt-2">Periode: {{ $activePeriod->period_name }} ({{ \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') }})</p>
                    </div>

                    @if($rankings->isEmpty())
                        <div class="p-12 text-center text-gray-400 font-medium">Belum ada data hasil pemeringkatan untuk periode ini.</div>
                    @else
                        <!-- Ringkasan Bobot AHP -->
                        <div class="mb-8">
                            <h4 class="text-md font-bold text-gray-800 border-l-4 border-indigo-500 pl-3 mb-4">1. Bobot Prioritas Kriteria (AHP)</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($ahpResults as $res)
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                                        <div class="text-sm font-bold text-gray-700">{{ optional($res->criterion)->criterion_name }}</div>
                                        <div class="text-lg font-black text-indigo-600">{{ number_format($res->weight * 100, 1) }}%</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tabel Hasil Keputusan -->
                        <div>
                            <h4 class="text-md font-bold text-gray-800 border-l-4 border-indigo-500 pl-3 mb-4">2. Rekomendasi Pemeringkatan & Keputusan (SAW)</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-bold text-gray-700 border-r border-gray-200">Rank</th>
                                            <th class="px-4 py-3 text-left font-bold text-gray-700 border-r border-gray-200">Kode & Nama Produk</th>
                                            <th class="px-4 py-3 text-center font-bold text-gray-700 border-r border-gray-200">Nilai (Vi)</th>
                                            <th class="px-4 py-3 text-left font-bold text-gray-700">Keputusan Diskon / Promosi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($rankings as $rank)
                                            <tr>
                                                <td class="px-4 py-3 border-r border-gray-200 text-center font-bold text-gray-900">{{ $rank->rank }}</td>
                                                <td class="px-4 py-3 border-r border-gray-200">
                                                    <div class="font-bold text-gray-900">{{ optional($rank->productAssessment->product)->product_name }}</div>
                                                    <div class="text-xs font-mono text-gray-500">{{ optional($rank->productAssessment->product)->product_code }}</div>
                                                </td>
                                                <td class="px-4 py-3 border-r border-gray-200 text-center font-bold text-indigo-600">{{ number_format($rank->preference_value, 4) }}</td>
                                                <td class="px-4 py-3">
                                                    @if(optional($rank->promotionDecision)->discount_type)
                                                        <div class="font-bold text-emerald-700">{{ $rank->promotionDecision->discount_type }}</div>
                                                        @if($rank->promotionDecision->reason)
                                                            <div class="text-xs text-gray-500 mt-1 italic">Catatan: {{ $rank->promotionDecision->reason }}</div>
                                                        @endif
                                                    @else
                                                        <div class="text-gray-400 text-xs italic">Belum ada keputusan</div>
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
            @endif

        </div>
    </div>
</x-app-layout>
