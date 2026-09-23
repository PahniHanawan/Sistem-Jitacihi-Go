<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-rose-800">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ __('Setelah akun dihapus, seluruh data akan hilang permanen.') }}
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-lg shadow-rose-200 font-bold transition-all">
        {{ __('Hapus Akun') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-800">
                {{ __('Yakin ingin menghapus akun?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ __('Masukkan password untuk konfirmasi.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4 rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500" placeholder="{{ __('Password') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-4 py-2.5 rounded-xl border-slate-200 font-bold">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-lg shadow-rose-200 font-bold transition-all">
                    {{ __('Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>