<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard SPK Toko Jitanichi Go') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Notification Alert -->
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Banner / Welcome -->
            <div class="bg-gradient-to-r from-rose-500 via-purple-600 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="md:flex md:items-center md:justify-between">
                    <div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider">
                            SPK Prioritas Promosi & Diskon
                        </span>
                        <h1 class="text-3xl font-extrabold mt-2">Selamat Datang, {{ auth()->user()->full_name }}!</h1>
                        <p class="text-rose-100 mt-1 max-w-2xl">
                            Sistem ini membantu menentukan produk mana yang paling layak diprioritaskan untuk dipromosikan atau diberikan diskon menggunakan integrasi metode <strong>AHP (Bobot Kriteria)</strong> dan <strong>SAW (Pemeringkatan Alternatif)</strong>.
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('saw.index') }}" class="px-5 py-2.5 bg-white text-rose-600 font-bold rounded-xl shadow hover:bg-rose-50 transition">
                            Lihat Pemeringkatan SAW &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-blue-50 text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Produk Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-purple-50 text-purple-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Periode Evaluasi Aktif</p>
                            <p class="text-lg font-bold text-gray-900">{{ optional($activePeriod)->period_name ?? 'Belum Ada' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Peran Pengguna</p>
                            <p class="text-lg font-bold text-gray-900 capitalize">{{ optional(auth()->user()->role)->role_name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 5 Priority Products Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Top 5 Produk Prioritas Promosi (Periode Aktif)</h3>
                    <a href="{{ route('saw.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                </div>

                @if($topRankings->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Preferensi (Vi)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekomendasi Diskon</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topRankings as $rank)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-full {{ $rank->rank == 1 ? 'bg-amber-100 text-amber-800' : ($rank->rank <= 3 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                                #{{ $rank->rank }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ optional($rank->productAssessment->product)->product_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ optional($rank->productAssessment->product)->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">{{ number_format($rank->preference_value, 4) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(optional($rank->promotionDecision)->discount_type)
                                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs">
                                                    {{ $rank->promotionDecision->discount_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 italic">Belum ditetapkan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-gray-500">Belum ada hasil pemeringkatan SAW untuk periode aktif saat ini.</p>
                        <a href="{{ route('saw.index') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow hover:bg-indigo-700">Hitung Pemeringkatan Sekarang</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
