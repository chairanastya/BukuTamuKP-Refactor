export function createImageStorage(token, dokumentasiInput, renderPreviews) {
    const storageKey = `notulensi_images_${token}`;
    let capturedImages = [];

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

    function setCapturedImages(images) {
        capturedImages = images;
    }

    function getCapturedImages() {
        return capturedImages;
    }

    return {
        saveImagesToStorage,
        loadSavedImages,
        clearSavedImages,
        setCapturedImages,
        getCapturedImages
    };
}

export function clearOldImageStorages() {
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('notulensi_images_')) {
            localStorage.removeItem(key);
        }
    });
}