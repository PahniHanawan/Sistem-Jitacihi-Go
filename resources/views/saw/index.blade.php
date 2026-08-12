<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perhitungan SAW & Pemeringkatan Prioritas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Pilih Periode -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Periode Perhitungan SAW</h3>
                    <p class="text-sm text-gray-500">Pilih periode penilaian untuk melakukan normalisasi dan melihat peringkat (ranking) prioritas promosi.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <form method="GET" action="{{ route('saw.index') }}" class="flex items-center space-x-2">
                        <select name="period_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach($periods as $p)
                                <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                    {{ $p->period_name }} ({{ $p->status }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if($activePeriod)
                    <form method="POST" action="{{ route('saw.store') }}">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $activePeriod->period_id }}">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition text-sm whitespace-nowrap">
                            Hitung Ulang SAW
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @if($activePeriod)
                <!-- Alert Bobot AHP -->
                @if(count($ahpResults) == 0)
                    <div class="bg-amber-100 border border-amber-400 text-amber-800 px-4 py-3 rounded relative">
                        <strong>Peringatan:</strong> Bobot AHP belum dihitung atau tidak konsisten pada periode ini. Silakan atur bobot AHP terlebih dahulu.
                    </div>
                @else
                    <!-- Hasil Pemeringkatan SAW -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900">Hasil Pemeringkatan Nilai Preferensi (Vi)</h3>
                        <p class="text-sm text-gray-500 mb-4">Produk diurutkan dari nilai Vi terbesar hingga terkecil. Produk teratas adalah prioritas utama untuk diberikan promosi atau diskon.</p>

                        @if($rankings->isEmpty())
                            <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <p class="text-gray-500 text-sm">Pemeringkatan SAW belum dihitung. Klik tombol "Hitung Ulang SAW" di atas.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm border-b border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Peringkat</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Kode</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Produk</th>
                                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Nilai Preferensi (Vi)</th>
                                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Status Promosi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($rankings as $rank)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3">
                                                    <span class="px-3 py-1 font-bold rounded-full {{ $rank->rank == 1 ? 'bg-amber-100 text-amber-800' : ($rank->rank <= 3 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                                        #{{ $rank->rank }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 font-mono font-semibold text-gray-700">{{ optional($rank->productAssessment->product)->product_code }}</td>
                                                <td class="px-4 py-3 font-bold text-gray-900">{{ optional($rank->productAssessment->product)->product_name }}</td>
                                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600">{{ number_format($rank->preference_value, 4) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if(optional($rank->promotionDecision)->discount_type)
                                                        <span class="px-2 py-1 text-xs font-bold bg-emerald-100 text-emerald-800 rounded-full">Ditetapkan: {{ $rank->promotionDecision->discount_type }}</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Belum Ada Keputusan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Detail Normalisasi Matriks R -->
                    @if($normalizations->isNotEmpty())
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mt-6">
                        <h3 class="text-md font-bold text-gray-900 mb-2">Detail Matriks Normalisasi SAW (Rij)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs border-b border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Nama Produk</th>
                                        @foreach($criteria as $crit)
                                            <th class="px-3 py-2 text-right font-semibold text-gray-600">{{ $crit->criterion_code }} ({{ ucfirst($crit->type) }})</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($rankings as $rank)
                                        @php
                                            $astId = $rank->assessment_id;
                                            $norms = $normalizations->get($astId) ?? collect();
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 font-medium text-gray-900">{{ optional($rank->productAssessment->product)->product_name }}</td>
                                            @foreach($criteria as $crit)
                                                @php
                                                    $val = $norms->firstWhere('criterion_id', $crit->criterion_id);
                                                @endphp
                                                <td class="px-3 py-2 text-right font-mono text-gray-600">
                                                    {{ $val ? number_format($val->normalized_value, 4) : '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
