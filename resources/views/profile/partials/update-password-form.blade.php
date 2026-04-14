<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900">
            <i class="fas fa-key text-purple-500 mr-2"></i> {{ __('Ganti Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda aman dengan menggunakan password yang kuat.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                
                <input id="current_password" name="current_password" type="password" 
                       class="w-full pl-10 pr-10 py-2 rounded-xl border border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition" 
                       autocomplete="current-password" placeholder="••••••••" />

                <button type="button" onclick="togglePassword('current_password', 'icon_current')" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600 focus:outline-none">
                    <i id="icon_current" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-key text-gray-400"></i>
                </div>
                
                <input id="new_password" name="password" type="password" 
                       class="w-full pl-10 pr-10 py-2 rounded-xl border border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition" 
                       autocomplete="new-password" placeholder="Password baru" />

                <button type="button" onclick="togglePassword('new_password', 'icon_new')" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600 focus:outline-none">
                    <i id="icon_new" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-check-circle text-gray-400"></i>
                </div>
                
                <input id="password_confirmation" name="password_confirmation" type="password" 
                       class="w-full pl-10 pr-10 py-2 rounded-xl border border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 transition" 
                       autocomplete="new-password" placeholder="Ulangi password baru" />

                <button type="button" onclick="togglePassword('password_confirmation', 'icon_confirm')" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600 focus:outline-none">
                    <i id="icon_confirm" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-semibold rounded-lg shadow-md hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium flex items-center">
                    <i class="fas fa-check mr-1"></i> {{ __('Berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
    
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // Ganti ikon jadi mata dicoret
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye"); // Kembali ke ikon mata biasa
            }
        }
    </script>
</section>