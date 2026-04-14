<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Alamat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('addresses.update', $address->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <x-input-label for="label" :value="__('Label Alamat')" />
                            <x-text-input id="label" class="block mt-1 w-full" type="text" name="label" :value="old('label', $address->label)" required />
                        </div>

                        <div>
                            <x-input-label for="recipient_name" :value="__('Nama Penerima')" />
                            <x-text-input id="recipient_name" class="block mt-1 w-full" type="text" name="recipient_name" :value="old('recipient_name', $address->recipient_name)" required />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Nomor Telepon')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $address->phone)" required />
                        </div>

                        <div class="col-span-2">
                            <x-input-label for="address" :value="__('Alamat Lengkap')" />
                            <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required>{{ old('address', $address->address) }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="select_province" :value="__('Provinsi')" />
                            <select id="select_province" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Memuat Provinsi...</option>
                            </select>
                            <input type="hidden" name="province" id="input_province" value="{{ old('province', $address->province) }}">
                        </div>

                        <div>
                            <x-input-label for="select_city" :value="__('Kota/Kabupaten')" />
                            <select id="select_city" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                <option value="">Pilih Kota...</option>
                            </select>
                            <input type="hidden" name="city" id="input_city" value="{{ old('city', $address->city) }}">
                        </div>

                        <div>
                            <x-input-label for="select_district" :value="__('Kecamatan')" />
                            <select id="select_district" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                <option value="">Pilih Kecamatan...</option>
                            </select>
                            <input type="hidden" name="district" id="input_district" value="{{ old('district', $address->district) }}">
                        </div>

                        <div>
                            <x-input-label for="select_village" :value="__('Kelurahan/Desa')" />
                            <select id="select_village" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                                <option value="">Pilih Kelurahan/Desa...</option>
                            </select>
                            <input type="hidden" name="village" id="input_village" value="{{ old('village', $address->village) }}">
                        </div>

                        <div>
                            <x-input-label for="postal_code" :value="__('Kode Pos')" />
                            <x-text-input id="postal_code" class="block mt-1 w-full" type="number" name="postal_code" :value="old('postal_code', $address->postal_code)" required />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">
                            Batal
                        </a>
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const provinceSelect = document.getElementById('select_province');
            const citySelect = document.getElementById('select_city');
            const districtSelect = document.getElementById('select_district');
            const villageSelect = document.getElementById('select_village');

            const provinceInput = document.getElementById('input_province');
            const cityInput = document.getElementById('input_city');
            const districtInput = document.getElementById('input_district');
            const villageInput = document.getElementById('input_village');

            // Data alamat yang sudah tersimpan
            const oldProvince = provinceInput.value;
            const oldCity = cityInput.value;
            const oldDistrict = districtInput.value;
            const oldVillage = villageInput.value;

            // Helper fetch data API
            const fetchData = async (url) => {
                try {
                    const response = await fetch(url);
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return [];
                }
            };

            // 1. Auto-Load Data Berdasarkan Data Lama
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
            const provinces = await fetchData('/api/regions/provinces');
            let selectedProvCode = null;

            provinces.forEach(prov => {
                let option = new Option(prov.name, prov.code);
                option.setAttribute('data-name', prov.name);
                // Jika nama provinsi sama dengan database, set terpilih
                if (prov.name === oldProvince) {
                    option.selected = true;
                    selectedProvCode = prov.code;
                }
                provinceSelect.add(option);
            });

            if (selectedProvCode) {
                citySelect.disabled = false;
                const cities = await fetchData(`/api/regions/cities/${selectedProvCode}`);
                
                let selectedCityCode = null;
                cities.forEach(city => {
                    let option = new Option(city.name, city.code);
                    option.setAttribute('data-name', city.name);
                    if (city.name === oldCity) {
                        option.selected = true;
                        selectedCityCode = city.code;
                    }
                    citySelect.add(option);
                });

                if (selectedCityCode) {
                    districtSelect.disabled = false;
                    const districts = await fetchData(`/api/regions/districts/${selectedCityCode}`);
                    
                    let selectedDistCode = null;
                    districts.forEach(dist => {
                        let option = new Option(dist.name, dist.code);
                        option.setAttribute('data-name', dist.name);
                        if (dist.name === oldDistrict) {
                            option.selected = true;
                            selectedDistCode = dist.code;
                        }
                        districtSelect.add(option);
                    });

                    if (selectedDistCode) {
                        villageSelect.disabled = false;
                        const villages = await fetchData(`/api/regions/villages/${selectedDistCode}`);
                        
                        villages.forEach(vill => {
                            let option = new Option(vill.name, vill.code);
                            option.setAttribute('data-name', vill.name);
                            if (vill.name === oldVillage) {
                                option.selected = true;
                            }
                            villageSelect.add(option);
                        });
                    }
                }
            }

            // 2. Event Listeners: Jika user mengganti dropdown secara manual

            provinceSelect.addEventListener('change', async function () {
                const code = this.value;
                provinceInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                
                // Reset dropdown turunan
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

            citySelect.addEventListener('change', async function () {
                const code = this.value;
                cityInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                
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
                    districts.forEach(dist => {
                        let option = new Option(dist.name, dist.code);
                        option.setAttribute('data-name', dist.name);
                        districtSelect.add(option);
                    });
                }
            });

            districtSelect.addEventListener('change', async function () {
                const code = this.value;
                districtInput.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';

                villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                villageSelect.disabled = true;
                villageInput.value = '';

                if (code) {
                    villageSelect.disabled = false;
                    villageSelect.innerHTML = '<option value="">Memuat Kelurahan...</option>';
                    const villages = await fetchData(`/api/regions/villages/${code}`);
                    
                    villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                    villages.forEach(vill => {
                        let option = new Option(vill.name, vill.code);
                        option.setAttribute('data-name', vill.name);
                        villageSelect.add(option);
                    });
                }
            });

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