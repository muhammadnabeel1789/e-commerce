<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Toko & Ongkir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- 1. Informasi Toko --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-8">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center gap-2 mb-6 border-b pb-4">
                            <i class="fas fa-store text-indigo-600"></i>
                            <h3 class="text-lg font-bold">Informasi Toko</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Logo Toko (Opsional)
                                </label>
                                <div class="flex items-center gap-4">
                                    @if(isset($shop_logo) && $shop_logo)
                                        <img src="{{ asset($shop_logo) }}" alt="Logo" class="w-16 h-16 object-contain border rounded-xl p-2 bg-white shadow-sm">
                                    @else
                                        <div class="w-16 h-16 flex items-center justify-center border rounded-xl bg-gray-50 text-gray-400 shadow-sm">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    @endif
                                    <input type="file" id="shop_logo" name="shop_logo" accept="image/*"
                                           class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm" >
                                </div>
                                <p class="text-[10.5px] text-gray-400 mt-2 uppercase font-bold tracking-wide">Format: JPG, PNG, WEBP, SVG (Maks 2MB). Kosongkan jika tidak ingin mengubah.</p>
                            </div>

                            <div class="col-span-1 md:col-span-2 border-t pt-4 mt-2">
                                <label for="shop_name" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Nama Toko
                                </label>
                                <input type="text" id="shop_name" name="shop_name" 
                                       value="{{ old('shop_name', $shop_name) }}" 
                                       class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 font-bold" 
                                       placeholder="Contoh: Fashion Store" required>
                            </div>

                            <div>
                                <label for="shop_phone" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    No. Telepon / WA
                                </label>
                                <input type="text" id="shop_phone" name="shop_phone" 
                                       value="{{ old('shop_phone', $shop_phone) }}" 
                                       class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" 
                                       placeholder="Contoh: 08123456789">
                            </div>

                            <div>
                                <label for="shop_email" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Email Toko
                                </label>
                                <input type="email" id="shop_email" name="shop_email" 
                                       value="{{ old('shop_email', $shop_email) }}" 
                                       class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" 
                                       placeholder="Contoh: admin@toko.com">
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label for="shop_address" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Alamat Toko (Pusat)
                                </label>
                                <textarea id="shop_address" name="shop_address" rows="3"
                                          class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" 
                                          placeholder="Contoh: Jl. Merdeka No. 123, Depok, Jawa Barat">{{ old('shop_address', $shop_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Pengaturan Ongkir --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center gap-2 mb-6 border-b pb-4">
                            <i class="fas fa-truck text-indigo-600"></i>
                            <h3 class="text-lg font-bold">Tarif Ongkos Kirim</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="ongkir_per_km" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Harga Ongkir per KM
                                </label>
                                <div class="flex items-center gap-2">
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" id="ongkir_per_km" name="ongkir_per_km" 
                                               value="{{ old('ongkir_per_km', $ongkir_per_km) }}" 
                                               class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 font-bold" 
                                               required>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 uppercase">/ KM</span>
                                </div>
                            </div>

                            <div>
                                <label for="ongkir_per_gram" class="block mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Harga Ongkir per Gram
                                </label>
                                <div class="flex items-center gap-2">
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" id="ongkir_per_gram" name="ongkir_per_gram" 
                                               value="{{ old('ongkir_per_gram', $ongkir_per_gram) }}" 
                                               class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 font-bold" 
                                               required>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 uppercase">/ Gram</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 flex gap-3">
                            <i class="fas fa-info-circle text-indigo-400 mt-1"></i>
                            <p class="text-xs text-indigo-800 leading-relaxed uppercase tracking-wide font-medium">
                                Rumus Perhitungan:<br>
                                <span class="font-black">(Jarak KM × Harga/KM) + (Berat Gram × Harga/Gram)</span>
                            </p>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm">
                                <i class="fas fa-save"></i> SIMPAN PERUBAHAN KONFIGURASI
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</x-app-layout>