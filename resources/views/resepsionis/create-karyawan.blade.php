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
            <button onclick="closeSuccessModal()" class="modal-close">&times;</button>
        </div>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700" id="successMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="success" onclick="closeSuccessModal()">
                Tutup
            </x-button>
        </div>
    </x-modal>

    <script>
        let debounceTimeout;

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('show');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
        }

        // Check for session success message
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                showSuccessModal('{{ session('success') }}');
            });
        @endif

        document.addEventListener('click', function (e) {
            const successModal = document.getElementById('successModal');
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // Form validation
        const form = document.getElementById('karyawan_form');
        const namaInput = document.getElementById('nama_karyawan');
        const emailInput = document.getElementById('email_karyawan');
        const departemenInput = document.getElementById('departemen');
        const jabatanInput = document.getElementById('jabatan');

        function validateNama() {
            const namaError = document.getElementById('nama_karyawan_error');
            if (namaInput.value.trim() === '') {
                namaError.classList.add('show');
                return false;
            } else {
                namaError.classList.remove('show');
                return true;
            }
        }

        function validateEmail() {
            const emailError = document.getElementById('email_karyawan_error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                emailError.classList.add('show');
                return false;
            } else {
                emailError.classList.remove('show');
                return true;
            }
        }

        function validateDepartemen() {
            const departemenError = document.getElementById('departemen_error');
            if (departemenInput.value.trim() === '') {
                departemenError.classList.add('show');
                return false;
            } else {
                departemenError.classList.remove('show');
                return true;
            }
        }

        function validateJabatan() {
            const jabatanError = document.getElementById('jabatan_error');
            if (jabatanInput.value.trim() === '') {
                jabatanError.classList.add('show');
                return false;
            } else {
                jabatanError.classList.remove('show');
                return true;
            }
        }

        namaInput.addEventListener('blur', validateNama);
        emailInput.addEventListener('blur', validateEmail);
        departemenInput.addEventListener('blur', validateDepartemen);
        jabatanInput.addEventListener('blur', validateJabatan);

        form.addEventListener('submit', function (e) {
            const isNamaValid = validateNama();
            const isEmailValid = validateEmail();
            const isDepartemenValid = validateDepartemen();
            const isJabatanValid = validateJabatan();

            if (!isNamaValid || !isEmailValid || !isDepartemenValid || !isJabatanValid) {
                e.preventDefault();
            }
        });

        // Generic Autocomplete Handler
        function createAutocomplete(config) {
            const { input, dropdown, searchRoute, validateFn, label } = config;
            let allItems = [];

            const toggle = () => {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                } else {
                    loadAll();
                }
            };

            const loadAll = () => {
                fetch(`${searchRoute}?q=`)
                    .then(response => response.json())
                    .then(data => {
                        allItems = data;
                        display(data, input.value.trim());
                    })
                    .catch(error => console.error(`Error loading ${label}:`, error));
            };

            const search = (query) => {
                fetch(`${searchRoute}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        allItems = data;
                        display(data, query);
                    })
                    .catch(error => console.error(`Error searching ${label}:`, error));
            };

            const display = (items, query = '') => {
                const filteredItems = query ? items.filter(item => item.toLowerCase().includes(query.toLowerCase())) : items;

                let html = filteredItems.map(item => `
                                    <div class="autocomplete-item" onclick="${config.selectFn}('${escapeHtml(item)}')">
                                        ${escapeHtml(item)}
                                    </div>
                                `).join('');

                html += `
                                    <div class="autocomplete-item" style="border-top: 1px solid #e5e7eb; background-color: #f3f4f6; font-weight: 600;" onclick="${config.addNewFn}()">
                                        @svg('heroicon-o-plus-circle', 'inline w-4 h-4 mr-2')
                                        Tambah ${label} Baru
                                    </div>
                                `;

                dropdown.innerHTML = html;
                dropdown.classList.add('show');
            };

            const select = (value) => {
                input.value = value;
                dropdown.classList.remove('show');
                validateFn();
            };

            const addNew = () => {
                dropdown.classList.remove('show');
                input.focus();
                if (input.value.trim() === '') {
                    input.placeholder = `Ketik ${label.toLowerCase()} baru...`;
                }
            };

            // Event listeners
            input.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(debounceTimeout);

                if (query.length === 0) {
                    dropdown.classList.remove('show');
                    return;
                }

                debounceTimeout = setTimeout(() => {
                    allItems.length > 0 ? display(allItems, query) : search(query);
                }, 300);
            });

            input.addEventListener('focus', function () {
                if (this.value.trim().length > 0 && allItems.length > 0) {
                    display(allItems, this.value.trim());
                }
            });

            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target) && !e.target.closest(`button[onclick="${config.toggleFn}()"]`)) {
                    dropdown.classList.remove('show');
                }
            });

            return { toggle, select, addNew };
        }

        // Departemen Autocomplete
        const departemenAuto = createAutocomplete({
            input: departemenInput,
            dropdown: document.getElementById('departemen_dropdown'),
            searchRoute: '{{ route('resepsionis.karyawan.search-departemen') }}',
            validateFn: validateDepartemen,
            label: 'Departemen',
            toggleFn: 'toggleDepartemenDropdown',
            selectFn: 'selectDepartemen',
            addNewFn: 'addNewDepartemen'
        });

        function toggleDepartemenDropdown() { departemenAuto.toggle(); }
        function selectDepartemen(value) { departemenAuto.select(value); }
        function addNewDepartemen() { departemenAuto.addNew(); }

        // Jabatan Autocomplete
        const jabatanAuto = createAutocomplete({
            input: jabatanInput,
            dropdown: document.getElementById('jabatan_dropdown'),
            searchRoute: '{{ route('resepsionis.karyawan.search-jabatan') }}',
            validateFn: validateJabatan,
            label: 'Jabatan',
            toggleFn: 'toggleJabatanDropdown',
            selectFn: 'selectJabatan',
            addNewFn: 'addNewJabatan'
        });

        function toggleJabatanDropdown() { jabatanAuto.toggle(); }
        function selectJabatan(value) { jabatanAuto.select(value); updateSubmitButton(); }
        function addNewJabatan() { jabatanAuto.addNew(); }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Update button text when jabatan changes
        function updateSubmitButton() {
            const jabatanValue = jabatanInput.value.trim().toLowerCase();
            const submitButtonText = document.getElementById('submitButtonText');

            if (jabatanValue === 'resepsionis') {
                submitButtonText.textContent = 'Simpan & Kirim Undangan';
            } else {
                submitButtonText.textContent = 'Simpan';
            }
        }

        // Listen for jabatan changes
        jabatanInput.addEventListener('input', updateSubmitButton);
        jabatanInput.addEventListener('change', updateSubmitButton);

        // Also update when selecting from dropdown
        const originalSelectJabatan = window.selectJabatan;
        window.selectJabatan = function (value) {
            originalSelectJabatan(value);
            updateSubmitButton();
        };

    </script>
@endsection