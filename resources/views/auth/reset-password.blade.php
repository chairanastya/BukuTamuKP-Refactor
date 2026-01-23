@extends('layouts.guest')
@section('title', 'Reset Password - Buku Tamu Digital')
@push('styles')
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #0C4777;
            min-height: 100vh;
            position: relative;
            overflow-y: auto;
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

    <div class="relative flex items-center justify-center min-h-screen px-4 py-8">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md my-8">
            <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-2">
                Reset Password
            </h1>
            <p class="text-center text-gray-600 mb-8 text-sm">
                Masukkan password baru Anda
            </p>

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

            <div class="mb-6 bg-blue-50 border-2 border-blue-300 text-blue-800 px-4 py-3 rounded-lg">
                <p class="text-sm flex items-start">
                    @svg('heroicon-o-information-circle', 'w-4 h-4 mr-2 flex-shrink-0 mt-0.5')
                    <span>Buat password yang kuat untuk melindungi akun Anda. Gunakan kombinasi huruf besar, huruf kecil,
                        angka, dan simbol.</span>
                </p>
            </div>

            <form method="POST" action="{{ route('resepsionis.password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <x-input-wrapper id="email" name="email" label="Email" type="email" :value="$email" :readonly="true"
                        class="text-gray-600 cursor-not-allowed">
                        <x-slot:prepend>
                            @svg('heroicon-s-envelope', 'w-6 h-6 text-gray-500')
                        </x-slot:prepend>
                    </x-input-wrapper>
                </div>

                <div class="mb-4">
                    <x-input-wrapper id="password" name="password" label="Password Baru" type="password"
                        placeholder="Masukkan password baru" errorMessage="Password minimal 8 karakter" :required="true">
                        <x-slot:prepend>
                            @svg('fas-key', 'w-6 h-6 text-[#084E8F]')
                        </x-slot:prepend>
                    </x-input-wrapper>
                </div>

                <div class="mb-4">
                    <x-input-wrapper id="password_confirmation" name="password_confirmation" label="Konfirmasi Password"
                        type="password" placeholder="Masukkan ulang password" errorMessage="Konfirmasi password tidak cocok"
                        :required="true">
                        <x-slot:prepend>
                            @svg('fas-key', 'w-6 h-6 text-[#084E8F]')
                        </x-slot:prepend>
                    </x-input-wrapper>
                </div>

                <div class="mb-6 flex items-center justify-end">
                    <label class="flex items-center cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                        <input type="checkbox" id="showPassword"
                            class="mr-2 w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        Tampilkan Password
                    </label>
                </div>

                <x-button type="submit" variant="primary" class="w-full py-3 shadow-lg hover:shadow-xl" icon="">
                    Reset Password
                </x-button>

                <div class="mt-6 text-center">
                    <a href="{{ route('resepsionis.login') }}"
                        class="text-sm text-gray-600 hover:text-blue-700 hover:underline inline-flex items-center justify-center">
                        @svg('heroicon-o-arrow-left', 'w-4 h-4 mr-1')
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('showPassword').addEventListener('change', function () {
                const passwordInput = document.getElementById('password');
                const confirmInput = document.getElementById('password_confirmation');
                const type = this.checked ? 'text' : 'password';
                passwordInput.type = type;
                confirmInput.type = type;
            });

            function updateInputBackground(input) {
                const wrapper = input.closest('.input-wrapper');
                if (wrapper && !input.readOnly) {
                    wrapper.classList.toggle('filled', input.value.trim() !== '');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                const passwordInput = document.getElementById('password');
                const confirmInput = document.getElementById('password_confirmation');
                const passwordWrapper = passwordInput?.closest('.input-wrapper');
                const confirmWrapper = confirmInput?.closest('.input-wrapper');
                const passwordError = document.getElementById('password_error');
                const confirmError = document.getElementById('password_confirmation_error');

                passwordInput.addEventListener('input', function () {
                    updateInputBackground(this);
                    updatePasswordStrength();
                });

                confirmInput.addEventListener('input', function () {
                    updateInputBackground(this);
                });

                form.addEventListener('submit', function (e) {
                    let hasError = false;
                    let firstErrorElement = null;

                    passwordWrapper.classList.remove('error');
                    confirmWrapper.classList.remove('error');
                    passwordError.classList.remove('show');
                    confirmError.classList.remove('show');

                    if (!passwordInput.value?.trim() || passwordInput.value.length < 8) {
                        e.preventDefault();
                        hasError = true;
                        passwordWrapper.classList.add('error');
                        passwordError.classList.add('show');
                        if (!firstErrorElement) firstErrorElement = passwordInput;

                        setTimeout(() => {
                            passwordError.classList.remove('show');
                            passwordWrapper.classList.remove('error');
                        }, 5000);
                    }

                    if (passwordInput.value !== confirmInput.value) {
                        e.preventDefault();
                        hasError = true;
                        confirmWrapper.classList.add('error');
                        confirmError.classList.add('show');
                        if (!firstErrorElement) firstErrorElement = confirmInput;

                        setTimeout(() => {
                            confirmError.classList.remove('show');
                            confirmWrapper.classList.remove('error');
                        }, 5000);
                    }

                    if (hasError && firstErrorElement) {
                        // Hide loading spinner if validation fails
                        if (typeof window.hideLoading === 'function') {
                            window.hideLoading();
                        }
                        firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorElement.focus();
                        return false;
                    }

                    return true;
                });

                const inputs = document.querySelectorAll('.input-wrapper-setup input');
                inputs.forEach(input => {
                    updateInputBackground(input);
                });
            });
        </script>
    @endpush
@endsection