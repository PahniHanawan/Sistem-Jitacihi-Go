<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Jitanichi Go SPK') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800" x-data="{ sidebarOpen: false }">
        <div class="flex h-screen overflow-hidden">

            <!-- Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden"
                 @click="sidebarOpen = false"
                 aria-hidden="true"></div>

            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Header -->
                <header class="bg-white border-b border-slate-200/80 z-30 sticky top-0">
                    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                        <div class="flex items-center gap-4">
                            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 lg:hidden p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            
                            <div class="hidden sm:block">
                                @isset($header)
                                    {{ $header }}
                                @endisset
                            </div>

                            <!-- Breadcrumb kecil untuk mobile -->
                            @isset($header)
                                <div class="sm:hidden text-sm font-semibold text-slate-700 truncate max-w-[120px]">
                                    {!! strip_tags($header) !!}
                                </div>
                            @endisset
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="flex items-center gap-3">
                            <!-- Notifikasi / Info -->
                            <div class="hidden sm:flex items-center gap-1 text-xs text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ optional(\App\Models\AssessmentPeriod::where('status', 'aktif')->first())->period_name ?? 'Tidak ada periode aktif' }}
                            </div>

                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2 p-1.5 rounded-full border border-transparent hover:border-slate-200 hover:bg-slate-50 transition-all focus:outline-none group">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-indigo-200 group-hover:scale-105 transition-transform">
                                            {{ substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="hidden sm:flex flex-col items-start text-left">
                                            <span class="text-sm font-semibold text-slate-700 leading-tight">{{ auth()->user()->full_name ?? auth()->user()->username }}</span>
                                            <span class="text-xs font-medium text-slate-400 leading-tight">{{ auth()->user()->email }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 ms-1 hidden sm:block group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')" class="text-slate-700 font-medium">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profil Akun
                                        </div>
                                    </x-dropdown-link>
                                    
                                    <div class="border-t border-slate-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="confirmLogout(event, document.getElementById('logoutForm'))" class="text-rose-600 font-medium hover:bg-rose-50">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Log Out
                                            </div>
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Main Slot -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gradient-to-b from-slate-50 to-white">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: "{{ session('success') }}",
                                    icon: 'success',
                                    confirmButtonColor: '#4f46e5',
                                });
                            });
                        </script>
                    @endif
                    
                    @if (session('error'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: "{{ session('error') }}",
                                    icon: 'error',
                                    confirmButtonColor: '#4f46e5',
                                });
                            });
                        </script>
                    @endif

                    @if (session('warning'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Perhatian!',
                                    text: "{{ session('warning') }}",
                                    icon: 'warning',
                                    confirmButtonColor: '#4f46e5',
                                });
                            });
                        </script>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const message = form.getAttribute('data-confirm-message') || 'Yakin ingin menghapus data ini?';
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
            
            function confirmLogout(event, form) {
                event.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Yakin ingin keluar dari aplikasi?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    </body>
</html>