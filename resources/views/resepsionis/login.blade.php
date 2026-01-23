@extends('layouts.guest')
@section('title', 'Login Resepsionis - Buku Tamu Digital')
@push('styles')
    <style>
        /* Tambahan untuk Page Login */
        .input-wrapper{
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .input-wrapper textarea {
            flex: 1;
        }

        .input-wrapper > .flex-shrink-0 {
            margin-left: 10px;
        }

    </style>
@endpush
@section('content')
    <x-auth-background />
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md">
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ route('tamu.form') }}"
                    class="inline-flex items-center text-blue-600 hover:text-blue-900 font-normal transition duration-200">
                    @svg('heroicon-o-arrow-left', 'w-4 h-4 mr-1')
                    Kembali ke Form Tamu
                </a>
            </div>
            <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8">
                Buku Tamu Digital
            </h1>

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

            @if (session('status'))
                <div class="mb-4 bg-green-50 border-2 border-green-600 text-green-700 px-4 py-3 rounded-lg">
                    <p class="text-sm flex items-start">
                        @svg('heroicon-o-check-circle', 'w-4 h-4 mr-2 flex-shrink-0 mt-0.5')
                        <span>{{ session('status') }}</span>
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('resepsionis.login') }}" novalidate>
                @csrf

                <div class="mb-6">
                    <x-input-wrapper
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Email"
                        :value="old('email')"
                        :error="$errors->first('email')"
                        errorMessage="Email wajib diisi dengan format yang benar"
                        :showLabel="false"
                        :required="true"
                    >
                        <x-slot:prepend>
                            @svg('bi-person-fill', 'w-6 h-6 text-[#084E8F]')
                        </x-slot:prepend>
                    </x-input-wrapper>
                </div>
                <div class="mb-4">
                    <x-input-wrapper
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Password"
                        :value="''"
                        :error="$errors->first('password')"
                        errorMessage="Password wajib diisi"
                        :showLabel="false"
                        :required="true"
                    >
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

                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                    Login
                </button>

                <div class="mt-6 text-center">
                    <a href="{{ route('resepsionis.password.request') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                        Lupa Password?
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

            function updateInputBackground(input) {
                const wrapper = input.closest('.input-wrapper');
                if (wrapper) {
                    wrapper.classList.toggle('filled', input.value.trim() !== '');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const emailWrapper = emailInput?.closest('.input-wrapper');
                const passwordWrapper = passwordInput?.closest('.input-wrapper');
                const emailError = document.getElementById('email_error');
                const passwordError = document.getElementById('password_error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                form.addEventListener('submit', function (e) {
                    let hasError = false;
                    let firstErrorElement = null;

                    if (emailWrapper) emailWrapper.classList.remove('error');
                    if (passwordWrapper) passwordWrapper.classList.remove('error');
                    if (emailError) emailError.classList.remove('show');
                    if (passwordError) passwordError.classList.remove('show');

                    if (!emailInput.value?.trim() || !emailRegex.test(emailInput.value)) {
                        e.preventDefault();
                        hasError = true;
                        if (emailWrapper) emailWrapper.classList.add('error');
                        if (emailError) emailError.classList.add('show');
                        if (!firstErrorElement) firstErrorElement = emailInput;

                        setTimeout(() => {
                            if (emailError) emailError.classList.remove('show');
                            if (emailWrapper) emailWrapper.classList.remove('error');
                        }, 5000);
                    }

                    if (!passwordInput.value?.trim()) {
                        e.preventDefault();
                        hasError = true;
                        if (passwordWrapper) passwordWrapper.classList.add('error');
                        if (passwordError) passwordError.classList.add('show');
                        if (!firstErrorElement) firstErrorElement = passwordInput;

                        setTimeout(() => {
                            if (passwordError) passwordError.classList.remove('show');
                            if (passwordWrapper) passwordWrapper.classList.remove('error');
                        }, 5000);
                    }

                    if (hasError && firstErrorElement) {
                        firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorElement.focus();
                        return false;
                    }

                    return true;
                });

                const inputs = document.querySelectorAll('.input-wrapper input');
                inputs.forEach(input => {
                    updateInputBackground(input);
                    input.addEventListener('input', () => updateInputBackground(input));
                    input.addEventListener('change', () => updateInputBackground(input));
                });
            });
        </script>
    @endpush
@endsection