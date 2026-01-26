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
                                    value="{{ old('jam_selesai', $kunjungan->jam_selesai) }}" required>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                    </div>

                    <!-- Baris 5: Anggota Kunjungan/Rapat -->
                    <x-input-wrapper 
                        id="anggota_rapat"
                        name="anggota_rapat"
                        type="textarea"
                        rows="4"
                        required
                        placeholder="Sebutkan anggota yang hadir dalam kunjungan/rapat..."
                        value="{{ old('anggota_rapat') }}"
                        :error="$errors->first('anggota_rapat')"
                        class="lg:col-span-2">
                        <x-slot:label>
                            Anggota Kunjungan/Rapat <span class="text-red-500">*</span>
                        </x-slot:label>
                    </x-input-wrapper>

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
            if (form) {
                form.addEventListener('submit', function () {
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