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

            let capturedImages = [];

            let imageStorage, webcam, saveImagesToStorage, loadSavedImages, clearSavedImages, setCapturedImages, getCapturedImages;

            window.addEventListener('DOMContentLoaded', async function () {
                // Initialize after window is ready
                try {
                    imageStorage = window.createImageStorage('{{ $token }}', dokumentasiInput, renderPreviews);
                    ({ saveImagesToStorage, loadSavedImages, clearSavedImages, setCapturedImages, getCapturedImages } = imageStorage);

                    webcam = window.initWebcam({
                        videoId: 'webcam_video',
                        canvasId: 'capture_canvas',
                        modalId: 'dokumentasi_modal',
                        qualityJpeg: 0.8,
                        frameWidthPercent: 0.85,
                        frameAspectRatio: 1.586
                    });

                    capturedImages = await loadSavedImages();
                } catch (e) {
                    console.error('Initialization error:', e);
                }
            });

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
                window.showModal('dokumentasi_modal');
                choiceContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
            }

            window.openDokumentasiModal = openDokumentasiModal;

            // Close modal
            function closeDokumentasiModal() {
                window.closeModal('dokumentasi_modal');
                webcam.stop();
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
                console.log('chooseCamera called');
                choiceContainer.classList.add('hidden');
                videoContainer.classList.remove('hidden');
                console.log('webcam object:', webcam);
                if (webcam && webcam.start) {
                    webcam.start();
                    console.log('webcam.start called');
                } else {
                    console.error('webcam.start not available');
                }
            }

            // Back to choice
            function backToChoice() {
                webcam.stop();
                videoContainer.classList.add('hidden');
                choiceContainer.classList.remove('hidden');
            }

            // Capture photo
            function capturePhoto() {
                console.log('capturePhoto called');
                try {
                    const video = document.getElementById('webcam_video');
                    const canvas = document.getElementById('capture_canvas');
                    const ctx = canvas.getContext('2d');

                    console.log('video element:', video);
                    console.log('video readyState:', video ? video.readyState : 'no video');
                    console.log('video dimensions:', video ? video.videoWidth + 'x' + video.videoHeight : 'no dimensions');

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

                    console.log('Captured photo data length:', photoData.length);

                    // Convert base64 to file
                    fetch(photoData)
                        .then(res => res.blob())
                        .then(blob => {
                            console.log('Blob created:', blob);
                            const file = new File([blob], `dokumentasi-${Date.now()}.jpg`, { type: 'image/jpeg' });
                            console.log('File created:', file);
                            capturedImages.push(file);
                            console.log('Captured images length:', capturedImages.length);
                            setCapturedImages(capturedImages);

                            // Update file input
                            const dt = new DataTransfer();
                            capturedImages.forEach(img => dt.items.add(img));
                            dokumentasiInput.files = dt.files;
                            console.log('Input files updated:', dokumentasiInput.files.length);

                            // Save to localStorage
                            saveImagesToStorage();

                            // Trigger change event to update preview
                            dokumentasiInput.dispatchEvent(new Event('change'));

                            closeDokumentasiModal();
                        })
                        .catch(error => {
                            console.error('Error in photo processing:', error);
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
                    setCapturedImages(capturedImages);
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
                setCapturedImages(capturedImages);
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
