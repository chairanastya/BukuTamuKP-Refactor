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
                <button type="button" id="capture_button"
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
            // Global variables
            let capturedImages = [];
            let imageStorage, webcam, saveImagesToStorage, loadSavedImages, clearSavedImages, setCapturedImages, getCapturedImages;
            let dokumentasiInput, previewContainer, dokumentasiModal, choiceContainer, videoContainer;

            // Global functions
            window.openDokumentasiModal = function() {
                window.showModal('dokumentasi_modal');
                choiceContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');

                // Ensure capture button event listener is attached
                const captureButton = document.getElementById('capture_button');
                if (captureButton && !captureButton.hasEventListener) {
                    captureButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Capture button clicked');
                        console.log('capturePhoto function exists:', typeof window.capturePhoto);
                        try {
                            if (window.capturePhoto) {
                                console.log('Calling capturePhoto...');
                                window.capturePhoto();
                            } else {
                                console.error('capturePhoto function not found');
                            }
                        } catch (error) {
                            console.error('Error in capture button handler:', error);
                        }
                    });
                    captureButton.hasEventListener = true; // Mark as attached
                    console.log('Capture button event listener attached on modal open');
                }
            };

            window.closeDokumentasiModal = function() {
                console.log('Closing dokumentasi modal');
                window.closeModal('dokumentasi_modal');
                if (webcam) webcam.stop();
                choiceContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
                console.log('Modal closed');
            };

            window.chooseUpload = function() {
                window.closeDokumentasiModal();
                dokumentasiInput.click();
            };

            window.chooseCamera = function() {
                try {
                    console.log('chooseCamera called');
                    choiceContainer.classList.add('hidden');
                    videoContainer.classList.remove('hidden');
                    console.log('webcam object:', webcam);
                    if (webcam && webcam.start) {
                        webcam.start();
                        console.log('webcam.start called');
                    } else {
                        console.error('webcam.start not available');
                        alert('Kamera tidak tersedia. Silakan coba lagi.');
                        window.backToChoice();
                    }
                } catch (error) {
                    console.error('Error in chooseCamera:', error);
                    alert('Gagal mengakses kamera: ' + error.message);
                    window.backToChoice();
                }
            };

            window.backToChoice = function() {
                if (webcam) webcam.stop();
                videoContainer.classList.add('hidden');
                choiceContainer.classList.remove('hidden');
            };

            // Capture photo function - defined globally
            window.capturePhoto = function() {
                try {
                    console.log('=== CAPTURE PHOTO STARTED ===');

                    const video = document.getElementById('webcam_video');
                    const canvas = document.getElementById('capture_canvas');

                    if (!video || !canvas) {
                        throw new Error('Video atau canvas element tidak ditemukan');
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        throw new Error('Canvas context tidak tersedia');
                    }

                    // Wait for video to be ready
                    if (!video.videoWidth || !video.videoHeight) {
                        throw new Error('Video belum siap. Silakan tunggu sebentar.');
                    }

                    console.log('Video dimensions:', video.videoWidth, 'x', video.videoHeight);

                    // Set canvas size to match video
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
                    console.log('Canvas size set to:', width, 'x', height);

                    // Draw image to canvas
                    ctx.drawImage(video, 0, 0, width, height);
                    console.log('Image drawn to canvas successfully');

                    // Convert to data URL
                    const photoData = canvas.toDataURL('image/jpeg', 0.8);
                    console.log('Photo data length:', photoData.length);

                    if (!photoData || photoData.length < 100) {
                        throw new Error('Gagal mengambil foto. Silakan coba lagi.');
                    }

                    // Convert base64 to blob manually (more reliable than fetch)
                    const base64Data = photoData.split(',')[1];
                    const mimeType = photoData.split(',')[0].split(':')[1].split(';')[0];
                    const binaryString = atob(base64Data);
                    const bytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        bytes[i] = binaryString.charCodeAt(i);
                    }
                    const blob = new Blob([bytes], { type: mimeType });

                    console.log('Blob created:', blob);
                    const file = new File([blob], `dokumentasi-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    console.log('File created:', file);

                    // Add to captured images
                    capturedImages.push(file);
                    console.log('Captured images length:', capturedImages.length);

                    // Update UI
                    if (!dokumentasiInput) {
                        console.error('dokumentasiInput not found');
                    } else {
                        setCapturedImages(capturedImages);
                        renderPreviews();

                        // Update file input
                        const dt = new DataTransfer();
                        capturedImages.forEach(img => dt.items.add(img));
                        dokumentasiInput.files = dt.files;
                        console.log('Input files updated:', dokumentasiInput.files.length);

                        // Save to localStorage
                        saveImagesToStorage();

                        // Note: We already called renderPreviews() above, no need to trigger change event
                    }

                    console.log('=== CAPTURE PHOTO COMPLETED ===');
                    window.closeDokumentasiModal();

                } catch (error) {
                    console.error('Error in capturePhoto:', error);
                    alert('Gagal mengambil foto: ' + error.message);
                }
            };

            console.log('capturePhoto function assigned to window');

            function renderPreviews() {
                console.log('Rendering previews for', capturedImages.length, 'images');
                if (!previewContainer) {
                    console.error('previewContainer not found');
                    return;
                }
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
                console.log('Previews rendered');
            }

            function removeImage(index) {
                capturedImages.splice(index, 1);
                setCapturedImages(capturedImages);
                const dt = new DataTransfer();
                capturedImages.forEach(file => dt.items.add(file));
                dokumentasiInput.files = dt.files;

                saveImagesToStorage();

                dokumentasiInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            window.addEventListener('DOMContentLoaded', async function () {
                console.log('=== DOKUMENTASI MODAL INIT START ===');
                
                // Initialize DOM elements
                dokumentasiInput = document.getElementById('dokumentasi');
                previewContainer = document.getElementById('preview-container');
                dokumentasiModal = document.getElementById('dokumentasi_modal');
                choiceContainer = document.getElementById('choice_container');
                videoContainer = document.getElementById('video_container');
                
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
                    console.log('Loaded saved images:', capturedImages.length);

                    // Handle file input change after initialization
                    dokumentasiInput.addEventListener('change', function (e) {
                        console.log('File input change event triggered, detail:', e.detail);

                        // Skip if this event was triggered from capturePhoto to avoid duplication
                        if (e.detail && e.detail.fromCapture) {
                            console.log('Skipping change event from capture');
                            return;
                        }

                        console.log('Processing file input change...');

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

                    console.log('=== DOKUMENTASI MODAL INIT COMPLETED ===');
                } catch (e) {
                    console.error('Initialization error:', e);
                }
            });
        </script>
    @endpush
@endonce
