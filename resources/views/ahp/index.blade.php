<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perhitungan Pembobotan Kriteria AHP (Owner Only)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-amber-100 border border-amber-400 text-amber-800 px-4 py-3 rounded relative">
                    {{ session('warning') }}
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
                    <h3 class="text-lg font-bold text-gray-900">Periode Evaluasi AHP</h3>
                    <p class="text-sm text-gray-500">Pilih periode penilaian untuk melakukan perbandingan berpasangan kriteria.</p>
                </div>
                <form method="GET" action="{{ route('ahp.index') }}" class="flex items-center space-x-2">
                    <select name="period_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ $p->status }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($activePeriod && count($criteria) >= 2)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Form Pairwise Matrix -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Matriks Perbandingan Berpasangan Skala Saaty</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Skala 1 = Sama penting, 3 = Sedikit lebih penting, 5 = Lebih penting, 7 = Sangat penting, 9 = Mutlak lebih penting.
                    </p>

                    <form action="{{ route('ahp.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $activePeriod->period_id }}">

                        <div class="space-y-3">
                            @for($i = 0; $i < count($criteria); $i++)
                                @for($j = $i + 1; $j < count($criteria); $j++)
                                    @php
                                        $cA = $criteria[$i];
                                        $cB = $criteria[$j];
                                        $key = "{$cA->criterion_id}_{$cB->criterion_id}";
                                        $currentVal = $existingComparisons[$key] ?? 1;
                                    @endphp
                                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                                        <div class="flex justify-between items-center text-xs font-bold mb-2">
                                            <span class="text-indigo-700">[{{ $cA->criterion_code }}] {{ $cA->criterion_name }}</span>
                                            <span class="text-gray-400">VS</span>
                                            <span class="text-purple-700">[{{ $cB->criterion_code }}] {{ $cB->criterion_name }}</span>
                                        </div>
                                        <select name="comparisons[{{ $key }}]" class="block w-full rounded-md border-gray-300 shadow-sm text-xs font-mono">
                                            <option value="1" {{ $currentVal == 1 ? 'selected' : '' }}>1 - Sama Penting</option>
                                            <option value="2" {{ $currentVal == 2 ? 'selected' : '' }}>2 - Berdekatan Sedikit Lebih Penting</option>
                                            <option value="3" {{ $currentVal == 3 ? 'selected' : '' }}>3 - Sedikit Lebih Penting</option>
                                            <option value="4" {{ $currentVal == 4 ? 'selected' : '' }}>4 - Berdekatan Lebih Penting</option>
                                            <option value="5" {{ $currentVal == 5 ? 'selected' : '' }}>5 - Lebih Penting</option>
                                            <option value="6" {{ $currentVal == 6 ? 'selected' : '' }}>6 - Berdekatan Sangat Penting</option>
                                            <option value="7" {{ $currentVal == 7 ? 'selected' : '' }}>7 - Sangat Penting</option>
                                            <option value="8" {{ $currentVal == 8 ? 'selected' : '' }}>8 - Berdekatan Mutlak Lebih Penting</option>
                                            <option value="9" {{ $currentVal == 9 ? 'selected' : '' }}>9 - Mutlak Lebih Penting</option>
                                            <option value="0.3333" {{ abs($currentVal - 0.3333) < 0.01 ? 'selected' : '' }}>1/3 - Kebalikan (3)</option>
                                            <option value="0.2" {{ abs($currentVal - 0.2) < 0.01 ? 'selected' : '' }}>1/5 - Kebalikan (5)</option>
                                            <option value="0.1429" {{ abs($currentVal - 0.1429) < 0.01 ? 'selected' : '' }}>1/7 - Kebalikan (7)</option>
                                            <option value="0.1111" {{ abs($currentVal - 0.1111) < 0.01 ? 'selected' : '' }}>1/9 - Kebalikan (9)</option>
                                        </select>
                                    </div>
                                @endfor
                            @endfor
                        </div>

                        <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition text-sm">
                            Hitung Bobot AHP & Cek Konsistensi
                        </button>
                    </form>
                </div>

                <!-- Hasil Bobot & Konsistensi -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Hasil Bobot Prioritas AHP & Status Konsistensi</h3>

                    @if($consistencyData)
                        <div class="p-4 rounded-xl {{ $consistencyData['is_valid'] ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-gray-800">Status Konsistensi Matriks (CR):</span>
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $consistencyData['is_valid'] ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                    {{ $consistencyData['is_valid'] ? 'KONSISTEN (CR <= 0.10)' : 'TIDAK KONSISTEN (CR > 0.10)' }}
                                </span>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs font-mono">
                                <div class="bg-white p-2 rounded shadow-sm">
                                    <span class="block text-gray-500">λ max</span>
                                    <span class="font-bold text-gray-900">{{ number_format($consistencyData['lambda_max'], 4) }}</span>
                                </div>
                                <div class="bg-white p-2 rounded shadow-sm">
                                    <span class="block text-gray-500">CI</span>
                                    <span class="font-bold text-gray-900">{{ number_format($consistencyData['ci'], 4) }}</span>
                                </div>
                                <div class="bg-white p-2 rounded shadow-sm">
                                    <span class="block text-gray-500">CR</span>
                                    <span class="font-bold {{ $consistencyData['is_valid'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($consistencyData['cr'], 4) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Kode</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Kriteria</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Bobot Prioritas (W)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($ahpResults as $res)
                                        <tr>
                                            <td class="px-4 py-2 font-mono font-bold text-indigo-600">{{ optional($res->criterion)->criterion_code }}</td>
                                            <td class="px-4 py-2 text-gray-900 font-medium">{{ optional($res->criterion)->criterion_name }}</td>
                                            <td class="px-4 py-2 text-right font-mono font-bold text-emerald-600">{{ number_format($res->weight, 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed text-gray-400 text-sm">
                            Belum ada perhitungan AHP untuk periode ini. Silakan atur nilai perbandingan di sebelah kiri dan klik simpan.
                        </div>
                    @endif
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>
