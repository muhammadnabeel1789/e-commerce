<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Toko Online') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    
    <div class="flex flex-col min-h-screen">
        
        @include('layouts.navigation')

        @include('partials.splash')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer class="bg-blue-900 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-black tracking-tighter mb-4">
                            {{ $shop_name }}
                        </h3>
                        <p class="text-blue-200 text-sm leading-relaxed">
                            Destinasi belanja terbaik untuk kebutuhan Anda. Kualitas terjamin dengan pengiriman aman dan cepat ke seluruh Indonesia.
                        </p>
                    </div>
                    
          
                    <div>
                        <h3 class="text-lg font-bold mb-4">Hubungi Kami</h3>
                        <ul class="text-blue-200 text-sm space-y-3">
                            <li class="flex items-center"><i class="fas fa-envelope w-6"></i>{{ $shop_email }}</li>
                            <li class="flex items-center"><i class="fas fa-phone w-6"></i> {{ $shop_phone }}</li>
                            <li class="flex items-center"><i class="fas fa-map-marker-alt w-6"></i> {{ $shop_address }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-blue-800 mt-8 pt-8 text-center text-sm text-blue-300">
                    &copy; {{ date('Y') }}. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
</body>
</html>