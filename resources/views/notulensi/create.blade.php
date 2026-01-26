@extends('layouts.guest')
@section('title', 'Notulensi & Dokumentasi')
@section('header', 'Buku Tamu Digital')

@push('styles')
    <style>
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
                    <x-input-wrapper 
                        label="Nama Lengkap"
                        type="text"
                        value="{{ $kunjungan->tamu->nama_tamu }}"
                        readonly />

                    <x-input-wrapper 
                        label="Alamat Email"
                        type="text"
                        value="{{ $kunjungan->tamu->email_tamu }}"
                        readonly />

                    <!-- Baris 2: Instansi Asal & Karyawan Tertuju -->
                    <x-input-wrapper 
                        label="Instansi Asal"
                        type="text"
                        value="{{ $kunjungan->tamu->instansi_tamu ?? '-' }}"
                        readonly />

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
                    <x-input-wrapper 
                        label="Tujuan Kunjungan/Rapat"
                        type="textarea"
                        value="{{ $kunjungan->tujuan_kunjungan }}"
                        rows="3"
                        readonly 
                        class="lg:col-span-2" />

                    <!-- Baris 4: Tanggal & Jam -->
                    <x-input-wrapper 
                        label="Tanggal Kunjungan/Rapat"
                        type="text"
                        value="{{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('l, d F Y') }}"
                        readonly />

                    <div>
                        <label class="block text-[#084E8F] font-semibold mb-2">Jam Kunjungan/Rapat</label>
                        <div class="flex gap-2 items-center">
                            <div class="input-wrapper readonly flex-1">
                                <input type="text" value="{{ $kunjungan->jam_mulai }}" readonly>
                            </div>
                            <span class="text-gray-600">—</span>
                            <div class="input-wrapper flex-1">
                                    <input type="time" name="jam_selesai"
                                        value="{{ old('jam_selesai', $kunjungan->jam_selesai) }}">
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                    </div>

                    <!-- Baris 5: Anggota Kunjungan/Rapat -->
                    <div class="lg:col-span-2">
                        <label for="anggota_editor" class="block text-[#084E8F] font-semibold mb-2">
                            Anggota Kunjungan/Rapat <span class="text-red-500">*</span>
                        </label>

                        <style>
                            .anggota-editor {
                                min-height: 110px;
                                border: 2px solid #084E8F;
                                border-radius: 8px;
                                padding: 8px 12px;
                                background: #F9FCFF;
                                outline: none;
                                overflow: auto;
                            }
                            .anggota-editor:focus {
                                box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.08);
                                background: #fff;
                            }
                            .anggota-editor ol {
                                margin: 0;
                                padding-left: 1.35rem;
                                list-style-type: decimal;
                                list-style-position: outside;
                                color: #0b2e4a;
                            }
                            .anggota-editor ol li::marker {
                                color: #084E8F;
                                font-weight: 600;
                            }
                            .anggota-placeholder {
                                color: #6b7280;
                                pointer-events: none;
                                position: absolute;
                                left: 16px;
                                top: 12px;
                                font-size: 0.95rem;
                            }
                            .anggota-wrapper { position: relative; }
                        </style>

                        <div class="anggota-wrapper">
                            <div id="anggota_editor" class="anggota-editor" contenteditable="true" aria-label="Anggota rapat editor"></div>
                            <div id="anggota_placeholder" class="anggota-placeholder">Sebutkan anggota yang hadir dalam kunjungan/rapat...</div>
                        </div>

                        <input type="hidden" name="anggota_rapat" id="anggota_rapat_input" value="{{ old('anggota_rapat') }}">
                        <x-input-error :messages="$errors->get('anggota_rapat')" class="mt-2" />
                    </div>

                    @if($errors->has('anggota_rapat'))
                        <p class="text-gray-500 text-sm -mt-2 lg:col-span-2">Wajib diisi. Sebutkan nama anggota yang hadir dalam kunjungan/rapat ini.</p>
                    @else
                        <p class="text-gray-500 text-sm -mt-2 lg:col-span-2">Wajib diisi. Sebutkan nama anggota yang hadir dalam kunjungan/rapat ini.</p>
                    @endif

                    <!-- Baris 6: Notulensi Rapat -->
                    <div class="lg:col-span-2">
                        <label for="isi_notulensi" class="block text-[#084E8F] font-semibold mb-2">
                            Notulensi Kunjungan/Rapat <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <div id="quill-editor" class="bg-white" style="min-height:220px;">{!! old('isi_notulensi') !!}</div>
                            <input type="hidden" name="isi_notulensi" id="isi_notulensi_input" value="{{ old('isi_notulensi') }}">
                        </div>
                        <x-input-error :messages="$errors->get('isi_notulensi')" class="mt-2" />
                        <p class="text-gray-500 text-sm mt-2">Gunakan editor untuk memformat notulensi (tebal, miring, daftar, tautan).</p>
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
                        <x-input-error :messages="$errors->get('dokumentasi')" class="mt-2" />
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

    <!-- Use Dokumentasi Upload Modal Component -->
    <x-dokumentasi-upload-modal :token="$token" />

    <!-- Use Karyawan List Modal Component -->
    @if($kunjungan->karyawan->count() > 1)
        <x-karyawan-list-modal :karyawanList="$kunjungan->karyawan" />
    @endif

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var initialContent = @json(old('isi_notulensi', ''));
                var quill;
                if (document.querySelector('#quill-editor')) {
                    quill = new Quill('#quill-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'header': [1, 2, 3, false] }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['link']
                            ]
                        }
                    });

                    if (initialContent) {
                        quill.root.innerHTML = initialContent;
                    }

                    // Disable image paste and drop to prevent inserting images
                    quill.root.addEventListener('paste', function (e) {
                        try {
                            if (e.clipboardData && e.clipboardData.items) {
                                for (var i = 0; i < e.clipboardData.items.length; i++) {
                                    var item = e.clipboardData.items[i];
                                    if (item && item.type && item.type.indexOf('image') !== -1) {
                                        e.preventDefault();
                                        return;
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Error handling paste event:', err);
                        }
                    });

                    quill.root.addEventListener('drop', function (e) {
                        try {
                            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                                // Prevent dropping files (images)
                                e.preventDefault();
                                return;
                            }
                        } catch (err) {
                            console.error('Error handling drop event:', err);
                        }
                    });

                    var form = document.getElementById('notulensi-form');
                    form.addEventListener('submit', function (e) {
                        var html = quill.root.innerHTML;
                        document.getElementById('isi_notulensi_input').value = html;
                    });
                }
            });
        </script>
        <script>
            // Additional form handling scripts
            const form = document.getElementById('notulensi-form');

            // Editor elements
            const anggotaEditor = document.getElementById('anggota_editor');
            const anggotaInput = document.getElementById('anggota_rapat_input');
            const anggotaPlaceholder = document.getElementById('anggota_placeholder');

            // Helper: escape HTML
            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // Render the editor from plain newline-separated value
            function renderAnggotaEditorFromText(text) {
                const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
                if (lines.length === 0) {
                    anggotaEditor.innerHTML = '';
                    anggotaPlaceholder.style.display = 'block';
                    return;
                }
                anggotaPlaceholder.style.display = 'none';
                const html = '<ol>' + lines.map(l => '<li>' + escapeHtml(l) + '</li>').join('') + '</ol>';
                anggotaEditor.innerHTML = html;
            }

            // Sync editor content into hidden input (plain lines separated by \n)
            function syncEditorToInput() {
                if (!anggotaEditor || !anggotaInput) return;
                // Collect li texts if any, otherwise use innerText split
                const liEls = anggotaEditor.querySelectorAll('li');
                let lines = [];
                if (liEls && liEls.length > 0) {
                    liEls.forEach(li => {
                        const t = li.innerText.trim();
                        if (t) lines.push(t);
                    });
                } else {
                    const raw = anggotaEditor.innerText || '';
                    lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
                }
                anggotaInput.value = lines.join('\n');
            }

            // Initialize editor with old value (if any)
                if (anggotaEditor && anggotaInput) {
                    if (anggotaInput.value && anggotaInput.value.trim() !== '') {
                        renderAnggotaEditorFromText(anggotaInput.value);
                    } else {
                        // Initialize with an empty ordered list so bullets are visible and Enter works
                        anggotaEditor.innerHTML = '<ol><li><br></li></ol>';
                    }

                    // Hide placeholder since we show bullets by default
                    anggotaPlaceholder.style.display = 'none';

                    // Ensure there is always at least one <li> so Enter creates new items naturally
                    function ensureListStructure() {
                        if (!anggotaEditor.querySelector('ol')) {
                            const raw = anggotaEditor.innerText || '';
                            const lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
                            if (lines.length > 0) {
                                const html = '<ol>' + lines.map(l => '<li>' + escapeHtml(l) + '</li>').join('') + '</ol>';
                                anggotaEditor.innerHTML = html;
                                setCaretToEnd(anggotaEditor);
                            } else {
                                anggotaEditor.innerHTML = '<ol><li><br></li></ol>';
                                setCaretToEnd(anggotaEditor);
                            }
                        }
                    }

                    function setCaretToEnd(el) {
                        const range = document.createRange();
                        const sel = window.getSelection();
                        range.selectNodeContents(el);
                        range.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }

                    anggotaEditor.addEventListener('focus', function () {
                        ensureListStructure();
                    });

                    anggotaEditor.addEventListener('input', function (e) {
                        // Keep structure intact and simply sync values; avoid full re-render to preserve Enter behavior
                        ensureListStructure();
                        syncEditorToInput();
                    });
                }

            if (form) {
                form.addEventListener('submit', function (e) {
                    // Ensure editor content is synced before submit
                    syncEditorToInput();

                    // Auto-fill jam_selesai with current time if empty
                    const jamSelesaiInput = document.querySelector('input[name="jam_selesai"]');
                    if (jamSelesaiInput && !jamSelesaiInput.value) {
                        const now = new Date();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        jamSelesaiInput.value = `${hours}:${minutes}`;
                    }

                    sessionStorage.setItem('form_submitted_{{ $token }}', 'true');
                });
            }

            window.addEventListener('load', function () {
                const wasSubmitted = sessionStorage.getItem('form_submitted_{{ $token }}');
                if (wasSubmitted) {
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