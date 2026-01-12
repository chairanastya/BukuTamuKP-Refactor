@extends('layouts.guest')
@section('title', 'Form Tamu - Buku Tamu Digital')

@push('styles')
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: white;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            border-radius: 1rem;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e40af;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #F7B218;
        }

        .autocomplete-dropdown {
            position: absolute;
            margin-top: 10px;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #084E8F;
            border-radius: 0.5rem;
            overflow-y: auto;
            z-index: 50;
            display: none;
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
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
            margin-bottom: 0.25rem;
        }

        .autocomplete-detail {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .karyawan-cards {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .karyawan-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            background-color: white;
            border: 2px solid #084E8F;
            border-radius: 0.5rem;
            width: 100%;
            box-sizing: border-box;
        }

        .karyawan-card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 10px;
            gap: 0.125rem;
            min-width: 0;
        }

        .karyawan-card-name {
            color: #084E8F;
            font-weight: 600;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .karyawan-card-detail {
            color: #6b7280;
            font-size: 0.8rem;
            line-height: 1.2;
        }

        .karyawan-card-buttons {
            display: none;
        }

        .karyawan-btn {
            display: none;
        }

        .karyawan-btn:hover {
            display: none;
        }

        .karyawan-btn svg {
            display: none;
        }

        .karyawan-search-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .karyawan-search-container {
            flex: 1;
        }

        .karyawan-action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .karyawan-add-btn,
        .karyawan-minus-btn {
            width: 50px;
            height: 50px;
            border: 2px dashed #084E8F;
            border-radius: 0.375rem;
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
            border-color: #084E8F;
        }

        .karyawan-add-btn svg,
        .karyawan-minus-btn svg {
            width: 28px;
            height: 28px;
        }

        /* Upload area */
        .upload-area {
            border: 2px dashed #3b82f6;
            border-radius: 0.75rem;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-area:hover {
            background-color: #F9FCFF;
            border-color: #2563eb;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            color: #1e40af;
        }

        .input-wrapper {
            border: 2px solid #084E8F;
            border-radius: 0.5rem;
            padding: 0.5rem;
            width: 100%;
            transition: border-color 0.2s ease;
            background-color: #F9FCFF;
        }

        .input-wrapper:focus {
            border-color: #084E8F;
            box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.1);
        }

        .input-wrapper input,
        .input-wrapper textarea {
            background-color: #F9FCFF;
            width: 100%;
        }

        .input-wrapper input.filled,
        .input-wrapper textarea.filled {
            background-color: white;
        }
    </style>
@endpush

@section('content')
    <!-- Main Form -->
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('tamu.submit') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto">
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
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-[#084E8F] font-bold mb-2">
                            Alamat Email
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="Tuliskan alamat email anda" required>
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
                            <svg class="upload-icon" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 12.5c1.38 0 2.5-1.12 2.5-2.5S13.38 7.5 12 7.5 9.5 8.62 9.5 10s1.12 2.5 2.5 2.5z" />
                                <path
                                    d="M21 5v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2zm-2 0H5v14h14V5z" />
                                <path d="M14.25 14.5l-1.5 1.875L11 14.5 8 18h8z" />
                            </svg>
                            <p class="text-[#084E8F] font-bold">Klik untuk ambil foto</p>
                        </div>

                        <!-- Preview Captured Image (shown after capture) -->
                        <div id="image_preview" class="hidden">
                            <img id="preview_img" src="" alt="Preview KTP"
                                class="w-full rounded-lg border-2 border-blue-300">
                            <button type="button" onclick="openWebcamModal()"
                                class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                Foto Ulang
                            </button>
                        </div>

                        <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                        </svg>
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
                <video id="webcam_video" autoplay playsinline class="w-full rounded-lg border-2 border-blue-300"></video>
                <button type="button" onclick="capturePhoto()"
                    class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Ambil Foto
                </button>
                <button type="button" onclick="closeWebcamModal()"
                    class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition">
                    Batalkan
                </button>
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
        let selectedKaryawan = [];
        let rowCounter = 0;

        // Initialize first row
        document.addEventListener('DOMContentLoaded', function () {
            addKaryawanRow();
        });

        function addKaryawanRow() {
            const container = document.getElementById('karyawan_rows_container');
            const rowId = rowCounter++;

            const rowHtml = `
                                                                    <div id="karyawan-row-${rowId}" class="karyawan-search-row">
                                                                        <!-- Search or Card Container -->
                                                                        <div class="karyawan-search-container relative" id="content-${rowId}">
                                                                            <div class="w-full px-2 py-2 border-2 border-[#084E8F] rounded-lg focus:border-[#084E8F] transition">
                                                                                <input type="text" 
                                                                                    id="karyawan_input_${rowId}" 
                                                                                    placeholder="Cari nama karyawan..."
                                                                                    class="w-full karyawan-search-input"
                                                                                    autocomplete="off"
                                                                                    data-row-id="${rowId}">
                                                                            </div>
                                                                            <!-- Autocomplete Dropdown -->
                                                                            <div id="autocomplete_dropdown_${rowId}" class="autocomplete-dropdown"></div>
                                                                        </div>
                                                                        <!-- Action Buttons -->
                                                                        <div class="karyawan-action-buttons">
                                                                            <button type="button" class="karyawan-add-btn" onclick="addKaryawanRow()" title="Tambah karyawan">
                                                                                <svg fill="currentColor" viewBox="0 0 24 24">
                                                                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                                                                </svg>
                                                                            </button>
                                                                            <button type="button" class="karyawan-minus-btn" onclick="removeKaryawanRow(${rowId})" title="Hapus baris">
                                                                                <svg fill="currentColor" viewBox="0 0 24 24">
                                                                                    <path d="M19 13H5v-2h14v2z"/>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                `;

            container.insertAdjacentHTML('beforeend', rowHtml);
            setupRowListeners(rowId);
        }

        function removeKaryawanRow(rowId) {
            const row = document.getElementById(`karyawan-row-${rowId}`);
            const rowData = selectedKaryawan.find(k => k.rowId === rowId);

            // Remove dari selectedKaryawan
            if (rowData) {
                selectedKaryawan = selectedKaryawan.filter(k => k.rowId !== rowId);
                updateHiddenInput();
            }

            // Remove DOM element
            if (row) row.remove();
        }

        function setupRowListeners(rowId) {
            const input = document.getElementById(`karyawan_input_${rowId}`);
            const dropdown = document.getElementById(`autocomplete_dropdown_${rowId}`);
            let debounceId;

            input.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(debounceId);

                if (query.length < 2) {
                    dropdown.classList.remove('show');
                    dropdown.innerHTML = '';
                    return;
                }

                debounceId = setTimeout(() => {
                    searchKaryawanForRow(query, rowId, dropdown);
                }, 300);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }

        function searchKaryawanForRow(query, rowId, dropdown) {
            fetch(`{{ route('tamu.search-karyawan') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayAutocompleteForRow(data, rowId, dropdown);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function displayAutocompleteForRow(karyawans, rowId, dropdown) {
            if (karyawans.length === 0) {
                dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
                dropdown.classList.add('show');
                return;
            }

            let html = '';
            karyawans.forEach(karyawan => {
                // Skip jika sudah dipilih
                if (selectedKaryawan.find(k => k.id_karyawan === karyawan.id_karyawan)) {
                    return;
                }

                html += `
                                                                        <div class="autocomplete-item" onclick="selectKaryawanForRow(${rowId}, ${karyawan.id_karyawan}, '${karyawan.nama_karyawan}', '${karyawan.jabatan}', '${karyawan.departemen}')">
                                                                            <div class="autocomplete-name">${karyawan.nama_karyawan}</div>
                                                                            <div class="autocomplete-detail">${karyawan.jabatan} - ${karyawan.departemen}</div>
                                                                        </div>
                                                                    `;
            });

            dropdown.innerHTML = html;
            dropdown.classList.add('show');
        }

        function selectKaryawanForRow(rowId, id, nama, jabatan, departemen) {
            // Check if already selected in other rows
            if (selectedKaryawan.find(k => k.id_karyawan === id)) {
                alert('Karyawan ini sudah dipilih di baris lain');
                return;
            }

            // Remove old selection for this row if exists
            selectedKaryawan = selectedKaryawan.filter(k => k.rowId !== rowId);

            // Add new selection
            selectedKaryawan.push({
                rowId: rowId,
                id_karyawan: id,
                nama_karyawan: nama,
                jabatan: jabatan,
                departemen: departemen
            });

            renderRowContent(rowId, id, nama, jabatan, departemen);
            updateHiddenInput();
        }

        function renderRowContent(rowId, id, nama, jabatan, departemen) {
            const content = document.getElementById(`content-${rowId}`);

            const cardHtml = `
                                                                    <div class="karyawan-card w-full\" style="margin: 0; padding: 0;\">
                                                                        <div class="karyawan-card-info\">
                                                                            <div class="karyawan-card-name">${nama}</div>
                                                                            <div class="karyawan-card-detail">${jabatan} - ${departemen}</div>
                                                                        </div>
                                                                    </div>
                                                                `;

            content.innerHTML = cardHtml;
        }

        function updateHiddenInput() {
            const ids = selectedKaryawan.map(k => k.id_karyawan);
            document.getElementById('karyawan_ids').value = JSON.stringify(ids);
        }

        // Webcam
        let stream = null;
        const video = document.getElementById('webcam_video');
        const canvas = document.getElementById('capture_canvas');
        const ctx = canvas.getContext('2d');
        const modal = document.getElementById('webcam_modal');

        function openWebcamModal() {
            modal.classList.add('show');
            startWebcam();
        }

        function closeWebcamModal() {
            modal.classList.remove('show');
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

        function capturePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const photoData = canvas.toDataURL('image/jpeg', 0.8);

            document.getElementById('foto_ktp_base64').value = photoData;

            document.getElementById('preview_img').src = photoData;
            document.getElementById('image_preview').classList.remove('hidden');
            document.getElementById('webcam_area').classList.add('hidden');

            closeWebcamModal();
        }

        function stopWebcam() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeWebcamModal();
            }
        });

        const successModal = document.getElementById('success_modal');
        function showSuccessModal() {
            if (successModal) successModal.classList.add('show');
        }

        function closeSuccessModal() {
            if (successModal) successModal.classList.remove('show');
            const msg = document.getElementById('success_message');
            if (msg) msg.textContent = '';
        }


        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                showSuccessModal();
            @endif

                                                                // Setup input/textarea background color based on filled state
                                                                const inputs = document.querySelectorAll('input[type="text"], input[type="email"], textarea');

            inputs.forEach(input => {
                // Check initial state
                updateInputBackground(input);

                // Listen for input changes
                input.addEventListener('input', function () {
                    updateInputBackground(this);
                });

                // Also listen for change event
                input.addEventListener('change', function () {
                    updateInputBackground(this);
                });
            });
        });

        function updateInputBackground(input) {
            if (input.value.trim() !== '') {
                input.classList.add('filled');
            } else {
                input.classList.remove('filled');
            }
        }
    </script>
@endpush