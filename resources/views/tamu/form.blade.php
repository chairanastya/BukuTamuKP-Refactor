@extends('layouts.guest')
@section('title', 'Form Tamu - Buku Tamu Digital')

@push('styles')
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f5f5f5;
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
            color: #1f2937;
        }

        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #3b82f6;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            max-height: 250px;
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
            background-color: #f3f4f6;
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

        .karyawan-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .karyawan-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #dbeafe;
            color: #1e40af;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .karyawan-chip button {
            background: none;
            border: none;
            color: #1e40af;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .karyawan-chip button:hover {
            color: #1e3a8a;
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
            background-color: #eff6ff;
            border-color: #2563eb;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            color: #1e40af;
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
                        <label for="nama_lengkap" class="block text-blue-900 font-semibold mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Tuliskan nama lengkap anda"
                            class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:border-blue-600 transition"
                            required>
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-blue-900 font-semibold mb-2">
                            Alamat Email
                        </label>
                        <input type="email" id="email" name="email" placeholder="Tuliskan alamat email anda"
                            class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:border-blue-600 transition"
                            required>
                    </div>

                    <!-- Instansi Asal -->
                    <div>
                        <label for="instansi" class="block text-blue-900 font-semibold mb-2">
                            Instansi Asal
                        </label>
                        <input type="text" id="instansi" name="instansi" placeholder="Tuliskan instansi asal anda"
                            class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:border-blue-600 transition">
                    </div>

                    <!-- Tujuan Kedatangan -->
                    <div>
                        <label for="tujuan" class="block text-blue-900 font-semibold mb-2">
                            Tujuan Kedatangan
                        </label>
                        <textarea id="tujuan" name="tujuan" rows="4" placeholder="Jelaskan tujuan kedatangan anda"
                            class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:border-blue-600 transition resize-none"
                            required></textarea>
                    </div>

                    <!-- Karyawan yang Anda Tuju -->
                    <div>
                        <label for="karyawan_input" class="block text-blue-900 font-semibold mb-2">
                            Karyawan yang Anda Tuju
                        </label>
                        <div class="relative">
                            <input type="text" id="karyawan_input" placeholder="Cari nama karyawan..."
                                class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:border-blue-600 transition"
                                autocomplete="off">

                            <!-- Autocomplete Dropdown -->
                            <div id="autocomplete_dropdown" class="autocomplete-dropdown"></div>
                        </div>

                        <!-- Selected Karyawan Chips -->
                        <div id="selected_karyawan" class="karyawan-chips"></div>

                        <!-- Hidden input untuk menyimpan ID karyawan yang dipilih -->
                        <input type="hidden" id="karyawan_ids" name="karyawan_ids" value="[]">
                    </div>
                </div>

                <!-- Right Column - Webcam KTP & Submit -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Webcam Foto KTP -->
                    <div>
                        <label class="block text-blue-900 font-semibold mb-2">
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
                            <p class="text-blue-900 font-semibold">Klik untuk ambil foto</p>
                            <p class="text-gray-500 text-sm mt-1">Menggunakan kamera</p>
                        </div>

                        <!-- Preview Captured Image (shown after capture) -->
                        <div id="image_preview" class="hidden">
                            <img id="preview_img" src="" alt="Preview KTP"
                                class="w-full rounded-lg border-2 border-blue-300">
                            <button type="button" onclick="openWebcamModal()"
                                class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Foto Ulang
                            </button>
                        </div>

                        <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
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
                <video id="webcam_video" autoplay playsinline
                    class="w-full rounded-lg border-2 border-blue-300"></video>
                <button type="button" onclick="capturePhoto()"
                    class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                    Ambil Foto
                </button>
                <button type="button" onclick="closeWebcamModal()"
                    class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition">
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
                    <button type="button" onclick="closeSuccessModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedKaryawan = [];
        let debounceTimer;

        const input = document.getElementById('karyawan_input');
        const dropdown = document.getElementById('autocomplete_dropdown');

        input.addEventListener('input', function () {
            const query = this.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                dropdown.classList.remove('show');
                dropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                searchKaryawan(query);
            }, 300);
        });

        function searchKaryawan(query) {
            fetch(`{{ route('tamu.search-karyawan') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayAutocomplete(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function displayAutocomplete(karyawans) {
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
                                                                <div class="autocomplete-item" onclick="selectKaryawan(${karyawan.id_karyawan}, '${karyawan.nama_karyawan}', '${karyawan.jabatan}', '${karyawan.departemen}')">
                                                                    <div class="autocomplete-name">${karyawan.nama_karyawan}</div>
                                                                    <div class="autocomplete-detail">${karyawan.jabatan} - ${karyawan.departemen}</div>
                                                                </div>
                                                            `;
            });

            dropdown.innerHTML = html;
            dropdown.classList.add('show');
        }

        function selectKaryawan(id, nama, jabatan, departemen) {
            selectedKaryawan.push({ id_karyawan: id, nama_karyawan: nama, jabatan, departemen });

            renderSelectedKaryawan();

            input.value = '';
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';

            updateHiddenInput();
        }

        function removeKaryawan(id) {
            selectedKaryawan = selectedKaryawan.filter(k => k.id_karyawan !== id);
            renderSelectedKaryawan();
            updateHiddenInput();
        }

        function renderSelectedKaryawan() {
            const container = document.getElementById('selected_karyawan');

            if (selectedKaryawan.length === 0) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            selectedKaryawan.forEach(karyawan => {
                html += `
                                                                <div class="karyawan-chip">
                                                                    <span>${karyawan.nama_karyawan}</span>
                                                                    <button type="button" onclick="removeKaryawan(${karyawan.id_karyawan})">&times;</button>
                                                                </div>
                                                            `;
            });

            container.innerHTML = html;
        }

        function updateHiddenInput() {
            const ids = selectedKaryawan.map(k => k.id_karyawan);
            document.getElementById('karyawan_ids').value = JSON.stringify(ids);
        }

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

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

        modal.addEventListener('click', function(e) {
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
        });
    </script>
@endpush