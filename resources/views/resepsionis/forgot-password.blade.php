@extends('layouts.guest')
@section('title', 'Lupa Password - Resepsionis')
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
        }

        .donut {
            position: absolute;
            border-radius: 50%;
            -webkit-mask: radial-gradient(transparent 0, transparent 110px, black 110px);
            mask: radial-gradient(transparent 0, transparent 110px, black 110px);
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(180deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%);
            top: -200px;
            left: -100px;
        }

        .circle-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(247, 178, 24, 0.00) 0%, rgba(247, 178, 24, 0.70) 100%);
            top: 10px;
            right: 100px;
        }

        .donut-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(180deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%);
            top: 10%;
            left: 5%;
        }

        .donut-2 {
            width: 250px;
            height: 250px;
            background: linear-gradient(-45deg, rgba(255, 227, 102, 0.38) 0%, rgba(95, 129, 161, 0.38) 52.4%, rgba(71, 185, 174, 0.38) 100%);
            bottom: 5%;
            right: 15%;
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
        <div class="donut donut-1"></div>
        <div class="donut donut-2"></div>

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

    <!-- Forgot Password Card -->
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md">
            <!-- Title -->
            <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-2">
                Lupa Password
            </h1>
            <p class="text-center text-gray-600 mb-8 text-sm">
                Masukkan email Anda dan kami akan mengirimkan link reset password
            </p>

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
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    <p class="text-sm">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('resepsionis.password.email') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-6">
                    <div
                        class="flex items-center border-2 border-blue-300 rounded-lg px-4 py-3 focus-within:border-blue-600 transition">
                        <svg class="w-6 h-6 text-blue-900 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <input type="email" name="email" id="email" placeholder="Email Resepsionis"
                            value="{{ old('email') }}"
                            class="flex-1 border-0 outline-none text-gray-700 placeholder-gray-400" required autofocus>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                    Kirim Link Reset Password
                </button>

                <!-- Back to Login Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('resepsionis.login') }}"
                        class="text-sm text-gray-600 hover:text-blue-700 hover:underline inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
