<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Kelola Periode') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Tambah Periode -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 h-fit sticky top-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Tambah Periode</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Penjadwalan evaluasi</p>
                    </div>
                </div>

                <form action="{{ route('periods.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Periode</label>
                        <input type="text" name="period_name" placeholder="Contoh: Periode Agustus 2026" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                        <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white">
                            <option value="aktif">Aktif (Periode Utama)</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                        Simpan Periode
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>
                </form>
            </div>

            <!-- Tabel Data Periode -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 leading-tight">Daftar Periode Evaluasi</h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Penilaian SPK dilakukan per periode</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 flex-1">
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                                <tr>
                                    <th scope="col" class="px-6 py-4 rounded-tl-2xl">Nama Periode</th>
                                    <th scope="col" class="px-6 py-4">Mulai</th>
                                    <th scope="col" class="px-6 py-4">Selesai</th>
                                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($periods as $per)
                                    <tr class="{{ $per->status == 'aktif' ? 'bg-indigo-50/30' : 'hover:bg-slate-50/80 transition-colors' }}">
                                        <td class="px-6 py-4 font-bold {{ $per->status == 'aktif' ? 'text-indigo-900' : 'text-slate-800' }}">
                                            {{ $per->period_name }}
                                            @if($per->status == 'aktif')
                                                <span class="inline-flex ml-2 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 tracking-wider uppercase">
                                                    Current
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-600">{{ $per->start_date }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-600">{{ $per->end_date }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($per->status == 'aktif')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-600 text-white font-bold text-xs tracking-wider shadow-sm shadow-indigo-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5 animate-pulse"></span> AKTIF
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-xs tracking-wider">
                                                    SELESAI
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('periods.edit', $per->period_id) }}" class="p-2 rounded-xl text-amber-500 hover:bg-amber-50 hover:text-amber-600 transition-colors tooltip" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                                <form action="{{ route('periods.destroy', $per->period_id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Yakin hapus periode ini?">
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
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700">Tidak ada periode</h4>
                                            <p class="text-xs font-medium text-slate-400 mt-1">Silakan tambahkan periode penilaian baru.</p>
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
