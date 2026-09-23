<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Penilaian Produk') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-start gap-3 shadow-sm shadow-rose-100/50">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 font-medium text-sm">
                    <strong class="block mb-1">Gagal menyimpan data:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600">✕</button>
            </div>
        @endif

        <!-- ================================================================
        HEADER: Periode + Tombol Tambah
        ================================================================ -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Penilaian Produk</h3>
                    <p class="text-sm font-medium text-slate-500">Periode: <strong class="text-indigo-600">{{ optional($activePeriod)->period_name ?? 'Pilih periode' }}</strong></p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <form method="GET" action="{{ route('assessments.index') }}">
                    <select name="period_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-slate-700 transition-all bg-slate-50 min-w-[160px]">
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ optional($activePeriod)->period_id == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ strtoupper($p->status) }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($activePeriod && $activePeriod->status === 'aktif')
                <button @click="$dispatch('open-modal', 'tambah-penilaian')"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Data
                </button>
                @endif
            </div>
        </div>

        <!-- BANNER PERIODE TERKUNCI / BERAKHIR -->
        @if($activePeriod && $activePeriod->status !== 'aktif')
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2z" />
                </svg>
                <div class="text-sm font-medium">
                    <strong>Periode Berakhir/Nonaktif:</strong> Periode ini bersifat <em>read-only</em>. Anda tidak dapat menambah, mengedit, atau menghapus data penilaian.
                </div>
            </div>
        @endif

        <!-- ================================================================
        STATS CARD (MENGGUNAKAN $stats DARI CONTROLLER - KOMPLIT SEMUA DATA)
        ================================================================ -->
        @if($activePeriod)
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-2xl font-extrabold text-indigo-600">{{ $stats['total'] }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Data</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['layak'] }}</p>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Layak</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-2xl font-extrabold text-amber-500">{{ $stats['belum_memadai'] }}</p>
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Belum Memadai</p>
            </div>
        </div>
        @endif

        @if($activePeriod)

        <!-- ================================================================
        TABEL DATA (DENGAN PAGINATION) + INFORMASI HALAMAN
        ================================================================ -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-bold text-slate-700">Daftar Produk</span>
                    <span class="text-[10px] font-bold text-slate-400 bg-white px-2 py-0.5 rounded-full border border-slate-200">
                        {{ $assessments->total() }} dari {{ $stats['total'] }} total
                    </span>
                </div>
                <div class="text-[10px] font-bold text-slate-400">
                    Halaman {{ $assessments->currentPage() }} dari {{ $assessments->lastPage() }}
                </div>
            </div>

            @if($assessments->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Belum Ada Data</h4>
                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Data" untuk menginput.</p>
                </div>
            @else
                <!-- Tabel -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center">Stok Awal</th>
                                <th class="px-4 py-3 text-center">Stok Akhir</th>
                                <th class="px-4 py-3 text-center">Terjual</th>
                                <th class="px-4 py-3 text-right">Harga Jual</th>
                                <th class="px-4 py-3 text-right">Harga Pokok</th>
                                <th class="px-4 py-3 text-center">Margin</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($assessments as $ast)
                                @php
                                    $margin = $ast->selling_price > 0 ? round((($ast->selling_price - $ast->cost_price) / $ast->selling_price) * 100, 2) : 0;
                                    $isLayak = $ast->data_status == 'layak';
                                    $stockRatio = $ast->initial_stock > 0 ? round(($ast->final_stock / $ast->initial_stock) * 100, 0) : 0;
                                    $stockColor = $stockRatio > 70 ? 'text-rose-600' : ($stockRatio > 40 ? 'text-amber-600' : 'text-emerald-600');
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 text-sm">{{ optional($ast->product)->product_name }}</span>
                                            <span class="text-[10px] font-mono font-bold text-slate-400">{{ optional($ast->product)->product_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-bold text-slate-700">{{ $ast->initial_stock }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="font-bold {{ $stockColor }}">{{ $ast->final_stock }}</span>
                                            <div class="w-full max-w-[50px] h-1 rounded-full bg-slate-200 overflow-hidden mt-1">
                                                <div class="h-full rounded-full {{ $stockRatio > 70 ? 'bg-rose-400' : ($stockRatio > 40 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                                     style="width: {{ min($stockRatio, 100) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-bold text-indigo-600 text-sm">{{ $ast->units_sold }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium text-slate-700 text-sm">Rp{{ number_format($ast->selling_price, 0) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium text-slate-500 text-sm">Rp{{ number_format($ast->cost_price, 0) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($margin >= 50)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                                {{ $margin }}%
                                            </span>
                                        @elseif($margin >= 25)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">
                                                {{ $margin }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px] border border-rose-200">
                                                {{ $margin }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isLayak)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                LAYAK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                Belum Memadai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('assessments.show', $ast->assessment_id) }}"
                                               class="p-1.5 rounded-lg text-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                               title="Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('assessments.edit', $ast->assessment_id) }}"
                                               class="p-1.5 rounded-lg text-amber-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('assessments.destroy', $ast->assessment_id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Yakin hapus data penilaian ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-5 py-3 bg-slate-50/50 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs text-slate-500">
                        Menampilkan <strong class="text-slate-700">{{ $assessments->firstItem() ?? 0 }}</strong> -
                        <strong class="text-slate-700">{{ $assessments->lastItem() ?? 0 }}</strong> dari
                        <strong class="text-slate-700">{{ $assessments->total() }}</strong> data
                    </div>
                    <div>
                        {{ $assessments->appends(['period_id' => request('period_id')])->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
        </div>

        @else
            <!-- No Active Period -->
            <div class="bg-white p-12 rounded-3xl shadow-sm border border-slate-100 text-center">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Periode Aktif</h3>
                <p class="text-slate-500 text-sm max-w-md mx-auto">Silakan buat periode penilaian terlebih dahulu di menu <strong>Periode</strong>.</p>
                <div class="mt-6">
                    <a href="{{ route('periods.index') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-200 transition-all text-sm">Kelola Periode</a>
                </div>
            </div>
        @endif

    </div>

    <!-- ================================================================
    MODAL TAMBAH DATA PENILAIAN
    ================================================================ -->
    <x-modal name="tambah-penilaian" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tambah Data Penilaian</h3>
                    <p class="text-sm font-medium text-slate-500">Periode: <strong class="text-indigo-600">{{ optional($activePeriod)->period_name }}</strong></p>
                </div>
                <button @click="$dispatch('close-modal', 'tambah-penilaian')" class="ml-auto text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($activePeriod)
            <form action="{{ route('assessments.store') }}" method="POST" class="space-y-4" id="assessmentForm">
                @csrf
                <input type="hidden" name="period_id" value="{{ optional($activePeriod)->period_id }}">
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- PILIH PRODUK -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Produk</label>
                        <select name="product_id" id="product_id" onchange="onProductSelect()" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->product_id }}" 
                                        data-price="{{ $prod->selling_price ?? 0 }}"
                                        data-cost="{{ $prod->cost_price ?? 0 }}">
                                    {{ $prod->product_code }} - {{ $prod->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- TANGGAL MASUK -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Masuk</label>
                        <input type="date" name="entry_date" id="entry_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
            
                    <!-- STOK AWAL -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Stok Awal</label>
                        <input type="number" name="initial_stock" id="initial_stock" min="0" placeholder="0" oninput="calculateSold()" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                        <span id="stockNotice" class="text-[11px] text-indigo-600 font-medium hidden"></span>
                    </div>
            
                    <!-- STOK AKHIR -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Stok Akhir</label>
                        <input type="number" name="final_stock" id="final_stock" min="0" placeholder="0" oninput="calculateSold()" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
            
                    <!-- UNIT TERJUAL (READONLY OTOMATIS) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Unit Terjual (Otomatis)</label>
                        <input type="number" name="units_sold" id="units_sold" value="0" readonly class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 font-bold text-indigo-600 text-sm cursor-not-allowed">
                    </div>
            
                    <!-- HARGA JUAL & POKOK -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Harga Jual</label>
                            <input type="number" step="0.01" name="selling_price" id="selling_price" placeholder="25000" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Harga Pokok</label>
                            <input type="number" step="0.01" name="cost_price" id="cost_price" placeholder="15000" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                        </div>
                    </div>
                </div>
            
                <!-- TOMBOL SUBMIT DYNAMIC -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center gap-2">
                        Simpan Data
                    </button>
                    <button type="button" @click="$dispatch('close-modal', 'tambah-penilaian')" class="px-6 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
            
            <!-- SCRIPT JAVASCRIPT OTOMATISASI -->
            <script>
                // Map Stok Akhir Periode Sebelumnya dari Controller
                const previousStockMap = @json($previousAssessmentMap ?? []);
                
                // Map Data Penilaian yang Sudah Ada di Periode Ini (untuk Upsert/Edit Autofill)
                const currentAssessments = @json($allAssessments ?? []);
            
                function calculateSold() {
                    const initial = parseInt(document.getElementById('initial_stock').value) || 0;
                    const final = parseInt(document.getElementById('final_stock').value) || 0;
                    
                    let sold = initial - final;
                    if (sold < 0) sold = 0; // Mencegah nilai negatif jika stok akhir > stok awal
                    
                    document.getElementById('units_sold').value = sold;
                }
            
                function onProductSelect() {
                    const select = document.getElementById('product_id');
                    const productId = select.value;
                    const selectedOption = select.options[select.selectedIndex];
                    
                    const initialStockInput = document.getElementById('initial_stock');
                    const finalStockInput = document.getElementById('final_stock');
                    const sellingPriceInput = document.getElementById('selling_price');
                    const costPriceInput = document.getElementById('cost_price');
                    const stockNotice = document.getElementById('stockNotice');
                    const submitBtn = document.getElementById('submitBtn');
            
                    if (!productId) {
                        submitBtn.innerText = 'Simpan Data';
                        stockNotice.classList.add('hidden');
                        return;
                    }
            
                    // 1. Cek apakah produk sudah dinilai di periode ini (Mencegah Duplikasi)
                    const existingData = currentAssessments.find(a => a.product_id == productId);
            
                    if (existingData) {
                        // Mode Produk Sudah Dinilai
                        initialStockInput.value = '';
                        finalStockInput.value = '';
                        sellingPriceInput.value = '';
                        costPriceInput.value = '';
                        submitBtn.innerText = 'Sudah Dinilai';
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        stockNotice.innerText = ' Data produk ini sudah ada di periode ini. Gunakan menu Edit pada tabel.';
                        stockNotice.classList.remove('hidden');
                        stockNotice.classList.add('text-rose-600');
                        stockNotice.classList.remove('text-indigo-600');
                    } else {
                        // Mode Tambah Data Baru
                        submitBtn.innerText = 'Simpan Data';
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        stockNotice.classList.add('text-indigo-600');
                        stockNotice.classList.remove('text-rose-600');
                        
                        // Auto-fill Stok Awal dan Harga dari Periode Sebelumnya (jika ada)
                        if (previousStockMap[productId] !== undefined) {
                            initialStockInput.value = previousStockMap[productId].final_stock;
                            sellingPriceInput.value = previousStockMap[productId].selling_price;
                            costPriceInput.value = previousStockMap[productId].cost_price;
                            stockNotice.innerText = ` Otomatis diisi dari stok akhir & harga periode sebelumnya (${previousStockMap[productId].final_stock}).`;
                            stockNotice.classList.remove('hidden');
                        } else {
                            initialStockInput.value = '';
                            sellingPriceInput.value = '';
                            costPriceInput.value = '';
                            stockNotice.classList.add('hidden');
                        }
                        
                        finalStockInput.value = '';
                    }
            
                    calculateSold();
                }
            </script>
            @endif
        </div>
    </x-modal>

</x-app-layout>
