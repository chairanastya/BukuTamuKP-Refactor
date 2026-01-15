@extends('layouts.guest')
@section('title', 'Notulensi & Dokumentasi')
@section('header', 'Buku Tamu Digital')

@push('styles')
    <style>
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

        .input-wrapper.readonly {
            background-color: white;
        }

        .input-wrapper:focus-within {
            box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.1);
        }

        .input-wrapper input,
        .input-wrapper textarea,
        .input-wrapper select {
            background-color: transparent;
            width: 100%;
            border: none;
            outline: none;
        }

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
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-[#084E8F] mb-2">
                    Notulensi & Dokumentasi
                </h1>
                <p class="text-gray-600">
                    Mohon isi notulensi rapat dengan lengkap untuk dokumentasi
                </p>
            </div>

            <form id="notulensi-form" action="{{ route('notulensi.store', $token) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Baris 1: Nama Lengkap & Email -->
                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Nama Lengkap</label>
                        <div class="input-wrapper readonly">
                            <input type="text" value="{{ $kunjungan->tamu->nama_tamu }}" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Alamat Email</label>
                        <div class="input-wrapper readonly">
                            <input type="text" value="{{ $kunjungan->tamu->email_tamu }}" readonly>
                        </div>
                    </div>

                    <!-- Baris 2: Instansi Asal & Karyawan Tertuju -->
                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Instansi Asal</label>
                        <div class="input-wrapper readonly">
                            <input type="text" value="{{ $kunjungan->tamu->instansi_tamu ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Karyawan Tertuju</label>
                        @if($kunjungan->karyawan->count() == 1)
                            <div class="input-wrapper readonly">
                                <input type="text"
                                    value="{{ $kunjungan->karyawan->first()->nama_karyawan }} - {{ $kunjungan->karyawan->first()->jabatan }}"
                                    readonly>
                            </div>
                        @else
                            <div class="input-wrapper" style="padding: 0; overflow: hidden;">
                                <button type="button" onclick="openKaryawanModal()"
                                    class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-semibold py-[16px] px-4 transition flex items-center justify-center gap-2">
                                    @svg('heroicon-m-users', 'w-5 h-5')
                                    Lihat Detail ({{ $kunjungan->karyawan->count() }} Karyawan)
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Baris 3: Tujuan Kunjungan/Rapat -->
                    <div class="lg:col-span-2">
                        <label class="block text-[#084E8F] font-semibold mb-2">Tujuan Kunjungan/Rapat</label>
                        <div class="input-wrapper readonly">
                            <textarea rows="3" readonly>{{ $kunjungan->tujuan_kunjungan }}</textarea>
                        </div>
                    </div>

                    <!-- Baris 4: Tanggal & Jam -->
                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Tanggal Kunjungan/Rapat</label>
                        <div class="input-wrapper readonly">
                            <input type="text"
                                value="{{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('l, d F Y') }}"
                                readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Jam Kunjungan/Rapat</label>
                        <div class="flex gap-2 items-center">
                            <div class="input-wrapper readonly flex-1">
                                <input type="text" value="{{ $kunjungan->jam_mulai }}" readonly>
                            </div>
                            <span class="text-gray-600">—</span>
                            <div class="input-wrapper flex-1">
                                <input type="time" name="jam_selesai"
                                    value="{{ old('jam_selesai', $kunjungan->jam_selesai) }}" required>
                            </div>
                        </div>
                        @error('jam_selesai')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Baris 5: Anggota Kunjungan/Rapat -->
                    <div class="lg:col-span-2">
                        <label for="anggota_rapat" class="block text-[#084E8F] font-semibold mb-2">
                            Anggota Kunjungan/Rapat <span class="text-gray-500 text-sm font-normal">(Opsional)</span>
                        </label>
                        <div class="input-wrapper">
                            <textarea name="anggota_rapat" id="anggota_rapat" rows="4"
                                placeholder="Sebutkan anggota lain yang hadir jika ada...">{{ old('anggota_rapat') }}</textarea>
                        </div>
                        @error('anggota_rapat')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Baris 6: Notulensi Rapat -->
                    <div class="lg:col-span-2">
                        <label for="isi_notulensi" class="block text-[#084E8F] font-semibold mb-2">
                            Notulensi Kunjungan/Rapat <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <textarea name="isi_notulensi" id="isi_notulensi" rows="12" required
                                placeholder="Tuliskan ringkasan pembahasan, keputusan yang diambil, dan tindak lanjut yang diperlukan...">{{ old('isi_notulensi') }}</textarea>
                        </div>
                        @error('isi_notulensi')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-2">Minimal 50 karakter. Sertakan detail lengkap untuk dokumentasi
                            yang baik.</p>
                    </div>

                    <!-- Baris 7: Dokumentasi Rapat -->
                    <div class="lg:col-span-2">
                        <label class="block text-[#084E8F] font-semibold mb-2">
                            Dokumentasi Kunjungan/Rapat <span class="text-gray-500 text-sm font-normal">(Opsional)</span>
                        </label>
                        <div class="upload-area" onclick="openDokumentasiModal()">
                            @svg('zondicon-camera', 'upload-icon')
                            <p class="text-[#084E8F] font-semibold mb-2">Klik untuk ambil foto atau unggah file</p>
                            <p class="text-gray-500 text-sm">Format: JPG, PNG, maksimal 5MB per file</p>
                        </div>
                        <input type="file" id="dokumentasi" name="dokumentasi[]" accept="image/*" multiple class="hidden">
                        <div id="preview-container" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                        @error('dokumentasi')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="lg:col-span-2">
                        <button type="submit"
                            class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                            Simpan Notulensi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Popup untuk Dokumentasi -->
    <div id="dokumentasi_modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Dokumentasi Kunjungan/Rapat</h2>
                <button type="button" class="modal-close" onclick="closeDokumentasiModal()">&times;</button>
            </div>

            <!-- Pilihan: Upload atau Ambil Foto -->
            <div id="choice_container" class="px-1">
                <p class="text-gray-700 mb-4">Pilih cara menambahkan dokumentasi:</p>
                <div class="flex gap-3">
                    <button type="button" onclick="chooseUpload()"
                        class="flex-1 bg-[#47B9AE] hover:opacity-90 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        @svg('heroicon-o-photo', 'w-5 h-5')
                        Upload Gambar
                    </button>
                    <button type="button" onclick="chooseCamera()"
                        class="flex-1 bg-[#084E8F] hover:!bg-[#F7B218] text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        @svg('zondicon-camera', 'w-5 h-5')
                        Ambil Foto
                    </button>
                </div>
            </div>

            <!-- Video Preview untuk Kamera -->
            <div id="video_container" class="hidden">
                <video id="webcam_video" autoplay playsinline class="w-full rounded-lg mb-4 bg-gray-100"></video>
                <div class="flex gap-3">
                    <button type="button" onclick="backToChoice()"
                        class="flex-1 bg-[#D9D9D9] hover:opacity-90 text-black font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        @svg('heroicon-o-arrow-left', 'w-5 h-5')
                        Kembali
                    </button>
                    <button type="button" onclick="capturePhoto()"
                        class="flex-1 bg-[#084E8F] hover:!bg-[#F7B218] text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        @svg('zondicon-camera', 'w-5 h-5')
                        Ambil Foto
                    </button>
                </div>
            </div>

            <!-- Canvas (hidden, untuk capture) -->
            <canvas id="capture_canvas" class="hidden"></canvas>
        </div>
    </div>

    <!-- Modal Popup untuk Daftar Karyawan -->
    @if($kunjungan->karyawan->count() > 1)
        <div id="karyawan_modal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Daftar Karyawan Tertuju</h2>
                    <button type="button" class="modal-close" onclick="closeKaryawanModal()">&times;</button>
                </div>
                <div class="px-1">
                    <p class="text-gray-600 mb-4">Total {{ $kunjungan->karyawan->count() }} karyawan yang terlibat dalam
                        kunjungan ini:</p>
                    <div class="space-y-3">
                        @foreach($kunjungan->karyawan as $index => $karyawan)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-[#084E8F] text-white rounded-full flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $karyawan->nama_karyawan }}</p>
                                    <p class="text-sm text-gray-600">{{ $karyawan->jabatan }}</p>
                                    @if($karyawan->email_karyawan)
                                        <p class="text-sm text-gray-500 mt-1">{{ $karyawan->email_karyawan }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        <button type="button" onclick="closeKaryawanModal()"
                            class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-4 rounded-lg transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            /* Modal Styles */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                padding: 20px;
            }

            .modal-overlay.show {
                display: flex;
            }

            .modal-content {
                background-color: white;
                border-radius: 12px;
                padding: 24px;
                max-width: 600px;
                width: 100%;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 2px solid #e5e7eb;
            }

            .modal-title {
                font-size: 1.5rem;
                font-weight: bold;
                color: #084E8F;
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
                transition: color 0.2s;
            }

            .modal-close:hover {
                color: #ef4444;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const dokumentasiInput = document.getElementById('dokumentasi');
            const previewContainer = document.getElementById('preview-container');
            const dokumentasiModal = document.getElementById('dokumentasi_modal');
            const choiceContainer = document.getElementById('choice_container');
            const videoContainer = document.getElementById('video_container');
            const video = document.getElementById('webcam_video');
            const canvas = document.getElementById('capture_canvas');
            const ctx = canvas.getContext('2d');
            const storageKey = 'notulensi_images_{{ $token }}';

            let stream = null;
            let capturedImages = [];

            window.addEventListener('DOMContentLoaded', function () {
                loadSavedImages();
            });

            function saveImagesToStorage() {
                const imageData = [];
                capturedImages.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imageData.push({
                            name: file.name,
                            type: file.type,
                            data: e.target.result
                        });

                        if (imageData.length === capturedImages.length) {
                            try {
                                localStorage.setItem(storageKey, JSON.stringify(imageData));
                            } catch (e) {
                                console.error('Error saving to localStorage:', e);
                            }
                        }
                    };
                    reader.readAsDataURL(file);
                });

                if (capturedImages.length === 0) {
                    localStorage.removeItem(storageKey);
                }
            }

            function loadSavedImages() {
                try {
                    const saved = localStorage.getItem(storageKey);
                    if (!saved) return;

                    const imageData = JSON.parse(saved);
                    if (!imageData || imageData.length === 0) return;

                    const promises = imageData.map(img => {
                        return fetch(img.data)
                            .then(res => res.blob())
                            .then(blob => new File([blob], img.name, { type: img.type }));
                    });

                    Promise.all(promises).then(files => {
                        capturedImages = files;

                        const dt = new DataTransfer();
                        files.forEach(file => dt.items.add(file));
                        dokumentasiInput.files = dt.files;

                        renderPreviews();
                    });
                } catch (e) {
                    console.error('Error loading from localStorage:', e);
                    localStorage.removeItem(storageKey);
                }
            }

            function clearSavedImages() {
                localStorage.removeItem(storageKey);
            }

            function renderPreviews() {
                previewContainer.innerHTML = '';
                capturedImages.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                                                                                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border-2 border-[#084E8F]">
                                                                                                    <button type="button" onclick="removeImage(${index})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                                                                        ×
                                                                                                    </button>
                                                                                                `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            const karyawanModal = document.getElementById('karyawan_modal');

            function openKaryawanModal() {
                if (karyawanModal) {
                    karyawanModal.classList.add('show');
                }
            }

            function closeKaryawanModal() {
                if (karyawanModal) {
                    karyawanModal.classList.remove('show');
                }
            }

            // Close modal on backdrop click
            if (karyawanModal) {
                karyawanModal.addEventListener('click', function (e) {
                    if (e.target === karyawanModal) closeKaryawanModal();
                });
            }

            // Open modal
            function openDokumentasiModal() {
                dokumentasiModal.classList.add('show');
                choiceContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
            }

            // Close modal
            function closeDokumentasiModal() {
                dokumentasiModal.classList.remove('show');
                stopWebcam();
                choiceContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
            }

            // Choose upload
            function chooseUpload() {
                closeDokumentasiModal();
                dokumentasiInput.click();
            }

            // Choose camera
            function chooseCamera() {
                choiceContainer.classList.add('hidden');
                videoContainer.classList.remove('hidden');
                startWebcam();
            }

            // Back to choice
            function backToChoice() {
                stopWebcam();
                videoContainer.classList.add('hidden');
                choiceContainer.classList.remove('hidden');
            }

            // Start webcam
            async function startWebcam() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' }, // rear camera
                        audio: false
                    });
                    video.srcObject = stream;
                } catch (error) {
                    console.error('Error accessing webcam:', error);
                    alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                    backToChoice();
                }
            }

            // Stop webcam
            function stopWebcam() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }

            // Capture photo
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

                    const photoData = canvas.toDataURL('image/jpeg', 0.8);

                    if (!photoData || photoData.length < 100) {
                        throw new Error('Gagal mengambil foto. Silakan coba lagi.');
                    }

                    // Convert base64 to file
                    fetch(photoData)
                        .then(res => res.blob())
                        .then(blob => {
                            const file = new File([blob], `dokumentasi-${Date.now()}.jpg`, { type: 'image/jpeg' });
                            capturedImages.push(file);

                            // Update file input
                            const dt = new DataTransfer();
                            capturedImages.forEach(img => dt.items.add(img));
                            dokumentasiInput.files = dt.files;

                            // Save to localStorage
                            saveImagesToStorage();

                            // Trigger change event to update preview
                            dokumentasiInput.dispatchEvent(new Event('change'));

                            closeDokumentasiModal();
                        });

                } catch (error) {
                    console.error('Error capture foto:', error);
                    alert('Gagal mengambil foto: ' + error.message);
                }
            }

            // Handle file input change
            dokumentasiInput.addEventListener('change', function (e) {
                const files = Array.from(e.target.files);

                // Merge with existing captured images, avoiding duplicates
                const existingNames = new Set(capturedImages.map(f => f.name));
                const newFiles = files.filter(f => !existingNames.has(f.name));

                if (newFiles.length > 0) {
                    capturedImages = [...capturedImages, ...newFiles];
                }

                // Update the file input with all captured images
                const dt = new DataTransfer();
                capturedImages.forEach(img => dt.items.add(img));
                dokumentasiInput.files = dt.files;

                // Save to localStorage
                saveImagesToStorage();

                // Re-render all previews
                renderPreviews();
            });

            function removeImage(index) {
                capturedImages.splice(index, 1);
                const dt = new DataTransfer();
                capturedImages.forEach(file => dt.items.add(file));
                dokumentasiInput.files = dt.files;

                saveImagesToStorage();

                dokumentasiInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            dokumentasiModal.addEventListener('click', function (e) {
                if (e.target === dokumentasiModal) closeDokumentasiModal();
            });

            const form = document.getElementById('notulensi-form');
            if (form) {
                form.addEventListener('submit', function () {
                    sessionStorage.setItem('form_submitted_{{ $token }}', 'true');
                });
            }

            window.addEventListener('load', function () {
                const wasSubmitted = sessionStorage.getItem('form_submitted_{{ $token }}');
                if (wasSubmitted) {
                    const hasErrors = document.querySelector('.text-red-500');
                    if (!hasErrors) {
                    } else {
                    }
                    sessionStorage.removeItem('form_submitted_{{ $token }}');
                }
            });

            // Toggle filled class based on input value
            function updateFilledState(element) {
                const wrapper = element.closest('.input-wrapper');
                if (wrapper && !wrapper.classList.contains('readonly')) {
                    if (element.value.trim() !== '') {
                        wrapper.classList.add('filled');
                    } else {
                        wrapper.classList.remove('filled');
                    }
                }
            }

            // Check all inputs on page load
            document.querySelectorAll('.input-wrapper input, .input-wrapper textarea').forEach(element => {
                updateFilledState(element);
                element.addEventListener('input', function () {
                    updateFilledState(this);
                });
            });
        </script>
    @endpush
@endsection