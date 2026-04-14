<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Brand: {{ $brand->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nama Brand --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Brand</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', $brand->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi (BARU) --}}
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $brand->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status (BARU) --}}
                    <div class="mb-4">
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" id="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" {{ old('is_active', $brand->is_active) == true ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $brand->is_active) == false ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Logo Brand</label>
                        
                        @if($brand->logo)
                            <div class="my-2">
                                <img src="{{ asset('storage/' . $brand->logo) }}" class="h-20 w-auto object-contain rounded border bg-gray-50 p-2">
                                <p class="text-xs text-gray-500">Logo saat ini</p>
                            </div>
                        @endif

                        <input type="file" name="logo" id="logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah logo.</p>
                        @error('logo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Brand</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>