<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 leading-tight">
            {{ __('Pengaturan Akun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2 space-y-6">
                    <div class="p-4 sm:p-8 bg-white shadow-lg sm:rounded-2xl border-l-4 border-blue-500">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow-lg sm:rounded-2xl border-l-4 border-purple-500">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="p-4 sm:p-8 bg-red-50 shadow-lg sm:rounded-2xl border border-red-100">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>