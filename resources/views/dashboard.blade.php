<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Dashboard Jitanichi Go') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Welcome Banner -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 rounded-3xl p-8 sm:p-10 shadow-lg shadow-indigo-200">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-white opacity-5 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-400 opacity-20 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 md:flex md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center px-3 py-1 bg-white/10 border border-white/20 backdrop-blur-md rounded-full text-xs font-bold text-indigo-100 uppercase tracking-widest shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                        SPK Prioritas Promosi
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 tracking-tight leading-tight">
                        Halo, {{ auth()->user()->full_name }}! 👋
                    </h1>
                    <p class="text-indigo-100/90 mt-3 text-lg leading-relaxed font-medium max-w-xl">
                        Pantau keseluruhan metrik produk dan temukan rekomendasi promosi terbaik hari ini.
                    </p>
                </div>
                <div class="mt-6 md:mt-0 md:ml-6 shrink-0">
                    <a href="{{ route('saw.index') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-indigo-700 font-bold rounded-2xl shadow-xl shadow-indigo-900/20 hover:bg-slate-50 hover:scale-105 active:scale-95 transition-all duration-200 gap-2">
                        Cek Pemeringkatan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300 group">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Produk</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalProducts }}</h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm font-medium text-slate-500">
                    <span class="text-emerald-500">●</span> Aktif di toko
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-purple-200 transition-all duration-300 group">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Periode Aktif</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight truncate max-w-[150px] sm:max-w-[200px]">{{ optional($activePeriod)->period_name ?? 'N/A' }}</h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-purple-50 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm font-medium text-slate-500">
                    <span class="text-emerald-500">●</span> Siap dinilai
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-emerald-200 transition-all duration-300 group">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Hak Akses</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight capitalize">{{ optional(auth()->user()->role)->role_name }}</h3>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm font-medium text-slate-500">
                    <span class="text-emerald-500">●</span> Akses disesuaikan
                </div>
            </div>
        </div>

        <!-- Top Priority Products -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Top 5 Produk Prioritas</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Rekomendasi berdasarkan metode SAW</p>
                    </div>
                </div>
                <a href="{{ route('saw.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-sm font-bold text-slate-700 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="p-6 flex-1">
                @if($topRankings->isNotEmpty())
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4 rounded-tl-2xl">#</th>
                                    <th class="px-6 py-4">Kode</th>
                                    <th class="px-6 py-4">Nama Produk</th>
                                    <th class="px-6 py-4 text-center">Nilai</th>
                                    <th class="px-6 py-4 text-center rounded-tr-2xl">Status Promo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($topRankings as $rank)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-6 py-4">
                                            @if($rank->rank == 1)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-amber-400 to-amber-200 text-amber-900 font-bold shadow-sm ring-4 ring-amber-50">1</span>
                                            @elseif($rank->rank == 2)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-slate-300 to-slate-100 text-slate-700 font-bold shadow-sm ring-4 ring-slate-50">2</span>
                                            @elseif($rank->rank == 3)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-orange-400 to-orange-200 text-orange-900 font-bold shadow-sm ring-4 ring-orange-50">3</span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold">{{ $rank->rank }}</span>
                                            @endif
                                        </td>
                                        <td><span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs font-bold">{{ optional($rank->productAssessment->product)->product_code }}</span></td>
                                        <td class="font-bold text-slate-800">{{ optional($rank->productAssessment->product)->product_name }}</td>
                                        <td class="text-center font-mono font-bold text-indigo-600">{{ number_format($rank->preference_value, 4) }}</td>
                                        <td class="text-center">
                                            @if(optional($rank->promotionDecision)->discount_type)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                    {{ $rank->promotionDecision->discount_type }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-slate-400 text-xs font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-4 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Hasil</h4>
                        <p class="text-slate-500 text-sm max-w-md mb-6">Periode saat ini belum memiliki hasil pemeringkatan SAW.</p>
                        <a href="{{ route('saw.index') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all">Hitung Pemeringkatan</a>
                    </div>
                @endif
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 sm:hidden">
                <a href="{{ route('saw.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-sm font-bold text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
                    Lihat Semua Ranking
                </a>
            </div>
        </div>

        <!-- Quick Action -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('products.index') }}" class="bg-white p-4 rounded-2xl border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 mx-auto mb-2 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-600">Kelola Produk</p>
            </a>
            <a href="{{ route('periods.index') }}" class="bg-white p-4 rounded-2xl border border-slate-100 hover:border-purple-200 hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 mx-auto mb-2 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-600">Kelola Periode</p>
            </a>
            <a href="{{ route('assessments.index') }}" class="bg-white p-4 rounded-2xl border border-slate-100 hover:border-amber-200 hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 mx-auto mb-2 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-600">Input Penilaian</p>
            </a>
            <a href="{{ route('reports.index') }}" class="bg-white p-4 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 mx-auto mb-2 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                </div>
                <p class="text-xs font-bold text-slate-600">Lihat Laporan</p>
            </a>
        </div>

    </div>
</x-app-layout>