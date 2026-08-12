<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Penilaian Produk (Product Assessments)') }}
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
                    <h3 class="text-lg font-bold text-gray-900">Periode Penilaian Selected</h3>
                    <p class="text-sm text-gray-500">Pilih periode transaksi penjualan/stok produk yang ingin diinputkan.</p>
                </div>
                <form method="GET" action="{{ route('assessments.index') }}" class="flex items-center space-x-2">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form Input Penilaian Produk -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Input Data Produk</h3>
                    <form action="{{ route('assessments.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $activePeriod->period_id }}">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Pilih Produk</label>
                            <select name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->product_id }}">{{ $prod->product_code }} - {{ $prod->product_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Stok Awal</label>
                                <input type="number" name="initial_stock" min="0" value="100" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Stok Akhir</label>
                                <input type="number" name="final_stock" min="0" value="20" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Unit Terjual</label>
                            <input type="number" name="units_sold" min="0" value="80" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Harga Jual (Rp)</label>
                                <input type="number" step="0.01" name="selling_price" value="25000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Harga Pokok (Rp)</label>
                                <input type="number" step="0.01" name="cost_price" value="15000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Tanggal Input Data</label>
                            <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs">
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition text-sm mt-2">
                            Simpan Data Penilaian
                        </button>
                    </form>
                </div>

                <!-- Tabel Assessments -->
                <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Data Produk Periode Ini</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Produk</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600">Awal/Akhir</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600">Terjual</th>
                                    <th class="px-3 py-2 text-right font-semibold text-gray-600">Harga (Jual/Pokok)</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600">Status Data</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($assessments as $ast)
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-900">{{ optional($ast->product)->product_name }}</td>
                                        <td class="px-3 py-2 text-center text-gray-600">{{ $ast->initial_stock }} / {{ $ast->final_stock }}</td>
                                        <td class="px-3 py-2 text-center font-bold text-indigo-600">{{ $ast->units_sold }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">Rp{{ number_format($ast->selling_price, 0) }} / Rp{{ number_format($ast->cost_price, 0) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $ast->data_status == 'layak' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ ucfirst($ast->data_status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <form action="{{ route('assessments.destroy', $ast->assessment_id) }}" method="POST" onsubmit="return confirm('Hapus data penilaian ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada data penilaian untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
