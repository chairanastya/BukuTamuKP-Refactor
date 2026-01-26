@props(['token'])

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

@once
    @push('styles')
        <style>
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
        </script>
    @endpush
@endonce
