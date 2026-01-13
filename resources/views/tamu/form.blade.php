@extends('layouts.guest')
@section('title', 'Form Tamu - Buku Tamu Digital')
@section('header')
    Buku Tamu Digital
@endsection
@section('header-action')
    <a href="{{ route('resepsionis.login') }}" class="">
        Login
    </a>
@endsection
@push('styles')
    <style>
        /* Base Styles */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: white;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #084E8F;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 32px;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            line-height: 1;
        }

        .modal-close:hover {
            color: #374151;
        }

        /* Autocomplete Dropdown */
        .autocomplete-dropdown {
            position: absolute;
            margin-top: 10px;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #084E8F;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 50;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .autocomplete-item:hover {
            background-color: #F9FCFF;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-name {
            color: #1e40af;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .autocomplete-detail {
            color: #6b7280;
            font-size: 14px;
        }

        /* Karyawan Search & Card */
        .karyawan-search-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .karyawan-search-container {
            flex: 1;
            position: relative;
            height: 50px;
        }

        .karyawan-action-buttons {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .karyawan-card {
            display: flex;
            align-items: center;
            padding: 0 12px;
            background-color: white;
            border: 2px solid #084E8F;
            border-radius: 8px;
            width: 100%;
            height: 50px;
            box-sizing: border-box;
            cursor: pointer;
            transition: all 0.2s;
        }

        .karyawan-card:hover {
            background-color: #f0f9ff;
            border-color: #0C4777;
        }

        .karyawan-card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 4px 0;
            gap: 2px;
            min-width: 0;
            overflow: hidden;
        }

        .karyawan-card-name {
            color: #084E8F;
            font-weight: 600;
            font-size: 15px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .karyawan-card-detail {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Action Buttons */
        .karyawan-add-btn,
        .karyawan-minus-btn {
            width: 50px;
            height: 50px;
            border: 2px dashed #084E8F;
            border-radius: 6px;
            background-color: white;
            color: #084E8F;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            font-weight: bold;
            padding: 0;
        }

        .karyawan-add-btn:hover,
        .karyawan-minus-btn:hover {
            background-color: #f0f9ff;
            transform: scale(1.05);
        }

        .karyawan-add-btn svg,
        .karyawan-minus-btn svg {
            width: 28px;
            height: 28px;
        }

        .karyawan-minus-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background-color: #f3f4f6;
            border-color: #d1d5db;
            color: #9ca3af;
        }

        .karyawan-minus-btn:disabled:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: none;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #084E8F;
            border-radius: 12px;
            padding: 48px 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #F9FCFF;
        }

        .upload-area:hover {
            background-color: white;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            color: #084E8F;
        }

        /* Input Wrapper */
        .input-wrapper {
            border: 2px solid #084E8F;
            border-radius: 8px;
            padding: 8px;
            width: 100%;
            transition: all 0.2s ease;
            background-color: #F9FCFF;
        }

        .input-wrapper.filled {
            background-color: white;
        }

        .input-wrapper:focus-within {
            box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.1);
        }

        .input-wrapper input,
        .input-wrapper textarea {
            background-color: transparent;
            width: 100%;
            border: none;
            outline: none;
        }

        /* Error States */
        .input-wrapper.error,
        .input-wrapper:has(input:invalid:not(:placeholder-shown)),
        .input-wrapper:has(textarea:invalid:not(:placeholder-shown)),
        .upload-area.error {
            border-color: #dc2626 !important;
            background-color: #fef2f2;
        }

        /* Error Message */
        .error-message {
            color: #dc2626;
            font-size: 14px;
            margin-top: 8px;
            display: none;
        }

        .error-message.show {
            display: block;
        }
    </style>
@endpush

@section('content')
    <!-- Main Form -->
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('tamu.submit') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto"
            novalidate>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Form Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-[#084E8F] font-bold mb-2">
                            Nama Lengkap
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="nama_lengkap" name="nama_lengkap"
                                placeholder="Tuliskan nama lengkap anda" required>
                        </div>
                        <div id="nama_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Nama lengkap wajib diisi
                        </div>
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-[#084E8F] font-bold mb-2">
                            Alamat Email
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="Tuliskan alamat email anda" required>
                        </div>
                        <div id="email_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Email wajib diisi dengan format yang benar
                        </div>
                    </div>

                    <!-- Instansi Asal -->
                    <div>
                        <label for="instansi" class="block text-[#084E8F] font-bold mb-2">
                            Instansi Asal
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="instansi" name="instansi" placeholder="Tuliskan instansi asal anda"
                                required>
                        </div>
                        <div id="instansi_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Instansi asal wajib diisi
                        </div>
                    </div>

                    <!-- Tujuan Kedatangan -->
                    <div>
                        <label for="tujuan" class="block text-[#084E8F] font-bold mb-2">
                            Tujuan Kedatangan
                        </label>
                        <div class="input-wrapper">
                            <textarea id="tujuan" name="tujuan" rows="4" placeholder="Jelaskan tujuan kedatangan anda"
                                class="resize-none" required></textarea>
                        </div>
                        <div id="tujuan_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Tujuan kedatangan wajib diisi
                        </div>
                    </div>

                    <!-- Karyawan yang Anda Tuju -->
                    <div>
                        <label class="block text-[#084E8F] font-bold mb-2">
                            Karyawan yang Anda Tuju
                        </label>

                        <!-- Container untuk search rows -->
                        <div id="karyawan_rows_container" class="space-y-3"></div>

                        <!-- Hidden input untuk menyimpan ID karyawan yang dipilih -->
                        <input type="hidden" id="karyawan_ids" name="karyawan_ids" value="[]">

                        <!-- Error message -->
                        <div id="karyawan_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Minimal pilih 1 karyawan yang dituju
                        </div>
                    </div>
                </div>

                <!-- Right Column - Webcam KTP & Submit -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Webcam Foto KTP -->
                    <div>
                        <label class="block text-[#084E8F] font-bold mb-2">
                            Foto Identitas (KTP)
                        </label>

                        <!-- Webcam Area (default state) -->
                        <div id="webcam_area" class="upload-area" onclick="openWebcamModal()">
                            @svg('zondicon-camera', 'upload-icon')
                            <p class="text-[#084E8F] font-bold">Klik untuk ambil foto</p>
                        </div>

                        <!-- Preview Captured Image (shown after capture) -->
                        <div id="image_preview" class="hidden">
                            <img id="preview_img" src="" alt="Preview KTP"
                                class="w-full rounded-lg border-2 border-[#084E8F]">
                            <button type="button" onclick="openWebcamModal()"
                                class="mt-3 w-full bg-[#47B9AE] hover:bg-[#F7B218] text-white font-bold py-2 px-4 rounded-lg transition">
                                Foto Ulang
                            </button>
                        </div>

                        <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="">
                        <div id="foto_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Foto KTP wajib diambil sebelum mengirim data
                        </div>
                        
                        <!-- Error dari backend khusus upload foto -->
                        @if($errors->has('foto_error'))
                            <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                                <div class="flex items-start">
                                    @svg('heroicon-o-x-circle', 'w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5')
                                    <div class="text-sm">
                                        <p class="font-bold text-red-800 mb-1">Gagal Upload Foto</p>
                                        <pre class="text-red-700 whitespace-pre-wrap font-sans">{{ $errors->first('foto_error') }}</pre>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        @svg('phosphor-paper-plane-tilt-fill', 'w-5 h-5')
                        Kirim Data
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Popup untuk Webcam -->
    <div id="webcam_modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Foto Identitas (KTP)</h2>
                <button type="button" class="modal-close" onclick="closeWebcamModal()">&times;</button>
            </div>

            <!-- Video Preview -->
            <div id="video_container">
                <video id="webcam_video" autoplay playsinline class="w-full rounded-lg mb-4"
                    style="background: #f3f4f6;"></video>
                <div class="flex gap-3">
                    <button type="button" onclick="closeWebcamModal()" style="background-color: #D9D9D9; color: #000000;"
                        class="flex-1 font-semibold py-3 px-4 rounded-lg transition hover:opacity-90 flex items-center justify-center gap-2">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                        Batalkan
                    </button>
                    <button type="button" onclick="capturePhoto()" style="background-color: #084E8F; color: white;"
                        class="flex-1 font-semibold py-3 px-4 rounded-lg transition hover:bg-[#F7B218] flex items-center justify-center gap-2">
                        @svg('zondicon-camera', 'w-5 h-5')
                        Ambil Foto
                    </button>
                </div>
            </div>

            <!-- Canvas (hidden, untuk capture) -->
            <canvas id="capture_canvas" class="hidden"></canvas>
        </div>
    </div>

    <!-- Modal Popup untuk Success -->
    <div id="success_modal" class="modal-overlay" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Sukses</h2>
                <button type="button" class="modal-close" onclick="closeSuccessModal()">&times;</button>
            </div>
            <div class="px-1">
                <p class="text-gray-700" id="success_message">{{ session('success') }}</p>
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeSuccessModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global Variables
        let selectedKaryawan = [];
        let rowCounter = 0;
        let stream = null;

        // DOM Elements
        const video = document.getElementById('webcam_video');
        const canvas = document.getElementById('capture_canvas');
        const ctx = canvas.getContext('2d');
        const webcamModal = document.getElementById('webcam_modal');
        const successModal = document.getElementById('success_modal');

        // Initialization
        document.addEventListener('DOMContentLoaded', function () {
            addKaryawanRow();

            @if(session('success'))
                showSuccessModal();
            @endif

            setupInputBackgrounds();
            setupFormValidation();
        });

        // Form Validation
        function setupFormValidation() {
            const form = document.querySelector('form');
            const inputs = {
                nama: document.getElementById('nama_lengkap'),
                email: document.getElementById('email'),
                instansi: document.getElementById('instansi'),
                tujuan: document.getElementById('tujuan'),
                foto: document.getElementById('foto_ktp_base64')
            };

            const errors = {
                nama: document.getElementById('nama_error'),
                email: document.getElementById('email_error'),
                instansi: document.getElementById('instansi_error'),
                tujuan: document.getElementById('tujuan_error'),
                karyawan: document.getElementById('karyawan_error'),
                foto: document.getElementById('foto_error')
            };

            const webcamArea = document.getElementById('webcam_area');
            const karyawanContainer = document.getElementById('karyawan_rows_container');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            form.addEventListener('submit', function (e) {
                let hasError = false;
                let firstErrorElement = null;

                if (!inputs.nama.value?.trim()) {
                    e.preventDefault();
                    showError(inputs.nama, errors.nama);
                    hasError = true;
                    if (!firstErrorElement) firstErrorElement = inputs.nama;
                }

                if (!inputs.email.value?.trim() || !emailRegex.test(inputs.email.value)) {
                    e.preventDefault();
                    showError(inputs.email, errors.email);
                    hasError = true;
                    if (!firstErrorElement) firstErrorElement = inputs.email;
                }

                if (!inputs.instansi.value?.trim()) {
                    e.preventDefault();
                    showError(inputs.instansi, errors.instansi);
                    hasError = true;
                    if (!firstErrorElement) firstErrorElement = inputs.instansi;
                }

                if (!inputs.tujuan.value?.trim()) {
                    e.preventDefault();
                    showError(inputs.tujuan, errors.tujuan);
                    hasError = true;
                    if (!firstErrorElement) firstErrorElement = inputs.tujuan;
                }

                if (selectedKaryawan.length === 0) {
                    e.preventDefault();
                    hasError = true;
                    errors.karyawan.classList.add('show');
                    const firstRow = karyawanContainer.querySelector('.karyawan-search-container');
                    if (firstRow) {
                        const inputWrapper = firstRow.querySelector('.border-2');
                        if (inputWrapper) {
                            inputWrapper.classList.add('border-red-600');
                            inputWrapper.classList.remove('border-[#084E8F]');
                        }
                    }
                    if (!firstErrorElement) firstErrorElement = karyawanContainer;

                    setTimeout(() => {
                        errors.karyawan.classList.remove('show');
                        const firstRow = karyawanContainer.querySelector('.karyawan-search-container');
                        if (firstRow) {
                            const inputWrapper = firstRow.querySelector('.border-2');
                            if (inputWrapper) {
                                inputWrapper.classList.remove('border-red-600');
                                inputWrapper.classList.add('border-[#084E8F]');
                            }
                        }
                    }, 5000);
                }

                if (!inputs.foto.value?.trim()) {
                    e.preventDefault();
                    hasError = true;
                    errors.foto.classList.add('show');
                    webcamArea.classList.add('error');
                    if (!firstErrorElement) firstErrorElement = webcamArea;

                    setTimeout(() => {
                        errors.foto.classList.remove('show');
                        webcamArea.classList.remove('error');
                    }, 5000);
                }

                if (hasError && firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                return true;
            });

            function showError(input, errorElement) {
                errorElement.classList.add('show');
                input.closest('.input-wrapper').classList.add('error');
                setTimeout(() => {
                    errorElement.classList.remove('show');
                    input.closest('.input-wrapper').classList.remove('error');
                }, 5000);
            }
        }

        // Karyawan Management Functions
        function addKaryawanRow() {
            const container = document.getElementById('karyawan_rows_container');
            const rowId = rowCounter++;

            const rowHtml = `
                            <div id="karyawan-row-${rowId}" class="karyawan-search-row">
                                <div class="karyawan-search-container" id="content-${rowId}">
                                    <div class="w-full h-full px-2 border-2 border-[#084E8F] rounded-lg transition flex items-center">
                                        <input type="text" 
                                            id="karyawan_input_${rowId}" 
                                            placeholder="Cari nama karyawan..."
                                            class="w-full karyawan-search-input"
                                            autocomplete="off"
                                            data-row-id="${rowId}">
                                    </div>
                                    <div id="autocomplete_dropdown_${rowId}" class="autocomplete-dropdown"></div>
                                </div>
                                <div class="karyawan-action-buttons">
                                    <button type="button" class="karyawan-add-btn" onclick="addKaryawanRow()" title="Tambah karyawan">
                                        @svg('heroicon-o-plus', 'w-7 h-7')
                                    </button>
                                    <button type="button" class="karyawan-minus-btn" onclick="removeKaryawanRow(${rowId})" title="Hapus baris">
                                        @svg('heroicon-o-minus', 'w-7 h-7')
                                    </button>
                                </div>
                            </div>`;

            container.insertAdjacentHTML('beforeend', rowHtml);
            setupRowListeners(rowId);
            updateMinusButtonsVisibility();
        }

        function removeKaryawanRow(rowId) {
            const rows = document.querySelectorAll('[id^="karyawan-row-"]');

            if (rows.length <= 1) {
                alert('Minimal harus ada satu karyawan yang dituju');
                return;
            }

            const row = document.getElementById(`karyawan-row-${rowId}`);
            selectedKaryawan = selectedKaryawan.filter(k => k.rowId !== rowId);
            updateHiddenInput();

            if (row) row.remove();
            updateMinusButtonsVisibility();
        }

        function setupRowListeners(rowId) {
            const input = document.getElementById(`karyawan_input_${rowId}`);
            const dropdown = document.getElementById(`autocomplete_dropdown_${rowId}`);
            let debounceTimeout;

            input.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(debounceTimeout);

                if (query.length < 2) {
                    dropdown.classList.remove('show');
                    dropdown.innerHTML = '';
                    return;
                }

                debounceTimeout = setTimeout(() => {
                    searchKaryawan(query, rowId, dropdown);
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }

        function searchKaryawan(query, rowId, dropdown) {
            fetch(`{{ route('tamu.search-karyawan') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => displayAutocomplete(data, rowId, dropdown))
                .catch(error => console.error('Error searching karyawan:', error));
        }

        function displayAutocomplete(karyawans, rowId, dropdown) {
            if (karyawans.length === 0) {
                dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
                dropdown.classList.add('show');
                return;
            }

            const html = karyawans
                .filter(k => !selectedKaryawan.find(sk => sk.id_karyawan === k.id_karyawan))
                .map(k => `
                                <div class="autocomplete-item" onclick="selectKaryawan(${rowId}, ${k.id_karyawan}, '${escapeHtml(k.nama_karyawan)}', '${escapeHtml(k.jabatan)}', '${escapeHtml(k.departemen)}')">
                                    <div class="autocomplete-name">${escapeHtml(k.nama_karyawan)}</div>
                                    <div class="autocomplete-detail">${escapeHtml(k.jabatan)} - ${escapeHtml(k.departemen)}</div>
                                </div>`)
                .join('');

            dropdown.innerHTML = html;
            dropdown.classList.add('show');
        }

        function selectKaryawan(rowId, id, nama, jabatan, departemen) {
            if (selectedKaryawan.find(k => k.id_karyawan === id)) {
                alert('Karyawan ini sudah dipilih di baris lain');
                return;
            }

            selectedKaryawan = selectedKaryawan.filter(k => k.rowId !== rowId);
            selectedKaryawan.push({ rowId, id_karyawan: id, nama_karyawan: nama, jabatan, departemen });

            renderKaryawanCard(rowId, nama, jabatan, departemen);
            updateHiddenInput();
        }

        function renderKaryawanCard(rowId, nama, jabatan, departemen) {
            const content = document.getElementById(`content-${rowId}`);
            content.innerHTML = `
                            <div class="karyawan-card w-full" onclick="resetKaryawanRow(${rowId})" title="Klik untuk mengganti karyawan">
                                <div class="karyawan-card-info">
                                    <div class="karyawan-card-name">${escapeHtml(nama)}</div>
                                    <div class="karyawan-card-detail">${escapeHtml(jabatan)} - ${escapeHtml(departemen)}</div>
                                </div>
                                @svg('zondicon-edit-pencil', 'w-5 h-5 text-[#084E8F]')
                            </div>`;
        }

        function updateHiddenInput() {
            const ids = selectedKaryawan.map(k => k.id_karyawan);
            document.getElementById('karyawan_ids').value = JSON.stringify(ids);
        }

        function resetKaryawanRow(rowId) {
            selectedKaryawan = selectedKaryawan.filter(k => k.rowId !== rowId);
            updateHiddenInput();

            const content = document.getElementById(`content-${rowId}`);
            content.innerHTML = `
                            <div class="w-full h-full px-2 border-2 border-[#084E8F] rounded-lg transition flex items-center">
                                <input type="text" 
                                    id="karyawan_input_${rowId}" 
                                    placeholder="Cari nama karyawan..."
                                    class="w-full karyawan-search-input"
                                    autocomplete="off"
                                    data-row-id="${rowId}">
                            </div>
                            <div id="autocomplete_dropdown_${rowId}" class="autocomplete-dropdown"></div>`;

            setupRowListeners(rowId);
        }

        function updateMinusButtonsVisibility() {
            const rows = document.querySelectorAll('[id^="karyawan-row-"]');
            const minusButtons = document.querySelectorAll('.karyawan-minus-btn');
            const shouldDisable = rows.length === 1;

            minusButtons.forEach(btn => btn.disabled = shouldDisable);
        }

        // Webcam Functions
        function openWebcamModal() {
            webcamModal.classList.add('show');
            startWebcam();
        }

        function closeWebcamModal() {
            webcamModal.classList.remove('show');
            stopWebcam();
        }

        async function startWebcam() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false
                });
                video.srcObject = stream;
            } catch (error) {
                console.error('Error accessing webcam:', error);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                closeWebcamModal();
            }
        }

        function stopWebcam() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function capturePhoto() {
            try {
                if (!video || !video.videoWidth || !video.videoHeight) {
                    throw new Error('Kamera tidak siap. Silakan coba lagi.');
                }
                
                const maxWidth = 1024;
                const maxHeight = 768;
                
                let width = video.videoWidth;
                let height = video.videoHeight;
                
                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width = Math.round(width * ratio);
                    height = Math.round(height * ratio);
                }
                
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(video, 0, 0, width, height);

                const photoData = canvas.toDataURL('image/jpeg', 0.7);
                
                if (!photoData || photoData.length < 100) {
                    throw new Error('Gagal mengambil foto. Silakan coba lagi.');
                }
                
                const sizeInMB = (photoData.length * 0.75) / (1024 * 1024);
                
                if (sizeInMB > 1.8) {
                    alert(`Ukuran foto: ${sizeInMB.toFixed(2)} MB\n\nJika upload gagal, coba ambil foto dengan pencahayaan lebih baik atau dari jarak lebih jauh.`);
                }
                
                document.getElementById('foto_ktp_base64').value = photoData;
                document.getElementById('preview_img').src = photoData;
                document.getElementById('image_preview').classList.remove('hidden');
                document.getElementById('webcam_area').classList.add('hidden');

                closeWebcamModal();
            } catch (error) {
                console.error('Error capture foto:', error);
                alert('Gagal mengambil foto: ' + error.message);
            }
        }

        webcamModal.addEventListener('click', function (e) {
            if (e.target === webcamModal) closeWebcamModal();
        });

        // Success Modal Functions
        function showSuccessModal() {
            if (successModal) successModal.classList.add('show');
        }

        function closeSuccessModal() {
            if (successModal) {
                successModal.classList.remove('show');
                const msg = document.getElementById('success_message');
                if (msg) msg.textContent = '';
            }
        }

        // Input Background Management
        function setupInputBackgrounds() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], textarea');
            inputs.forEach(input => {
                updateInputBackground(input);
                input.addEventListener('input', () => updateInputBackground(input));
                input.addEventListener('change', () => updateInputBackground(input));
            });
        }

        function updateInputBackground(input) {
            const wrapper = input.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.toggle('filled', input.value.trim() !== '');
            }
        }

        // Utility Functions
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
    </script>
@endpush