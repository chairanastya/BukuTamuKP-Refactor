@push('scripts')
    <script>
        // Pre-render SVG icons for use in JavaScript
        window.KARYAWAN_ICONS = {
            plus: `{!! svg('heroicon-o-plus', 'w-7 h-7')->toHtml() !!}`,
            minus: `{!! svg('heroicon-o-minus', 'w-7 h-7')->toHtml() !!}`,
            edit: `{!! svg('zondicon-edit-pencil', 'w-5 h-5 text-[#084E8F]')->toHtml() !!}`
        };

        function showSuccessModal() {
            const modal = document.getElementById('success_modal');
            const messageElement = document.getElementById('success_message');

            if (modal) {
                // Pastikan pesan ditampilkan
                if (messageElement && !messageElement.textContent.trim()) {
                    messageElement.textContent = 'Data berhasil dikirim. Email notifikasi telah dikirim ke karyawan tujuan.';
                }

                // Try to use window.showModal first
                if (typeof window.showModal === 'function') {
                    window.showModal('success_modal');
                } else {
                    // Fallback: manually show the modal
                    modal.classList.add('show');
                }
            } else {
                console.warn('[showSuccessModal] Modal with ID "success_modal" not found');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            setSearchKaryawanRoute('{{ route('tamu.search-karyawan') }}');
            setEscapeHtmlFn(escapeHtml);

            // Preload karyawan data for instant search
            preloadKaryawanData();

            restoreOldValues();

            addKaryawanRow();

            @if(session('success'))
                showSuccessModal();
            @endif

            initInputBackgrounds();
            setupFormValidation({
                formSelector: 'form[action*="tamu/submit"]',
                fields: {
                    nama: {
                        input: document.getElementById('nama_lengkap'),
                        error: document.getElementById('nama_lengkap_error')
                    },
                    email: {
                        input: document.getElementById('email'),
                        error: document.getElementById('email_error'),
                        type: 'email'
                    },
                    instansi: {
                        input: document.getElementById('instansi'),
                        error: document.getElementById('instansi_error')
                    },
                    tujuan: {
                        input: document.getElementById('tujuan'),
                        error: document.getElementById('tujuan_error')
                    },
                    karyawan: {
                        type: 'karyawan',
                        getKaryawanFn: window.getSelectedKaryawan,
                        error: document.getElementById('karyawan_error'),
                        extraElement: document.getElementById('karyawan_rows_container')
                    },
                    foto: {
                        input: document.getElementById('foto_ktp_base64'),
                        error: document.getElementById('foto_error'),
                        type: 'photo',
                        extraElement: document.getElementById('webcam_area')
                    }
                },
                onSubmit: function (e, form) {
                    // Show loading spinner sebelum form submit
                    if (typeof window.showLoading === 'function') {
                        window.showLoading();
                    }
                    return true;
                }
            });
            initWebcam();
            initModals();
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