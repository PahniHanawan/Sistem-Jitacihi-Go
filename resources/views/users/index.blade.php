<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- ============================================================
        FORM TAMBAH PENGGUNA — RAPIH
        ============================================================ -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-md font-bold text-slate-800">Tambah Pengguna Baru</h3>
                    <p class="text-xs font-medium text-slate-500">Tambahkan Admin atau Owner baru ke sistem</p>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('users.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="username" placeholder="Masukkan username" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all @error('username') border-rose-500 ring-rose-500 @enderror">
                        @error('username')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" placeholder="email@example.com" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all @error('email') border-rose-500 ring-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="full_name" placeholder="Nama lengkap" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all @error('full_name') border-rose-500 ring-rose-500 @enderror">
                        @error('full_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <select name="role_id" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white @error('role_id') border-rose-500 ring-rose-500 @enderror">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all @error('password') border-rose-500 ring-rose-500 @enderror">
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white">
                            <option value="aktif">✅ Aktif</option>
                            <option value="nonaktif">⛔ Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================
        TABEL DAFTAR USER
        ============================================================ -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Daftar Pengguna</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Kelola akun Admin & Owner</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-white px-3 py-1.5 rounded-full border border-slate-200">
                    {{ $users->count() }} pengguna
                </span>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-2xl">#</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3 text-center">Role</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center rounded-tr-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($users as $index => $user)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $user->user_id === Auth::id() ? 'bg-indigo-50/30' : '' }}">
                                    <td class="px-4 py-3 font-bold text-slate-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-sm font-bold text-slate-700">{{ $user->username }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $user->full_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ optional($user->role)->role_name == 'owner' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-700' }}">
                                            {{ strtoupper(optional($user->role)->role_name ?? 'USER') }}
                                        </span>
                                        @if($user->user_id === Auth::id())
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($user->status == 'aktif')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Edit Button -->
                                            <button onclick="openEditModal({{ $user->user_id }})"
                                                    class="p-1.5 rounded-lg text-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>

                                            <!-- Toggle Status -->
                                            @if($user->user_id !== Auth::id())
                                                <form action="{{ route('users.toggle-status', $user->user_id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="p-1.5 rounded-lg {{ $user->status == 'aktif' ? 'text-rose-500 hover:bg-rose-50 hover:text-rose-600' : 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600' }} transition-colors" title="{{ $user->status == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->status == 'aktif' ? 'M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-5.657-5.657a4.978 4.978 0 01-2.83-1.414m5.657-5.657a9 9 0 019.238 2.167M9 12a3 3 0 113 3m0-6a3 3 0 01-3 3' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Reset Password -->
                                            @if($user->user_id !== Auth::id())
                                                <button onclick="openResetModal({{ $user->user_id }})"
                                                        class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                                                        title="Reset Password">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Delete -->
                                            @if($user->user_id !== Auth::id())
                                                <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="inline-block delete-form" data-confirm-message="Yakin hapus user {{ $user->full_name }}?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-700">Tidak ada pengguna</h4>
                                        <p class="text-xs font-medium text-slate-400 mt-1">Tambahkan user baru di form di atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ================================================================
    MODAL EDIT USER
    ================================================================ -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm" aria-hidden="true" onclick="closeEditModal()"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="px-6 pt-6 pb-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">✏️ Edit Pengguna</h3>
                            <p class="text-sm font-medium text-slate-500">Perbarui data pengguna yang dipilih</p>
                        </div>
                    </div>
                    <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="editForm" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Username</label>
                            <input type="text" id="edit_username" name="username" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Email</label>
                            <input type="email" id="edit_email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" id="edit_full_name" name="full_name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Role</label>
                            <select id="edit_role_id" name="role_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white">
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ ucfirst($role->role_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                            <select id="edit_status" name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-white">
                                <option value="aktif">✅ Aktif</option>
                                <option value="nonaktif">⛔ Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================================================================
    MODAL RESET PASSWORD
    ================================================================ -->
    <div id="resetModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm" aria-hidden="true" onclick="closeResetModal()"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="px-6 pt-6 pb-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">🔑 Reset Password</h3>
                            <p class="text-sm font-medium text-slate-500">Reset password untuk user yang dipilih</p>
                        </div>
                    </div>
                    <button onclick="closeResetModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="resetForm" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
                        <p>⚠️ Password akan direset tanpa mengetahui password lama. Pastikan user mengetahui password baru ini.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Password Baru</label>
                        <input type="password" id="reset_password" name="password" placeholder="Minimal 6 karakter" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" id="reset_password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeResetModal()" class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold rounded-xl shadow-lg shadow-amber-200 transition-all text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================================================================
    JAVASCRIPT
    ================================================================ -->
    <script>
        // EDIT MODAL
        function openEditModal(userId) {
            fetch(`/users/${userId}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_username').value = data.username;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_full_name').value = data.full_name;
                    document.getElementById('edit_role_id').value = data.role_id;
                    document.getElementById('edit_status').value = data.status;
                    document.getElementById('editForm').action = `/users/${userId}`;
                    document.getElementById('editModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(() => alert('❌ Gagal mengambil data user'));
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // RESET MODAL
        function openResetModal(userId) {
            document.getElementById('resetForm').action = `/users/${userId}/reset-password`;
            document.getElementById('reset_password').value = '';
            document.getElementById('reset_password_confirmation').value = '';
            document.getElementById('resetModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ESC KEY
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeEditModal();
                closeResetModal();
            }
        });
    </script>
</x-app-layout>
