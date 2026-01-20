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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Sidebar Styles -->
    <style>
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 100px;
            background: linear-gradient(#46B8AD 20%, #0C4777 100%);
            z-index: 30;
            display: flex;
            flex-direction: column;
            padding-top: 116px;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem 1rem;
            color: white;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            background: #F7B218;
        }

        .sidebar-item svg {
            width: 32px;
            height: 32px;
            margin-bottom: 0.5rem;
        }

        .sidebar-item span {
            text-align: center;
        }

        .main-content {
            margin-left: 100px;
            padding-top: 80px;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        .menu-toggle svg {
            width: 28px;
            height: 28px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 25;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
            
            header {
                max-height: 120px;
            }
            
            header h1 {
                font-size: 1.5rem !important;
                line-height: 1.2 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Page Header with Gradient Background -->
    @if(View::hasSection('header'))
        <header class="fixed top-0 left-0 right-0 z-50" style="background: linear-gradient(#0C4777 17.8%, #47B9AE 100%);">
            <!-- Background Pattern -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <!-- Circles -->
                <div class="absolute w-96 h-96 rounded-full opacity-80"
                    style="background: linear-gradient(180deg, rgba(247, 178, 24, 0.00) 0%, rgba(247, 178, 24, 0.70) 100%); top: -100px; left: 35vw;">
                </div>

                <!-- Donuts -->
                <div class="absolute w-44 h-44 rounded-full opacity-90" style="background: linear-gradient(-45deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%); 
                                    -webkit-mask: radial-gradient(transparent 0, transparent 70px, black 70px); 
                                    mask: radial-gradient(transparent 0, transparent 70px, black 70px); 
                                    top: -45%; left: 1%;"></div>
                
                <div class="absolute w-40 h-40 rounded-full opacity-90" style="background: linear-gradient(-45deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%); 
                                    -webkit-mask: radial-gradient(transparent 0, transparent 40px, black 40px); 
                                    mask: radial-gradient(transparent 0, transparent 40px, black 40px); 
                                    bottom: 1%; right: 8%;"></div>

                <div class="absolute w-44 h-44 rounded-full opacity-100" style="background: linear-gradient(-45deg, rgba(247, 178, 24, 0.70) 0%, rgba(145, 104, 14, 0.70) 100%); 
                                    -webkit-mask: radial-gradient(transparent 0, transparent 70px, black 70px); 
                                    mask: radial-gradient(transparent 0, transparent 70px, black 70px); 
                                    top: -25%; right: -2%;"></div>

                <!-- Dots Pattern -->
                <div class="absolute grid grid-cols-8 gap-4 opacity-20" style="top: 30%; right: 28vw;">
                    @for ($i = 0; $i < 24; $i++)
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                    @endfor
                </div>

                <!-- Arrows -->
                <div class="absolute left-[30%] top-[-5%] opacity-15">
                    @for ($j = 0; $j < 4; $j++)
                        <div
                            class="inline-block my-2.5 border-r-[35px] border-r-white border-t-[20px] border-t-transparent border-b-[20px] border-b-transparent">
                        </div>
                    @endfor
                </div>
            </div>

            <div class="relative max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @auth('resepsionis')
                            <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
                                @svg('heroicon-o-bars-3', 'w-7 h-7')
                            </button>
                        @endauth
                        <h1 class="text-3xl font-extrabold text-white">
                            @yield('header')
                        </h1>
                    </div>
                    @if(View::hasSection('header-action'))
                        <div
                            class="text-white hover:bg-blue-50 hover:bg-opacity-20 px-4 py-2 rounded-lg font-semibold transition duration-200">
                            @yield('header-action')
                        </div>
                    @endif
                </div>
            </div>
        </header>
    @endif

    <!-- Sidebar (only for authenticated resepsionis) -->
    @auth('resepsionis')
        <!-- Sidebar Overlay (for mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
        
        <div class="sidebar" id="sidebar">
            @yield('sidebar')
        </div>
    @endauth

    <!-- Page Content -->
    @yield('content')

    <!-- Loading Spinner -->
    <x-loading-spinner />

    @stack('scripts')

    <!-- Sidebar Toggle Script -->
    @auth('resepsionis')
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            }

            function closeSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }

            // Close sidebar when clicking on a sidebar item on mobile
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarItems = document.querySelectorAll('.sidebar-item');
                sidebarItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            closeSidebar();
                        }
                    });
                });
            });
        </script>
    @endauth
</body>

</html>