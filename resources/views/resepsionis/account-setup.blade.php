@extends('layouts.guest')
@section('title', 'Buat Password Akun Baru - Buku Tamu Digital')
@push('styles')
    <style>
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
            Buat Password Akun Baru Resepsionis
        </h1>
        <p class="text-center text-gray-600 mb-8">
            Selamat datang, <strong class="text-blue-900">{{ $nama }}</strong>!
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

        <form method="POST" action="{{ route('resepsionis.account.store', ['token' => $token]) }}" novalidate>
            @csrf

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

            <div class="mb-4 flex items-center justify-end">
                <label class="flex items-center cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="showPassword"
                        class="mr-2 w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    Tampilkan Password
                </label>
            </div>

            <x-recaptcha class="mb-6" />

            <x-button type="submit" variant="primary" class="w-full py-3 shadow-lg hover:shadow-xl" icon="">
                Buat Akun
            </x-button>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initPasswordToggle({
                passwordFieldIds: ['password', 'password_confirmation'],
                checkboxId: 'showPassword'
            });

            initInputBackgrounds();

            setupFormValidation({
                passwordFieldId: 'password',
                confirmPasswordFieldId: 'password_confirmation',
                minLength: 8,
                formSelector: 'form',
                errorTimeout: 3000
            });
        });
    </script>
@endpush