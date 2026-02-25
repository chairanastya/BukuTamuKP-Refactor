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

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .input-wrapper>.flex-shrink-0 {
            margin-left: 10px;
        }
    </style>
@endpush
@section('content')
    <x-auth-background />

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
                <div class="mb-4 bg-red-50 border-2 border-red-600 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-none text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start">
                                @svg('heroicon-o-x-circle', 'w-4 h-4 mr-2 flex-shrink-0 mt-0.5')
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-4 bg-green-50 border-2 border-green-600 text-green-700 px-4 py-3 rounded-lg">
                    <p class="text-sm flex items-start">
                        @svg('heroicon-o-check-circle', 'w-4 h-4 mr-2 flex-shrink-0 mt-0.5')
                        <span>{{ session('status') }}</span>
                    </p>
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('resepsionis.password.email') }}" data-init="forgot-password">
                @csrf

                <!-- Email Input -->
                <div class="mb-6">
                    <x-input-wrapper id="email" name="email" type="email" placeholder="Email Resepsionis"
                        :value="old('email')" :error="$errors->first('email')"
                        errorMessage="Email wajib diisi dengan format yang benar" :showLabel="false" :required="true">
                        <x-slot:prepend>
                            @svg('heroicon-s-envelope', 'w-6 h-6 text-[#084E8F]')
                        </x-slot:prepend>
                    </x-input-wrapper>
                </div>

                <x-recaptcha class="mb-6" />

                <!-- Submit Button -->
                <x-button type="submit" variant="primary" class="w-full py-3 shadow-lg hover:shadow-xl" icon="">
                    Kirim Link Reset Password
                </x-button>

                <!-- Back to Login Link -->
                <div class="mt-6 text-center">
                    <x-button href="{{ route('resepsionis.login') }}" variant="primary" icon="heroicon-o-arrow-left"
                        iconClass="w-4 h-4"
                        class="!bg-transparent !text-gray-600 hover:!text-blue-700 !font-normal !px-0 !py-0 hover:underline">
                        Kembali ke Login
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[data-init="forgot-password"]');
            if (!form) return;

            if (typeof initInputBackgrounds === 'function') {
                initInputBackgrounds('.input-wrapper input');
            }
        });
    </script>
@endpush