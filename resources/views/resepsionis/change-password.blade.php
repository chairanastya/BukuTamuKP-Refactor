@extends('layouts.app')
@section('title', 'Ubah Password - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <x-user-dropdown :userName="Auth::user()->nama_resepsionis" :logoutRoute="route('resepsionis.logout')" />
@endsection

@section('sidebar')
    @include('partials.resepsionis-sidebar')
@endsection

@push('styles')
    <style>
        .container {
            margin-left: 90px;
            padding: 110px 4rem 0;
            max-width: 100%;
            width: calc(100vw - 90px);
            box-sizing: border-box;
        }

        .max-w-3xl {
            max-width: 768px;
            margin: 0 auto;
        }

        h1 {
            font-size: 30.8px;
        }

        .info-box {
            background-color: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: start;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        @media (max-width: 1200px) {
            .container {
                margin-left: 80px;
                width: calc(100vw - 80px);
                padding-inline: 3rem;
            }

            h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 160px 2rem 0;
                width: 100vw;
            }

            .flex.gap-4 {
                flex-direction: column;
            }

            .flex.gap-4>* {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-[#084E8F] mb-6">Ubah Password</h1>

            @if ($errors->any())
                <div class="info-box" style="background-color: #fee2e2; border-color: #ef4444; color: #991b1b;">
                    @svg('heroicon-o-x-circle', 'w-5 h-5 flex-shrink-0')
                    <div class="flex-1">
                        <ul class="list-none space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="info-box">
                @svg('heroicon-o-information-circle', 'w-5 h-5 flex-shrink-0')
                <span>Password harus minimal 8 karakter dan menggunakan kombinasi huruf, angka, dan simbol untuk keamanan
                    maksimal.</span>
            </div>

            <form id="password_form" method="POST" action="{{ route('resepsionis.password.change') }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <x-input-wrapper id="current_password" name="current_password" label="Password Saat Ini" type="password"
                    placeholder="Masukkan password saat ini" :required="true"
                    errorMessage="Password saat ini tidak sesuai" />

                <x-input-wrapper id="new_password" name="new_password" label="Password Baru" type="password"
                    placeholder="Masukkan password baru" :required="true"
                    errorMessage="Password baru minimal 8 karakter" />

                <x-input-wrapper id="new_password_confirmation" name="new_password_confirmation" label="Konfirmasi Password Baru" type="password"
                    placeholder="Masukkan ulang password baru" :required="true"
                    errorMessage="Konfirmasi password tidak cocok" />

                <div class="flex gap-4">
                    <x-button variant="cancel" :href="route('resepsionis.dashboard')"
                        class="flex-1 py-4 px-6 shadow-lg hover:shadow-xl">
                        Batalkan
                    </x-button>
                    <x-button variant="primary" type="submit" icon="fas-key"
                        class="flex-1 py-4 px-6 shadow-lg hover:shadow-xl">
                        Simpan Password Baru
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <x-modal name="successModal" :useAlpine="false" id="successModal" :showHeader="false" maxWidth="md">
        <div class="modal-header">
            <h3 class="text-2xl font-bold text-green-600">Berhasil</h3>
            <button onclick="closeSuccessModal()" class="modal-close">&times;</button>
        </div>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700">Password berhasil diubah!</p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="success" onclick="closeSuccessModal()">
                Tutup
            </x-button>
        </div>
    </x-modal>

    <script>
        function showSuccessModal() {
            document.getElementById('successModal').classList.add('show');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
            window.location.href = '{{ route('resepsionis.dashboard') }}';
        }

        // Check for session success message
        @if(session('status'))
            document.addEventListener('DOMContentLoaded', function () {
                showSuccessModal();
            });
        @endif

        document.addEventListener('click', function (e) {
            const successModal = document.getElementById('successModal');
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // Form elements
        const form = document.getElementById('password_form');
        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const newPasswordConfirmation = document.getElementById('new_password_confirmation');

        // Validation functions
        function validateCurrentPassword() {
            const error = document.getElementById('current_password_error');
            if (currentPasswordInput.value.trim() === '') {
                error.classList.add('show');
                return false;
            } else {
                error.classList.remove('show');
                return true;
            }
        }

        function validateNewPassword() {
            const error = document.getElementById('new_password_error');
            if (newPasswordInput.value.trim().length < 8) {
                error.classList.add('show');
                return false;
            } else {
                error.classList.remove('show');
                return true;
            }
        }

        function validateConfirmation() {
            const error = document.getElementById('new_password_confirmation_error');
            if (newPasswordInput.value !== newPasswordConfirmation.value) {
                error.classList.add('show');
                return false;
            } else {
                error.classList.remove('show');
                return true;
            }
        }

        // Validasi real-time password saat ini
        let verificationTimeout;
        currentPasswordInput.addEventListener('input', function() {
            clearTimeout(verificationTimeout);
            
            if (this.value.length >= 3) {
                verificationTimeout = setTimeout(() => {
                    fetch('{{ route('resepsionis.password.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            current_password: currentPasswordInput.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const wrapper = currentPasswordInput.closest('.input-wrapper');
                        const error = document.getElementById('current_password_error');
                        
                        if (data.valid === false) {
                            wrapper?.classList.add('error');
                            error?.classList.add('show');
                        } else {
                            wrapper?.classList.remove('error');
                            error?.classList.remove('show');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }, 500);
            }
        });

        // Blur validations
        currentPasswordInput.addEventListener('blur', validateCurrentPassword);
        newPasswordInput.addEventListener('blur', validateNewPassword);
        newPasswordConfirmation.addEventListener('blur', validateConfirmation);

        // Form submit validation
        form.addEventListener('submit', function (e) {
            const isCurrentValid = validateCurrentPassword();
            const isNewValid = validateNewPassword();
            const isConfirmValid = validateConfirmation();

            if (!isCurrentValid || !isNewValid || !isConfirmValid) {
                e.preventDefault();
            }
        });
    </script>
@endsection