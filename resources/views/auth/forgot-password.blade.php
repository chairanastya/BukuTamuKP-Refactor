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

        .circle-1 {
            width: 275px;
            height: 275px;
            background: linear-gradient(180deg, rgba(255, 227, 102, 0.00) 0%, rgba(255, 227, 102, 0.70) 100%);
            -webkit-mask: conic-gradient(from 90deg, transparent 0deg 45deg, black 45deg 360deg);
            mask: conic-gradient(from 90deg, transparent 0deg 45deg, black 45deg 360deg);
            border-radius: 50%;
            bottom: -40px;
            left: -80px;
        }

        .circle-2 {
            width: 450px;
            height: 450px;
            background: linear-gradient(180deg, rgba(247, 178, 24, 0.00) 0%, rgba(247, 178, 24, 0.70) 100%);
            top: 2px;
            right: 100px;
        }

        .donut {
            position: absolute;
            border-radius: 50%;
            -webkit-mask: radial-gradient(transparent 0, transparent 110px, black 110px);
            mask: radial-gradient(transparent 0, transparent 110px, black 110px);
        }

        .donut-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(-50deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%);
            top: -5%;
            left: 15%;
        }

        .donut-2 {
            width: 275px;
            height: 275px;
            background: linear-gradient(75deg, rgba(247, 178, 24, 0.70) 0%, rgba(145, 104, 14, 0.70) 100%);
            bottom: -15%;
            right: -5%;
        }

        .donut-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(-45deg, rgba(255, 227, 102, 0.38) 0%, rgba(95, 129, 161, 0.38) 52.4%, rgba(71, 185, 174, 0.38) 100%);
            -webkit-mask: radial-gradient(transparent 0, transparent 60px, black 60px);
            mask: radial-gradient(transparent 0, transparent 60px, black 60px);
            bottom: 1%;
            right: 15%;
        }

        .dots-pattern {
            position: absolute;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 40px;
            opacity: 0.2;
        }

        .dots-pattern-top {
            top: 20%;
            left: 2.5%;
        }

        .dots-pattern-bottom {
            bottom: 5%;
            left: 15%;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }

        .arrows {
            position: absolute;
            right: 1%;
            top: 40%;
            opacity: 0.15;
        }

        .arrow {
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-right: 40px solid white;
            margin: 10px;
        }

        .input-wrapper-forgot {
            border: 2px solid #084E8F;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            background-color: #F9FCFF;
            display: flex;
            align-items: center;
        }

        .input-wrapper-forgot.filled {
            background-color: white;
        }

        .input-wrapper-forgot:focus-within {
            box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.1);
        }

        .input-wrapper-forgot input {
            background-color: transparent;
            flex: 1;
        }
    </style>
@endpush
@section('content')
    <div class="bg-pattern">
        <div class="donut donut-3"></div>
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="donut donut-1"></div>
        <div class="donut donut-2"></div>

        <div class="dots-pattern dots-pattern-top">
            @for ($i = 0; $i < 40; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <div class="dots-pattern dots-pattern-bottom">
            @for ($i = 0; $i < 16; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <div class="arrows inline-flex">
            <div class="arrow"></div>
            <div class="arrow"></div>
            <div class="arrow"></div>
            <div class="arrow"></div>
        </div>
    </div>

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
                    <div class="input-wrapper-forgot">
                        @svg('heroicon-s-envelope', 'w-6 h-6 text-[#084E8F] mr-3')
                        <input type="email" name="email" id="email" placeholder="Email Resepsionis"
                            value="{{ old('email') }}" class="flex-1 border-0 outline-none text-gray-700" required
                            autofocus>
                    </div>
                </div>

                <x-recaptcha class="mb-6" />

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                    Kirim Link Reset Password
                </button>

                <!-- Back to Login Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('resepsionis.login') }}"
                        class="text-sm text-gray-600 hover:text-blue-700 hover:underline inline-flex items-center">
                        @svg('heroicon-o-arrow-left', 'w-4 h-4 mr-1')
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateInputBackground(input) {
                const wrapper = input.closest('.input-wrapper-forgot');
                if (wrapper) {
                    wrapper.classList.toggle('filled', input.value.trim() !== '');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const inputs = document.querySelectorAll('.input-wrapper-forgot input');
                inputs.forEach(input => {
                    updateInputBackground(input);
                    input.addEventListener('input', () => updateInputBackground(input));
                    input.addEventListener('change', () => updateInputBackground(input));
                });
            });
        </script>
    @endpush
@endsection