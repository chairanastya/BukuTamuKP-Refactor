<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- Page Header with Gradient Background -->
        @if(View::hasSection('header'))
            <header class="fixed top-0 left-0 right-0 z-50" style="background: linear-gradient(#0C4777 17.8%, #47B9AE 100%);">
                <!-- Background Pattern -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <!-- Circles -->
                    <div class="absolute w-96 h-96 rounded-full opacity-70" 
                         style="background: linear-gradient(180deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%); top: -100px; left: -50px;"></div>
                    
                    <div class="absolute w-[500px] h-[500px] rounded-full" 
                         style="background: linear-gradient(180deg, rgba(247, 178, 24, 0.00) 0%, rgba(247, 178, 24, 0.70) 100%); top: 10px; right: 100px;"></div>

                    <!-- Donuts -->
                    <div class="absolute w-72 h-72 rounded-full opacity-70" 
                         style="background: linear-gradient(180deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%); 
                                -webkit-mask: radial-gradient(transparent 0, transparent 110px, black 110px); 
                                mask: radial-gradient(transparent 0, transparent 110px, black 110px); 
                                top: 10%; left: 5%;"></div>

                    <!-- Dots Pattern -->
                    <div class="absolute grid grid-cols-8 gap-5 opacity-20" style="top: 20%; left: 10%;">
                        @for ($i = 0; $i < 16; $i++)
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        @endfor
                    </div>

                    <!-- Arrows -->
                    <div class="absolute right-[5%] top-[40%] opacity-15">
                        @for ($j = 0; $j < 3; $j++)
                            <div class="inline-block my-2.5 border-l-[40px] border-l-white border-t-[30px] border-t-transparent border-b-[30px] border-b-transparent"></div>
                        @endfor
                    </div>
                </div>

                <div class="relative max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h1 class="text-3xl font-extrabold text-white">
                            @yield('header')
                        </h1>
                        @if(View::hasSection('header-action'))
                            <div class="text-white hover:bg-blue-50 hover:bg-opacity-20 px-4 py-2 rounded-lg font-semibold transition duration-200">
                                @yield('header-action')
                            </div>
                        @endif
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        @yield('content')
        @stack('scripts')
    </body>
</html>
