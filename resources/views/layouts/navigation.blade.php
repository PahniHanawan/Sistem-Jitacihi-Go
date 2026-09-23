<!-- Sidebar Component -->
<nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-xl lg:shadow-none">

    <!-- Sidebar Header (Logo) -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-200/80 shrink-0">
        <div class="w-8 h-8 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0 shadow-sm shadow-indigo-200">
            <span class="text-white font-bold text-sm">JG</span>
        </div>
        <div>
            <h1 class="text-base font-bold text-slate-800 tracking-tight leading-none">Jitanichi <span class="text-indigo-600">Go</span></h1>
            <p class="text-[10px] font-semibold text-slate-400 mt-0.5 tracking-wider uppercase">SPK Promo</p>
        </div>
    </div>

    <!-- Sidebar Links -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 scrollbar-thin scrollbar-thumb-slate-200">

        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </x-nav-link>

        <!-- Section: Master Data -->
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Master Data</p>
        </div>

        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Produk
        </x-nav-link>

        <x-nav-link :href="route('criteria.index')" :active="request()->routeIs('criteria.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('criteria.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Kriteria
        </x-nav-link>

        <x-nav-link :href="route('periods.index')" :active="request()->routeIs('periods.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('periods.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Periode
        </x-nav-link>

        <x-nav-link :href="route('assessments.index')" :active="request()->routeIs('assessments.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('assessments.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Penilaian Produk
        </x-nav-link>

        @if(auth()->user()->isOwner())
        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Pengguna
        </x-nav-link>
        @endif

        <!-- Section: Kalkulasi SPK -->
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kalkulasi SPK</p>
        </div>

        <!-- AHP (BOBOT) → HANYA OWNER -->
        @if(auth()->user()->isOwner())
        <x-nav-link :href="route('ahp.index')" :active="request()->routeIs('ahp.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('ahp.*') ? 'text-violet-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="{{ request()->routeIs('ahp.*') ? 'text-violet-700' : '' }}">AHP (Bobot)</span>
        </x-nav-link>
        @endif

        <!-- SAW (RANKING) → ADMIN & OWNER -->
        <x-nav-link :href="route('saw.index')" :active="request()->routeIs('saw.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('saw.*') ? 'text-amber-500' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <span class="{{ request()->routeIs('saw.*') ? 'text-amber-600' : '' }}">SAW (Ranking)</span>
        </x-nav-link>

        <!-- KEPUTUSAN PROMOSI → HANYA OWNER -->
        @if(auth()->user()->isOwner())
        <x-nav-link :href="route('decisions.index')" :active="request()->routeIs('decisions.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('decisions.*') ? 'text-emerald-500' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span class="{{ request()->routeIs('decisions.*') ? 'text-emerald-600' : '' }}">Keputusan Promosi</span>
        </x-nav-link>
        @endif
        <!-- Section: Laporan -->
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Laporan</p>
        </div>

        <!-- LAPORAN → ADMIN & OWNER -->
        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Laporan
        </x-nav-link>

        <!-- Sidebar Footer -->
        <div class="pt-4 pb-2 px-3 mt-auto border-t border-slate-200/80 -mx-3 px-3 pt-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center shrink-0 text-white font-bold text-sm shadow-sm shadow-indigo-200">
                    {{ substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->full_name ?? auth()->user()->username }}</p>
                    <p class="text-xs font-medium text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ auth()->user()->isOwner() ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ auth()->user()->isOwner() ? 'OWNER' : 'ADMIN' }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                    Online
                </span>
            </div>
        </div>
    </div>
</nav>
