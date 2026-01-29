@extends('layouts.app')
@section('title', 'Buat Karyawan Baru - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <x-user-dropdown :userName="Auth::user()->nama_resepsionis" :logoutRoute="route('resepsionis.logout')" />
@endsection

@section('sidebar')
    @include('partials.resepsionis-sidebar')
@endsection

@include('partials.kunjungan-form-styles')

@push('styles')
    <style>
        .container {
            margin-left: 90px;
            padding: 110px 4rem 0;
            max-width: 100%;
            width: calc(100vw - 90px);
            box-sizing: border-box;
        }

        .max-w-6xl {
            max-width: 100%;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .flex.gap-4 {
            flex-wrap: wrap;
            gap: 1.1rem;
        }

        .autocomplete-dropdown {
            max-width: 100%;
            left: 0;
            right: 0;
            font-size: 16.5px;
        }

        input,
        textarea,
        select {
            max-width: 100%;
            box-sizing: border-box;
        }

        h1 {
            font-size: 30.8px;
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
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold text-[#084E8F] mb-6">Tambah Karyawan Baru</h1>

            <form id="karyawan_form" action="{{ route('resepsionis.karyawan.store') }}" method="POST" class="space-y-6"
                novalidate>
                @csrf

                <x-input-wrapper id="nama_karyawan" name="nama_karyawan" label="Nama Lengkap Karyawan" type="text"
                    placeholder="Tuliskan nama lengkap karyawan" :value="old('nama_karyawan')" :required="true"
                    :error="$errors->first('nama_karyawan')" errorMessage="Nama lengkap karyawan wajib diisi" />

                <x-input-wrapper id="email_karyawan" name="email_karyawan" label="Alamat Email Karyawan" type="email"
                    placeholder="Tuliskan alamat email karyawan" :value="old('email_karyawan')" :required="true"
                    :error="$errors->first('email_karyawan')" errorMessage="Email tidak valid" />

                <div class="relative">
                    <x-input-wrapper id="departemen" name="departemen" label="Departemen" type="text"
                        placeholder="Tuliskan atau pilih departemen" :value="old('departemen')" :required="true"
                        :error="$errors->first('departemen')" errorMessage="Departemen wajib diisi" :appendSlot="true"
                        autocomplete="off">
                        <button type="button" onclick="toggleDepartemenDropdown()"
                            class="ml-2 text-[#084E8F] hover:text-[#F7B218] transition">
                            @svg('heroicon-o-chevron-down', 'w-5 h-5')
                        </button>
                    </x-input-wrapper>
                    <div id="departemen_dropdown" class="autocomplete-dropdown"></div>
                </div>

                <div class="relative">
                    <x-input-wrapper id="jabatan" name="jabatan" label="Jabatan" type="text"
                        placeholder="Tuliskan atau pilih jabatan" :value="old('jabatan')" :required="true"
                        :error="$errors->first('jabatan')" errorMessage="Jabatan wajib diisi" :appendSlot="true"
                        autocomplete="off">
                        <button type="button" onclick="toggleJabatanDropdown()"
                            class="ml-2 text-[#084E8F] hover:text-[#F7B218] transition">
                            @svg('heroicon-o-chevron-down', 'w-5 h-5')
                        </button>
                    </x-input-wrapper>
                    <div id="jabatan_dropdown" class="autocomplete-dropdown"></div>
                </div>

                <div class="flex gap-4">
                    <x-button variant="cancel" :href="route('resepsionis.karyawan') . '#karyawan'"
                        class="flex-1 py-4 px-6 shadow-lg hover:shadow-xl">
                        Batalkan
                    </x-button>
                    <x-button variant="primary" type="submit" id="submitButton" icon="fas-save"
                        class="flex-1 py-4 px-6 shadow-lg hover:shadow-xl">
                        <span id="submitButtonText">Simpan</span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <x-modal name="successModal" :useAlpine="false" id="successModal" :showHeader="false" maxWidth="md">
        <div class="modal-header">
            <h3 class="text-2xl font-bold text-green-600">Berhasil</h3>
            <button onclick="window.closeSuccessModal()" class="modal-close">&times;</button>
        </div>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700" id="successMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="success" onclick="window.closeSuccessModal()">
                Tutup
            </x-button>
        </div>
    </x-modal>

    <script>
        // Form elements
        const form = document.getElementById('karyawan_form');
        const namaInput = document.getElementById('nama_karyawan');
        const emailInput = document.getElementById('email_karyawan');
        const departemenInput = document.getElementById('departemen');
        const jabatanInput = document.getElementById('jabatan');
        const submitButtonText = document.getElementById('submitButtonText');

        // Handle session success message
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                window.showSuccessModal('{{ session('success') }}');
            });
        @endif

        // Setup form validation using form-validation.js component
        function initializeKaryawanForm() {
            // Validate each field
            const validateFields = () => {
                const namaError = document.getElementById('nama_karyawan_error');
                const emailError = document.getElementById('email_karyawan_error');
                const departemenError = document.getElementById('departemen_error');
                const jabatanError = document.getElementById('jabatan_error');

                return validateTextField(namaInput, namaError) &&
                       validateEmail(emailInput, emailError) &&
                       validateTextField(departemenInput, departemenError) &&
                       validateTextField(jabatanInput, jabatanError);
            };

            // Add blur listeners for real-time validation
            namaInput.addEventListener('blur', () => validateTextField(namaInput, document.getElementById('nama_karyawan_error')));
            emailInput.addEventListener('blur', () => validateEmail(emailInput, document.getElementById('email_karyawan_error')));
            departemenInput.addEventListener('blur', () => validateTextField(departemenInput, document.getElementById('departemen_error')));
            jabatanInput.addEventListener('blur', () => validateTextField(jabatanInput, document.getElementById('jabatan_error')));

            // Setup form submit validation
            form.addEventListener('submit', function (e) {
                if (!validateFields()) {
                    e.preventDefault();
                }
            });
        }

        function initializeAutocomplete() {
            // Departemen autocomplete
            const departemenAuto = window.createAutocomplete({
                input: departemenInput,
                dropdown: document.getElementById('departemen_dropdown'),
                searchRoute: '{{ route('resepsionis.karyawan.search-departemen') }}',
                validateFn: () => validateTextField(departemenInput, document.getElementById('departemen_error')),
                label: 'Departemen'
            });
            window.toggleDepartemenDropdown = () => departemenAuto.toggle();

            // Jabatan autocomplete with submit button update callback
            const jabatanAuto = window.createAutocomplete({
                input: jabatanInput,
                dropdown: document.getElementById('jabatan_dropdown'),
                searchRoute: '{{ route('resepsionis.karyawan.search-jabatan') }}',
                validateFn: () => {
                    validateTextField(jabatanInput, document.getElementById('jabatan_error'));
                    updateSubmitButton();
                },
                label: 'Jabatan'
            });
            window.toggleJabatanDropdown = () => jabatanAuto.toggle();
        }

        // Update submit button text based on jabatan value
        function updateSubmitButton() {
            const jabatanValue = jabatanInput.value.trim().toLowerCase();
            submitButtonText.textContent = jabatanValue === 'resepsionis'
                ? 'Simpan & Kirim Undangan'
                : 'Simpan';
        }

        // Listen for jabatan input changes
        jabatanInput.addEventListener('input', updateSubmitButton);
        jabatanInput.addEventListener('change', updateSubmitButton);

        // Initialize all on DOM ready
        document.addEventListener('DOMContentLoaded', function () {
            initializeKaryawanForm();
            initializeAutocomplete();
        });
    </script>
@endsection