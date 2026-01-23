@push('scripts')
    <script>
        let selectedKaryawan = [];
        let rowCounter = 0;
        let stream = null;

        const video = document.getElementById('webcam_video');
        const canvas = document.getElementById('capture_canvas');
        const ctx = canvas.getContext('2d');
        const webcamModal = document.getElementById('webcam_modal');
        const successModal = document.getElementById('success_modal');

        document.addEventListener('DOMContentLoaded', function () {
            restoreOldValues();

            addKaryawanRow();

            @if(session('success'))
                showSuccessModal();
            @endif

            setupInputBackgrounds();
            setupFormValidation();
        });

        function restoreOldValues() {
            const fotoKtpBase64 = document.getElementById('foto_ktp_base64').value;
            if (fotoKtpBase64 && fotoKtpBase64.trim() !== '') {
                const previewImg = document.getElementById('preview_img');
                const imagePreview = document.getElementById('image_preview');
                const webcamArea = document.getElementById('webcam_area');

                if (previewImg && imagePreview && webcamArea) {
                    previewImg.src = fotoKtpBase64;
                    imagePreview.classList.remove('hidden');
                    webcamArea.classList.add('hidden');
                }
            }

            const karyawanIdsInput = document.getElementById('karyawan_ids');
            if (karyawanIdsInput && karyawanIdsInput.value) {
                try {
                    const karyawanIds = JSON.parse(karyawanIdsInput.value);
                    if (Array.isArray(karyawanIds) && karyawanIds.length > 0) {
                        selectedKaryawan = karyawanIds.map(id => ({
                            id_karyawan: id,
                            nama_karyawan: 'Loading...',
                            rowId: -1
                        }));
                    }
                } catch (e) {
                    console.error('Error parsing karyawan_ids:', e);
                }
            }
        }

        function setupFormValidation() {
            const form = document.querySelector('form[action*="tamu/submit"]');
            if (!form) {
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');

            let isSubmitting = false;

            const inputs = {
                nama: document.getElementById('nama_lengkap'),
                email: document.getElementById('email'),
                instansi: document.getElementById('instansi'),
                tujuan: document.getElementById('tujuan'),
                foto: document.getElementById('foto_ktp_base64')
            };

            const errors = {
                nama: document.getElementById('nama_lengkap_error'),
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
                e.preventDefault();

                if (isSubmitting) {
                    return false;
                }

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
                    if (errors.karyawan) {
                        errors.karyawan.classList.add('show');
                        setTimeout(() => {
                            errors.karyawan.classList.remove('show');
                        }, 5000);
                    }
                    const firstRow = karyawanContainer.querySelector('.karyawan-search-container');
                    if (firstRow) {
                        const inputWrapper = firstRow.querySelector('.border-2');
                        if (inputWrapper) {
                            inputWrapper.classList.add('border-red-600');
                            inputWrapper.classList.remove('border-[#084E8F]');
                            setTimeout(() => {
                                inputWrapper.classList.remove('border-red-600');
                                inputWrapper.classList.add('border-[#084E8F]');
                            }, 5000);
                        }
                    }
                    if (!firstErrorElement) firstErrorElement = karyawanContainer;
                }

                if (!inputs.foto.value?.trim()) {
                    e.preventDefault();
                    hasError = true;
                    if (errors.foto) {
                        errors.foto.classList.add('show');
                    }
                    webcamArea.classList.add('error');
                    if (!firstErrorElement) firstErrorElement = webcamArea;

                    setTimeout(() => {
                        if (errors.foto) {
                            errors.foto.classList.remove('show');
                        }
                        webcamArea.classList.remove('error');
                    }, 5000);
                }

                if (hasError && firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                isSubmitting = true;
                if (submitButton) {
                    submitButton.disabled = true;
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<span class="animate-pulse">Mengirim...</span>';

                    setTimeout(() => {
                        if (isSubmitting) {
                            isSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        }
                    }, 10000);
                }

                e.target.submit();
            });
        }

        function showError(input, errorElement) {
            if (errorElement) {
                errorElement.classList.add('show');
            }

            const wrapper = input.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.add('error');
            }

            setTimeout(() => {
                if (errorElement) {
                    errorElement.classList.remove('show');
                }
                if (wrapper) {
                    wrapper.classList.remove('error');
                }
            }, 5000);
        }

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
                console.log('Mengambil foto...');

                if (!video || !video.videoWidth || !video.videoHeight) {
                    throw new Error('Kamera tidak siap. Silakan coba lagi.');
                }
                if (!canvas || !ctx) {
                    throw new Error('Canvas tidak tersedia.');
                }

                console.log(`Video size: ${video.videoWidth}x${video.videoHeight}`);

                const videoRect = video.getBoundingClientRect();
                const frameWidthPercent = 0.85;
                const aspectRatio = 1.586;

                const frameWidth = videoRect.width * frameWidthPercent;
                const frameHeight = frameWidth / aspectRatio;

                const frameX = (videoRect.width - frameWidth) / 2;
                const frameY = (videoRect.height - frameHeight) / 2;

                const scaleX = video.videoWidth / videoRect.width;
                const scaleY = video.videoHeight / videoRect.height;

                let sourceX = Math.round(frameX * scaleX);
                let sourceY = Math.round(frameY * scaleY);
                let sourceWidth = Math.round(frameWidth * scaleX);
                let sourceHeight = Math.round(frameHeight * scaleY);

                sourceX = Math.max(0, Math.min(sourceX, video.videoWidth - 1));
                sourceY = Math.max(0, Math.min(sourceY, video.videoHeight - 1));
                sourceWidth = Math.min(sourceWidth, video.videoWidth - sourceX);
                sourceHeight = Math.min(sourceHeight, video.videoHeight - sourceY);

                if (sourceWidth <= 0 || sourceHeight <= 0 || isNaN(sourceWidth) || isNaN(sourceHeight)) {
                    console.warn('Koordinat crop tidak valid, menggunakan full video');
                    sourceX = 0;
                    sourceY = 0;
                    sourceWidth = video.videoWidth;
                    sourceHeight = video.videoHeight;
                }

                console.log(`Frame area: ${sourceWidth}x${sourceHeight} at (${sourceX}, ${sourceY})`);

                const maxWidth = 800;
                let finalWidth = sourceWidth;
                let finalHeight = sourceHeight;

                if (finalWidth > maxWidth) {
                    finalHeight = Math.round((maxWidth / finalWidth) * finalHeight);
                    finalWidth = maxWidth;
                }

                canvas.width = finalWidth;
                canvas.height = finalHeight;
                console.log(`Canvas size: ${canvas.width}x${canvas.height}`);

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                ctx.drawImage(
                    video,
                    sourceX, sourceY, sourceWidth, sourceHeight,
                    0, 0, canvas.width, canvas.height
                );

                const photoData = canvas.toDataURL('image/jpeg', 0.8);

                if (!photoData || photoData.length < 100) {
                    throw new Error('Gagal mengambil foto. Silakan coba lagi.');
                }

                const sizeInMB = (photoData.length * 0.75) / (1024 * 1024);
                console.log(`Ukuran foto: ${sizeInMB.toFixed(2)} MB`);

                if (sizeInMB > 1.8) {
                    console.warn('Foto cukup besar!');
                    alert(`Ukuran foto: ${sizeInMB.toFixed(2)} MB\n\nJika upload gagal, coba ambil foto dengan pencahayaan lebih baik atau dari jarak lebih jauh.`);
                }

                document.getElementById('foto_ktp_base64').value = photoData;
                document.getElementById('preview_img').src = photoData;
                document.getElementById('image_preview').classList.remove('hidden');
                document.getElementById('webcam_area').classList.add('hidden');

                console.log('Foto berhasil diambil (cropped ke area frame)');
                closeWebcamModal();
            } catch (error) {
                console.error('Error capture foto:', error);
                alert('Gagal mengambil foto: ' + error.message);
            }
        }

        webcamModal.addEventListener('click', function (e) {
            if (e.target === webcamModal) closeWebcamModal();
        });

        function showSuccessModal() {
            if (successModal) successModal.classList.add('show');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                if (modalId === 'success_modal') {
                    const msg = document.getElementById('success_message');
                    if (msg) msg.textContent = '';
                }
            }
        }

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