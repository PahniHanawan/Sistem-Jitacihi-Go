<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Master Produk') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Produk -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 h-fit sticky top-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Tambah Produk</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Input data produk baru</p>
                    </div>
                </div>
                
                <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Kode Produk</label>
                        <input type="text" name="product_code" placeholder="Contoh: PRD-006" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all font-mono">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Produk</label>
                        <input type="text" name="product_name" placeholder="Contoh: Bento Salmon Teriyaki" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Kategori</label>
                        <input type="text" name="category" placeholder="Contoh: Makanan Berat" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                        <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                        Simpan Produk
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>
                </form>
            </div>

            <!-- Tabel Data Produk -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 leading-tight">Daftar Produk</h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Kelola seluruh produk toko</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 flex-1">
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                <tr>
                                    <th scope="col" class="px-6 py-4 rounded-tl-2xl">Kode</th>
                                    <th scope="col" class="px-6 py-4">Nama Produk</th>
                                    <th scope="col" class="px-6 py-4">Kategori</th>
                                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($products as $p)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs font-bold tracking-wide">
                                                {{ $p->product_code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-800">{{ $p->product_name }}</td>
                                        <td class="px-6 py-4 text-slate-500 font-medium">{{ $p->category }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($p->status == 'aktif')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs tracking-wide border border-emerald-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-xs tracking-wide">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span> Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('products.show', $p->product_id) }}" class="p-2 rounded-xl text-blue-500 hover:bg-blue-50 hover:text-blue-600 transition-colors tooltip" title="Detail Riwayat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                </a>
                                                <a href="{{ route('products.edit', $p->product_id) }}" class="p-2 rounded-xl text-amber-500 hover:bg-amber-50 hover:text-amber-600 transition-colors tooltip" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                                <form action="{{ route('products.destroy', $p->product_id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Yakin hapus produk ini?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-colors tooltip" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700">Tidak ada produk</h4>
                                            <p class="text-xs font-medium text-slate-400 mt-1">Silakan tambahkan produk baru pada form di samping.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
