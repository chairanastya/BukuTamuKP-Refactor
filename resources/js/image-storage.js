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
        return new Promise((resolve) => {
            try {
                const saved = localStorage.getItem(storageKey);
                if (!saved) {
                    resolve([]);
                    return;
                }

                const imageData = JSON.parse(saved);
                if (!imageData || imageData.length === 0) {
                    resolve([]);
                    return;
                }

                const promises = imageData.map(img => {
                    return new Promise((resolve, reject) => {
                        try {
                            const base64Data = img.data.split(',')[1];
                            const mimeType = img.data.split(',')[0].split(':')[1].split(';')[0];
                            const binaryString = atob(base64Data);
                            const bytes = new Uint8Array(binaryString.length);
                            for (let i = 0; i < binaryString.length; i++) {
                                bytes[i] = binaryString.charCodeAt(i);
                            }
                            const blob = new Blob([bytes], { type: mimeType });
                            const file = new File([blob], img.name, { type: img.type });
                            resolve(file);
                        } catch (e) {
                            reject(e);
                        }
                    });
                });

                Promise.all(promises).then(files => {
                    capturedImages = files;
                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    dokumentasiInput.files = dt.files;
                    renderPreviews();
                    resolve(files);
                }).catch(e => {
                    console.error('Error loading images:', e);
                    localStorage.removeItem(storageKey);
                    resolve([]);
                });
            } catch (e) {
                console.error('Error loading from localStorage:', e);
                localStorage.removeItem(storageKey);
                resolve([]);
            }
        });
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