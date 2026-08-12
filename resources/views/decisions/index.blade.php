<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keputusan Promosi & Diskon (Owner Only)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Pilih Periode -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Periode Evaluasi Promosi</h3>
                    <p class="text-sm text-gray-500">Pilih periode penilaian untuk menetapkan keputusan diskon pada produk dengan peringkat prioritas tertinggi.</p>
                </div>
                <form method="GET" action="{{ route('decisions.index') }}" class="flex items-center space-x-2">
                    <select name="period_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ $p->status }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($activePeriod)
                @if($rankings->isEmpty())
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-500">
                        Belum ada data pemeringkatan untuk periode ini. Pastikan Anda telah menghitung SAW di menu "SAW (Ranking)".
                    </div>
                @else
                    <!-- Daftar Produk Berperingkat -->
                    <div class="space-y-6">
                        @foreach($rankings as $rank)
                            <div class="bg-white rounded-2xl shadow-sm border {{ $rank->rank <= 3 ? 'border-indigo-200 ring-1 ring-indigo-50' : 'border-gray-100' }} p-6 md:flex md:justify-between md:items-center">
                                <!-- Info Produk & Peringkat -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 mt-1">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full {{ $rank->rank == 1 ? 'bg-amber-100 text-amber-800' : ($rank->rank <= 3 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600') }} font-extrabold text-lg">
                                            #{{ $rank->rank }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs text-gray-500">{{ optional($rank->productAssessment->product)->product_code }}</span>
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600">{{ optional($rank->productAssessment->product)->category }}</span>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900 mt-1">{{ optional($rank->productAssessment->product)->product_name }}</h4>
                                        <p class="text-sm text-gray-500">Nilai Preferensi (Vi): <span class="font-bold text-emerald-600">{{ number_format($rank->preference_value, 4) }}</span></p>

                                        <!-- Status Keputusan Eksisting -->
                                        @if(optional($rank->promotionDecision)->decision_id)
                                            <div class="mt-3 p-3 bg-emerald-50 rounded-lg border border-emerald-100 inline-block">
                                                <p class="text-xs text-emerald-800 font-bold uppercase tracking-wider mb-1">Keputusan Ditetapkan</p>
                                                <p class="text-sm font-semibold text-emerald-900">{{ $rank->promotionDecision->discount_type }}</p>
                                                @if($rank->promotionDecision->reason)
                                                    <p class="text-xs text-emerald-700 italic mt-1">"{{ $rank->promotionDecision->reason }}"</p>
                                                @endif
                                                <p class="text-[10px] text-emerald-600 mt-1">Oleh: {{ optional($rank->promotionDecision->decidedBy)->full_name }} pada {{ \Carbon\Carbon::parse($rank->promotionDecision->decided_at)->format('d M Y H:i') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Form Keputusan (Hanya untuk top 10 atau semuanya, kita tampilkan untuk semua tapi ditekankan pada top) -->
                                <div class="mt-6 md:mt-0 md:ml-6 md:w-1/3 shrink-0">
                                    <form action="{{ route('decisions.store') }}" method="POST" class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        @csrf
                                        <input type="hidden" name="ranking_id" value="{{ $rank->ranking_id }}">

                                        <div class="mb-3">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tetapkan Diskon / Promosi</label>
                                            <select name="discount_type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Jenis Diskon --</option>
                                                <option value="Tidak Ada Promosi" {{ optional($rank->promotionDecision)->discount_type == 'Tidak Ada Promosi' ? 'selected' : '' }}>Tidak Ada Promosi</option>
                                                <option value="Diskon 10%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 10%' ? 'selected' : '' }}>Diskon 10%</option>
                                                <option value="Diskon 20%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 20%' ? 'selected' : '' }}>Diskon 20%</option>
                                                <option value="Diskon 30%" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 30%' ? 'selected' : '' }}>Diskon 30%</option>
                                                <option value="Diskon 50% (Clearance)" {{ optional($rank->promotionDecision)->discount_type == 'Diskon 50% (Clearance)' ? 'selected' : '' }}>Diskon 50% (Clearance)</option>
                                                <option value="Buy 1 Get 1 Free" {{ optional($rank->promotionDecision)->discount_type == 'Buy 1 Get 1 Free' ? 'selected' : '' }}>Buy 1 Get 1 Free</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan / Alasan (Opsional)</label>
                                            <input type="text" name="reason" value="{{ optional($rank->promotionDecision)->reason }}" placeholder="Misal: Stok menumpuk" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                        </div>
                                        <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow transition text-xs">
                                            Simpan Keputusan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
