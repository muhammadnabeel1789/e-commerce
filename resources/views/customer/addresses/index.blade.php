<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Alamat Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Alamat Tersimpan</h3>
                    <button x-data="" x-on:click="$dispatch('open-modal', 'create-address-modal')" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                        + Tambah Alamat Baru
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($addresses as $address)
                        <div class="border rounded-lg p-4 relative hover:shadow-md transition {{ $address->is_default ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            @if($address->is_default)
                                <span class="absolute top-2 right-2 px-2 py-1 bg-indigo-600 text-white text-xs rounded-full">Utama</span>
                            @endif
                            
                            <h4 class="font-bold text-gray-800">{{ $address->label }}</h4>
                            <p class="text-sm font-semibold mt-1">{{ $address->recipient_name }} ({{ $address->phone }})</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $address->address }}<br>
                                Kel. {{ $address->village }}, Kec. {{ $address->district }}<br>
                                {{ $address->city }}, {{ $address->province }} - {{ $address->postal_code }}
                            </p>

                            <div class="mt-4 flex gap-3 text-sm">
                                <a href="{{ route('addresses.edit', $address->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                
                                <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>

                                @if(!$address->is_default)
                                    <form action="{{ route('addresses.setDefault', $address->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-500 hover:text-indigo-600">Jadikan Utama</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-2 text-center py-4">Belum ada alamat tersimpan.</p>
                    @endforelse
                </div>
            </div>

            <x-modal name="create-address-modal" :show="$errors->isNotEmpty()" focusable>
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Tambah Alamat Baru</h2>
                    
                    <form action="{{ route('addresses.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="label" :value="__('Label Alamat (Rumah/Kantor)')" />
                                <x-text-input id="label" class="block mt-1 w-full" type="text" name="label" required placeholder="Contoh: Rumah" />
                            </div>
                            <div>
                                <x-input-label for="recipient_name" :value="__('Nama Penerima')" />
                                <x-text-input id="recipient_name" class="block mt-1 w-full" type="text" name="recipient_name" value="{{ Auth::user()->name }}" required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="phone" :value="__('Nomor HP')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" required placeholder="08xxxxxxxx" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="select_province" :value="__('Provinsi')" />
                                <select id="select_province" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Provinsi...</option>
                                </select>
                                <input type="hidden" name="province" id="input_province">
                            </div>

                            <div>
                                <x-input-label for="select_city" :value="__('Kota/Kabupaten')" />
                                <select id="select_city" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                    <option value="">Pilih Kota...</option>
                                </select>
                                <input type="hidden" name="city" id="input_city">
                            </div>

                            <div>
                                <x-input-label for="select_district" :value="__('Kecamatan')" />
                                <select id="select_district" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                    <option value="">Pilih Kecamatan...</option>
                                </select>
                                <input type="hidden" name="district" id="input_district">
                            </div>

                            <div>
                                <x-input-label for="select_village" :value="__('Kelurahan/Desa')" />
                                <select id="select_village" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                    <option value="">Pilih Kelurahan/Desa...</option>
                                </select>
                                <input type="hidden" name="village" id="input_village">
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="postal_code" :value="__('Kode Pos')" />
                                <x-text-input id="postal_code" class="block mt-1 w-full md:w-1/2" type="number" name="postal_code" required />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="address" :value="__('Alamat Lengkap (Jalan, No. Rumah, RT/RW)')" />
                            <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">
                                Batal
                            </button>
                            <x-primary-button>{{ __('Simpan Alamat') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const provinceSelect = document.getElementById('select_province');
            const citySelect = document.getElementById('select_city');
            const districtSelect = document.getElementById('select_district');
            const villageSelect = document.getElementById('select_village');
            
            // Input Hidden (Yang akan dikirim ke database)
            const provinceInput = document.getElementById('input_province');
            const cityInput = document.getElementById('input_city');
            const districtInput = document.getElementById('input_district');
            const villageInput = document.getElementById('input_village');

            // Helper function untuk fetch data API
            const fetchData = async (url) => {
                try {
                    const response = await fetch(url);
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return [];
                }
            };

            // 1. Load Provinsi saat halaman dibuka
            provinceSelect.innerHTML = '<option value="">Memuat Provinsi...</option>';
            const provinces = await fetchData('/api/regions/provinces');
            
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
            provinces.forEach(province => {
                let option = new Option(province.name, province.code);
                option.setAttribute('data-name', province.name);
                provinceSelect.add(option);
            });

            // 2. Saat Provinsi Dipilih
            provinceSelect.addEventListener('change', async function () {
                const code = this.value;
                provinceInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';

                // Reset Kota, Kecamatan, Kelurahan
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
                citySelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                villageSelect.disabled = true;
                
                cityInput.value = ''; districtInput.value = ''; villageInput.value = '';

                if (code) {
                    citySelect.disabled = false;
                    citySelect.innerHTML = '<option value="">Memuat Kota...</option>';
                    const cities = await fetchData(`/api/regions/cities/${code}`);
                    
                    citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
                    cities.forEach(city => {
                        let option = new Option(city.name, city.code);
                        option.setAttribute('data-name', city.name);
                        citySelect.add(option);
                    });
                }
            });

            // 3. Saat Kota Dipilih
            citySelect.addEventListener('change', async function () {
                const code = this.value;
                cityInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';

                // Reset Kecamatan, Kelurahan
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                villageSelect.disabled = true;

                districtInput.value = ''; villageInput.value = '';

                if (code) {
                    districtSelect.disabled = false;
                    districtSelect.innerHTML = '<option value="">Memuat Kecamatan...</option>';
                    const districts = await fetchData(`/api/regions/districts/${code}`);
                    
                    districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                    districts.forEach(district => {
                        let option = new Option(district.name, district.code);
                        option.setAttribute('data-name', district.name);
                        districtSelect.add(option);
                    });
                }
            });

            // 4. Saat Kecamatan Dipilih
            districtSelect.addEventListener('change', async function () {
                const code = this.value;
                districtInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';

                // Reset Kelurahan
                villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                villageSelect.disabled = true;
                villageInput.value = '';

                if (code) {
                    villageSelect.disabled = false;
                    villageSelect.innerHTML = '<option value="">Memuat Kelurahan...</option>';
                    const villages = await fetchData(`/api/regions/villages/${code}`);
                    
                    villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                    villages.forEach(village => {
                        let option = new Option(village.name, village.code);
                        option.setAttribute('data-name', village.name);
                        villageSelect.add(option);
                    });
                }
            });

            // 5. Saat Kelurahan Dipilih
            villageSelect.addEventListener('change', function () {
                if(this.value) {
                    villageInput.value = this.options[this.selectedIndex].getAttribute('data-name');
                } else {
                    villageInput.value = '';
                }
            });
        });
    </script>
</x-app-layout>