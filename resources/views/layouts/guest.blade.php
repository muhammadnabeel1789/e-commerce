<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --primary: #3b82f6;
                --primary-dark: #2563eb;
            }
            
            .bg-gradient-auth {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .auth-card {
                background: white;
                border-radius: 1rem;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-auth">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo -->
            <div class="mb-8">
                <a href="{{ route('home') }}" class="flex items-center justify-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-shopping-bag text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-white">
                        {{ config('app.name', 'ShopNow') }}
                    </span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 auth-card">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                        <div class="font-medium text-red-600">
                            {{ __('Whoops! Something went wrong.') }}
                        </div>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
                
                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="mt-8 text-center text-white text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'ShopNow') }}. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>