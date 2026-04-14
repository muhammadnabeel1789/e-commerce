<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-600 flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-red-600/80">
            {{ __('PERINGATAN: Tindakan ini tidak dapat dibatalkan. Semua data akan hilang permanen.') }}
        </p>
    </header>

    <div class="flex justify-end">
        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" 
        class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg shadow hover:bg-red-700 transition">
            {{ __('Hapus Akun Saya') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-gray-900">
                {{ __('Apakah Anda yakin?') }}
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                {{ __('Setelah akun dihapus, semua data (riwayat belanja, profil, dll) akan hilang selamanya. Masukkan password Anda untuk konfirmasi.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 transition"
                           placeholder="Masukkan Password Anda" />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg shadow hover:bg-red-700 transition">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>