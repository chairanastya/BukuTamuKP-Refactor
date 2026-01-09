@extends('layouts.guest')
@section('title', 'Login Resepsionis - Buku Tamu Digital')
@push('styles')
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(#0C4777 17.8%, #47B9AE 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles pattern */
        .bg-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #4db8ff, #6ab7b8);
            top: -200px;
            left: -100px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .circle-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #67b26f, #8fb569);
            bottom: -250px;
            right: -150px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .circle-3 {
            width: 300px;
            height: 300px;
            border: 40px solid rgba(255, 255, 255, 0.15);
            background: transparent;
            top: 10%;
            left: 5%;
        }

        .circle-4 {
            width: 250px;
            height: 250px;
            border: 35px solid rgba(255, 255, 255, 0.15);
            background: transparent;
            bottom: 15%;
            left: 15%;
        }

        /* Dots pattern */
        .dots-pattern {
            position: absolute;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 20px;
            opacity: 0.2;
        }

        .dots-pattern-top {
            top: 20%;
            left: 10%;
        }

        .dots-pattern-bottom {
            bottom: 25%;
            right: 10%;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Arrow pattern */
        .arrows {
            position: absolute;
            right: 5%;
            top: 40%;
            opacity: 0.15;
        }

        .arrow {
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 40px solid white;
            margin: 10px;
        }
    </style>
@endpush
@section('content')
    <!-- Background Pattern -->
    <div class="bg-pattern">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>

        <!-- Dots Pattern Top -->
        <div class="dots-pattern dots-pattern-top">
            @for ($i = 0; $i < 16; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <!-- Dots Pattern Bottom -->
        <div class="dots-pattern dots-pattern-bottom">
            @for ($i = 0; $i < 16; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <!-- Arrow Pattern -->
        <div class="arrows">
            <div class="arrow"></div>
            <div class="arrow"></div>
            <div class="arrow"></div>
        </div>
    </div>

    <!-- Login Card -->
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
            <!-- Title -->
            <h1 class="text-3xl font-bold text-center text-blue-900 mb-8">
                Buku Tamu Digital
            </h1>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('resepsionis.login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-6">
                    <div
                        class="flex items-center border-2 border-blue-300 rounded-lg px-4 py-3 focus-within:border-blue-600 transition">
                        <svg class="w-6 h-6 text-blue-900 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                        </svg>
                        <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}"
                            class="flex-1 border-0 outline-none text-gray-700 placeholder-gray-400" required autofocus>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <div
                        class="flex items-center border-2 border-blue-300 rounded-lg px-4 py-3 focus-within:border-blue-600 transition">
                        <svg class="w-6 h-6 text-blue-900 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <input type="password" name="password" id="password" placeholder="Password"
                            class="flex-1 border-0 outline-none text-gray-700 placeholder-gray-400" required>
                    </div>
                </div>

                <!-- Show Password Checkbox -->
                <div class="mb-6 flex items-center justify-end">
                    <label class="flex items-center cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                        <input type="checkbox" id="showPassword"
                            class="mr-2 w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        Show Password
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                    Login
                </button>

                <!-- Forgot Password Link -->
                <div class="mt-6 text-center">
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('showPassword').addEventListener('change', function () {
                const passwordInput = document.getElementById('password');
                passwordInput.type = this.checked ? 'text' : 'password';
            });
        </script>
    @endpush
@endsection