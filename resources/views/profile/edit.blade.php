<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight tracking-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Profile Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-2xl shadow-sm shadow-indigo-200">
                        {{ substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ auth()->user()->full_name ?? auth()->user()->username }}</h3>
                        <p class="text-sm font-medium text-slate-500">{{ auth()->user()->email }}</p>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ auth()->user()->isOwner() ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ strtoupper(optional(auth()->user()->role)->role_name ?? 'USER') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Profile Information -->
                <div class="p-4 sm:p-8 bg-white rounded-xl border border-slate-100">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="p-4 sm:p-8 bg-white rounded-xl border border-slate-100">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="p-4 sm:p-8 bg-white rounded-xl border border-slate-100">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>