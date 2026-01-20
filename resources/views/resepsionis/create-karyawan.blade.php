@extends('layouts.app')
@section('title', 'Buat Karyawan Baru - Buku Tamu Digital')

@section('header')
Buku Tamu Digital
@endsection

@section('header-action')
<div class="relative">
    <button onclick="toggleDropdown()" class="flex items-center gap-2">
        <span>{{ Auth::user()->nama_resepsionis }}</span>
        @svg('uiw-down', 'w-5 h-5')
    </button>
    <div id="dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <form method="POST" action="{{ route('resepsionis.logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700">
                Log Out
            </button>
        </form>
    </div>
</div>
@endsection

@section('sidebar')
    <a href="{{ route('resepsionis.dashboard') }}#beranda" class="sidebar-item">
        @svg('fluentui-home-24', 'w-8 h-8')
        <span>Beranda</span>
    </a>
    <a href="{{ route('resepsionis.dashboard') }}#riwayat" class="sidebar-item">
        @svg('gmdi-history', 'w-8 h-8')
        <span>Riwayat</span>
    </a>
    <a href="{{ route('resepsionis.dashboard') }}#karyawan" class="sidebar-item">
        @svg('gmdi-people-r', 'w-8 h-8')
        <span>Daftar Karyawan</span>
    </a>
@endsection

@include('partials.kunjungan-form-styles')

@push('styles')
    <style>
        .container {
            margin-left: 90px;
            padding-top: 110px;            
        }
        
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding-top: 160px;
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-[#084E8F] mb-6">Tambah Karyawan Baru</h1>
        
        <form id="karyawan_form" action="{{ route('resepsionis.karyawan.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="nama_karyawan" class="block text-[#084E8F] font-bold mb-2">
                    Nama Lengkap Karyawan
                </label>
                <div class="input-wrapper {{ $errors->has('nama_karyawan') ? 'border-red-500 bg-red-50' : '' }}">
                    <input type="text" id="nama_karyawan" name="nama_karyawan"
                        placeholder="Tuliskan nama lengkap karyawan" 
                        value="{{ old('nama_karyawan') }}"
                        required>
                </div>
                @error('nama_karyawan')
                    <div class="error-message show">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        {{ $message }}
                    </div>
                @else
                    <div id="nama_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Nama lengkap karyawan wajib diisi
                    </div>
                @enderror
            </div>

            <div>
                <label for="email_karyawan" class="block text-[#084E8F] font-bold mb-2">
                    Alamat Email Karyawan
                </label>
                <div class="input-wrapper {{ $errors->has('email_karyawan') ? 'border-red-500 bg-red-50' : '' }}">
                    <input type="email" id="email_karyawan" name="email_karyawan" 
                        placeholder="Tuliskan alamat email karyawan" 
                        value="{{ old('email_karyawan') }}"
                        required>
                </div>
                @error('email_karyawan')
                    <div class="error-message show">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        {{ $message }}
                    </div>
                @else
                    <div id="email_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Email tidak valid
                    </div>
                @enderror
            </div>

            <div>
                <label for="departemen" class="block text-[#084E8F] font-bold mb-2">
                    Departemen
                </label>
                <div class="relative">
                    <div class="input-wrapper {{ $errors->has('departemen') ? 'border-red-500 bg-red-50' : '' }} flex items-center">
                        <input type="text" id="departemen" name="departemen"
                            placeholder="Tuliskan atau pilih departemen" 
                            value="{{ old('departemen') }}"
                            autocomplete="off"
                            class="flex-1"
                            required>
                        <button type="button" onclick="toggleDepartemenDropdown()" class="ml-2 text-[#084E8F] hover:text-[#F7B218] transition">
                            @svg('heroicon-o-chevron-down', 'w-5 h-5')
                        </button>
                    </div>
                    <div id="departemen_dropdown" class="autocomplete-dropdown"></div>
                </div>
                @error('departemen')
                    <div class="error-message show">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        {{ $message }}
                    </div>
                @else
                    <div id="departemen_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Departemen wajib diisi
                    </div>
                @enderror
            </div>

            <div>
                <label for="jabatan" class="block text-[#084E8F] font-bold mb-2">
                    Jabatan
                </label>
                <div class="relative">
                    <div class="input-wrapper {{ $errors->has('jabatan') ? 'border-red-500 bg-red-50' : '' }} flex items-center">
                        <input type="text" id="jabatan" name="jabatan"
                            placeholder="Tuliskan atau pilih jabatan" 
                            value="{{ old('jabatan') }}"
                            autocomplete="off"
                            class="flex-1"
                            required>
                        <button type="button" onclick="toggleJabatanDropdown()" class="ml-2 text-[#084E8F] hover:text-[#F7B218] transition">
                            @svg('heroicon-o-chevron-down', 'w-5 h-5')
                        </button>
                    </div>
                    <div id="jabatan_dropdown" class="autocomplete-dropdown"></div>
                </div>
                @error('jabatan')
                    <div class="error-message show">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        {{ $message }}
                    </div>
                @else
                    <div id="jabatan_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Jabatan wajib diisi
                    </div>
                @enderror
            </div>

            <div class="flex gap-4">
                <a href="{{ route('resepsionis.karyawan') }}#karyawan" 
                    class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    @svg('heroicon-o-x-mark', 'w-5 h-5')
                    Batalkan
                </a>
                <button type="submit" id="submitButton"
                    class="flex-1 bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    @svg('fas-save', 'w-5 h-5')
                    <span id="submitButtonText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="successModal" class="modal-overlay">
    <div class="modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-green-600">Berhasil</h3>
            <button onclick="closeSuccessModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700" id="successMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="closeSuccessModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>

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
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal('{{ session('success') }}');
        });
    @endif

    document.addEventListener('click', function(e) {
        const successModal = document.getElementById('successModal');
        if (e.target === successModal) {
            closeSuccessModal();
        }
    });

    function toggleDropdown() {
        document.getElementById('dropdown').classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('dropdown');
        if (!e.target.closest('[onclick="toggleDropdown()"]') && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Form validation
    const form = document.getElementById('karyawan_form');
    const namaInput = document.getElementById('nama_karyawan');
    const emailInput = document.getElementById('email_karyawan');
    const departemenInput = document.getElementById('departemen');
    const jabatanInput = document.getElementById('jabatan');

    function validateNama() {
        const namaError = document.getElementById('nama_error');
        if (namaInput.value.trim() === '') {
            namaError.classList.add('show');
            return false;
        } else {
            namaError.classList.remove('show');
            return true;
        }
    }

    function validateEmail() {
        const emailError = document.getElementById('email_error');
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

    form.addEventListener('submit', function(e) {
        const isNamaValid = validateNama();
        const isEmailValid = validateEmail();
        const isDepartemenValid = validateDepartemen();
        const isJabatanValid = validateJabatan();

        if (!isNamaValid || !isEmailValid || !isDepartemenValid || !isJabatanValid) {
            e.preventDefault();
        }
    });

    // Autocomplete for Departemen
    const departemenDropdown = document.getElementById('departemen_dropdown');
    let allDepartemen = [];
    
    function toggleDepartemenDropdown() {
        if (departemenDropdown.classList.contains('show')) {
            departemenDropdown.classList.remove('show');
        } else {
            loadAllDepartemen();
        }
    }

    function loadAllDepartemen() {
        fetch(`{{ route('resepsionis.karyawan.search-departemen') }}?q=`)
            .then(response => response.json())
            .then(data => {
                allDepartemen = data;
                displayDepartemenAutocomplete(data, departemenInput.value.trim());
            })
            .catch(error => console.error('Error loading departemen:', error));
    }
    
    departemenInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimeout);
        
        if (query.length === 0) {
            departemenDropdown.classList.remove('show');
            return;
        }

        debounceTimeout = setTimeout(() => {
            if (allDepartemen.length > 0) {
                displayDepartemenAutocomplete(allDepartemen, query);
            } else {
                searchDepartemen(query);
            }
        }, 300);
    });

    departemenInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0 && allDepartemen.length > 0) {
            displayDepartemenAutocomplete(allDepartemen, this.value.trim());
        }
    });

    document.addEventListener('click', function(e) {
        if (!departemenInput.contains(e.target) && !departemenDropdown.contains(e.target) && !e.target.closest('button[onclick="toggleDepartemenDropdown()"]')) {
            departemenDropdown.classList.remove('show');
        }
    });

    function searchDepartemen(query) {
        fetch(`{{ route('resepsionis.karyawan.search-departemen') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                allDepartemen = data;
                displayDepartemenAutocomplete(data, query);
            })
            .catch(error => console.error('Error searching departemen:', error));
    }

    function displayDepartemenAutocomplete(items, query = '') {
        const filteredItems = query ? items.filter(item => item.toLowerCase().includes(query.toLowerCase())) : items;
        
        let html = '';
        
        if (filteredItems.length > 0) {
            html = filteredItems.map(item => `
                <div class="autocomplete-item" onclick="selectDepartemen('${escapeHtml(item)}')">
                    ${escapeHtml(item)}
                </div>
            `).join('');
        }
        
        // Add "Tambah Departemen" option
        html += `
            <div class="autocomplete-item" style="border-top: 1px solid #e5e7eb; background-color: #f3f4f6; font-weight: 600;" onclick="addNewDepartemen()">
                @svg('heroicon-o-plus-circle', 'inline w-4 h-4 mr-2')
                Tambah Departemen Baru
            </div>
        `;

        departemenDropdown.innerHTML = html;
        departemenDropdown.classList.add('show');
    }

    function selectDepartemen(value) {
        departemenInput.value = value;
        departemenDropdown.classList.remove('show');
        validateDepartemen();
    }

    function addNewDepartemen() {
        departemenDropdown.classList.remove('show');
        departemenInput.focus();
        if (departemenInput.value.trim() === '') {
            departemenInput.placeholder = 'Ketik departemen baru...';
        }
    }

    // Autocomplete for Jabatan
    const jabatanDropdown = document.getElementById('jabatan_dropdown');
    let allJabatan = [];
    
    function toggleJabatanDropdown() {
        if (jabatanDropdown.classList.contains('show')) {
            jabatanDropdown.classList.remove('show');
        } else {
            loadAllJabatan();
        }
    }

    function loadAllJabatan() {
        fetch(`{{ route('resepsionis.karyawan.search-jabatan') }}?q=`)
            .then(response => response.json())
            .then(data => {
                allJabatan = data;
                displayJabatanAutocomplete(data, jabatanInput.value.trim());
            })
            .catch(error => console.error('Error loading jabatan:', error));
    }
    
    jabatanInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimeout);
        
        if (query.length === 0) {
            jabatanDropdown.classList.remove('show');
            return;
        }

        debounceTimeout = setTimeout(() => {
            if (allJabatan.length > 0) {
                displayJabatanAutocomplete(allJabatan, query);
            } else {
                searchJabatan(query);
            }
        }, 300);
    });

    jabatanInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0 && allJabatan.length > 0) {
            displayJabatanAutocomplete(allJabatan, this.value.trim());
        }
    });

    document.addEventListener('click', function(e) {
        if (!jabatanInput.contains(e.target) && !jabatanDropdown.contains(e.target) && !e.target.closest('button[onclick="toggleJabatanDropdown()"]')) {
            jabatanDropdown.classList.remove('show');
        }
    });

    function searchJabatan(query) {
        fetch(`{{ route('resepsionis.karyawan.search-jabatan') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                allJabatan = data;
                displayJabatanAutocomplete(data, query);
            })
            .catch(error => console.error('Error searching jabatan:', error));
    }

    function displayJabatanAutocomplete(items, query = '') {
        const filteredItems = query ? items.filter(item => item.toLowerCase().includes(query.toLowerCase())) : items;
        
        let html = '';
        
        if (filteredItems.length > 0) {
            html = filteredItems.map(item => `
                <div class="autocomplete-item" onclick="selectJabatan('${escapeHtml(item)}')">
                    ${escapeHtml(item)}
                </div>
            `).join('');
        }
        
        // Add "Tambah Jabatan" option
        html += `
            <div class="autocomplete-item" style="border-top: 1px solid #e5e7eb; background-color: #f3f4f6; font-weight: 600;" onclick="addNewJabatan()">
                @svg('heroicon-o-plus-circle', 'inline w-4 h-4 mr-2')
                Tambah Jabatan Baru
            </div>
        `;

        jabatanDropdown.innerHTML = html;
        jabatanDropdown.classList.add('show');
    }

    function selectJabatan(value) {
        jabatanInput.value = value;
        jabatanDropdown.classList.remove('show');
        validateJabatan();
    }

    function addNewJabatan() {
        jabatanDropdown.classList.remove('show');
        jabatanInput.focus();
        if (jabatanInput.value.trim() === '') {
            jabatanInput.placeholder = 'Ketik jabatan baru...';
        }
    }

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
    window.selectJabatan = function(value) {
        originalSelectJabatan(value);
        updateSubmitButton();
    };

</script>
@endsection

