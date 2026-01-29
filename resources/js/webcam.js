export function initWebcam(options = {}) {
    const {
        videoId = 'webcam_video',
        canvasId = 'capture_canvas',
        modalId = 'webcam_modal',
        photoFieldId = 'foto_ktp_base64',
        previewImgId = 'preview_img',
        previewAreaId = 'image_preview',
        uploadAreaId = 'webcam_area',
        qualityJpeg = 0.8,
        frameWidthPercent = 0.85,
        frameAspectRatio = 1.586
    } = options;

    let stream = null;
    const video = document.getElementById(videoId);
    const canvas = document.getElementById(canvasId);
    const ctx = canvas ? canvas.getContext('2d') : null;
    const modal = document.getElementById(modalId);

    if (!video || !canvas || !ctx || !modal) {
        console.error('[initWebcam] Required elements not found');
        return;
    }

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
            console.error('[initWebcam] Error accessing webcam:', error);
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
            if (!canvas || !ctx) {
                throw new Error('Canvas tidak tersedia.');
            }

            const videoRect = video.getBoundingClientRect();
            const frameWidth = videoRect.width * frameWidthPercent;
            const frameHeight = frameWidth / frameAspectRatio;
            const offsetX = (video.videoWidth - frameWidth) / 2;
            const offsetY = (video.videoHeight - frameHeight) / 2;

            canvas.width = frameWidth;
            canvas.height = frameHeight;
            ctx.drawImage(video, offsetX, offsetY, frameWidth, frameHeight, 0, 0, frameWidth, frameHeight);

            const photoData = canvas.toDataURL('image/jpeg', qualityJpeg);
            
            const photoField = document.getElementById(photoFieldId);
            const previewImg = document.getElementById(previewImgId);
            const previewArea = document.getElementById(previewAreaId);
            const uploadArea = document.getElementById(uploadAreaId);

            if (photoField) photoField.value = photoData;
            if (previewImg) previewImg.src = photoData;
            if (previewArea) previewArea.classList.remove('hidden');
            if (uploadArea) uploadArea.classList.add('hidden');

            closeWebcamModal();
        } catch (error) {
            console.error('[initWebcam] Error capturing photo:', error);
            alert('Gagal mengambil foto. ' + error.message);
        }
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeWebcamModal();
    });

    window.openWebcamModal = openWebcamModal;
    window.closeWebcamModal = closeWebcamModal;
    window.capturePhoto = capturePhoto;

    return {
        open: openWebcamModal,
        close: closeWebcamModal,
        capture: capturePhoto,
        stop: stopWebcam,
        start: startWebcam
    };
}

export default initWebcam;
