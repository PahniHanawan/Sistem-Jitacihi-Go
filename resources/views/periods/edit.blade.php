<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Edit Periode') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('periods.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Kembali ke Kelola Periode
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Edit Data Periode</h3>
                    <p class="text-sm font-medium text-slate-500">Perbarui informasi periode penilaian</p>
                </div>
            </div>

            <form action="{{ route('periods.update', $period->period_id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Periode</label>
                    <input type="text" name="period_name" value="{{ old('period_name', $period->period_name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all @error('period_name') border-rose-500 @enderror">
                    @error('period_name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $period->start_date) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all text-slate-600 @error('start_date') border-rose-500 @enderror">
                        @error('start_date')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $period->end_date) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all text-slate-600 @error('end_date') border-rose-500 @enderror">
                        @error('end_date')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Status Periode</label>
                    <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all bg-white @error('status') border-rose-500 @enderror">
                        <option value="aktif" {{ old('status', $period->status) == 'aktif' ? 'selected' : '' }}>Aktif (Sedang Berjalan)</option>
                        <option value="selesai" {{ old('status', $period->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">Mengubah periode ini menjadi 'Aktif' akan otomatis membuat periode aktif lainnya menjadi 'Selesai'.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('periods.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
