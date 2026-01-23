<div class="container">
    <div class="form-container">
        <h1 class="text-2xl font-bold text-[#084E8F] mb-6">Buat Kunjungan Baru</h1>
        
        <form action="{{ route('tamu.submit') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-layout">
                <div class="form-left">
                <x-input-wrapper 
                    id="nama_lengkap"
                    name="nama_lengkap"
                    label="Nama Lengkap"
                    type="text"
                    placeholder="Tuliskan nama lengkap anda"
                    :value="old('nama_lengkap')"
                    :error="$errors->first('nama_lengkap')"
                    errorMessage="Nama lengkap wajib diisi"
                    :required="true"
                />
                <x-input-wrapper 
                    id="email"
                    name="email"
                    label="Alamat Email"
                    type="email"
                    placeholder="Tuliskan alamat email anda"
                    :value="old('email')"
                    :error="$errors->first('email')"
                    errorMessage="Email tidak valid"
                    :required="true"
                    class="mt-4"
                />

                <x-input-wrapper 
                    id="instansi"
                    name="instansi"
                    label="Instansi Asal"
                    type="text"
                    placeholder="Tuliskan instansi asal anda"
                    :value="old('instansi')"
                    :error="$errors->first('instansi')"
                    errorMessage="Instansi asal wajib diisi"
                    :required="true"
                    class="mt-4"
                />

                <x-input-wrapper 
                    id="tujuan"
                    name="tujuan"
                    label="Tujuan Kedatangan"
                    type="textarea"
                    placeholder="Jelaskan tujuan kedatangan anda"
                    :value="old('tujuan')"
                    :error="$errors->first('tujuan')"
                    errorMessage="Tujuan kedatangan wajib diisi"
                    :required="true"
                    :rows="4"
                    class="mt-4"
                />

                <div>
                    <label class="block text-[#084E8F] font-bold mb-2 mt-4">
                        Karyawan yang Anda Tuju
                    </label>

                    <div id="karyawan_rows_container" class="space-y-3"></div>

                    <input type="hidden" id="karyawan_ids" name="karyawan_ids" value="{{ old('karyawan_ids', '[]') }}">

                    @error('karyawan_ids')
                        <div class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="karyawan_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Minimal pilih 1 karyawan yang dituju
                        </div>
                    @enderror
                </div>
                </div>

                <div class="form-right">
                <div>
                    <label class="block text-[#084E8F] font-bold mb-2">
                        Foto Identitas (KTP)
                    </label>

                    <div id="webcam_area" class="upload-area" onclick="openWebcamModal()">
                        @svg('zondicon-camera', 'upload-icon')
                        <p class="text-[#084E8F] font-bold">Klik untuk ambil foto</p>
                    </div>

                    <div id="image_preview" class="hidden">
                        <img id="preview_img" src="" alt="Preview KTP"
                            class="w-full rounded-lg border-2 border-[#084E8F]">
                        <x-button 
                            onclick="openWebcamModal()"
                            variant="primary"
                            class="mt-3 w-full"
                            icon="">
                            Foto Ulang
                        </x-button>
                    </div>

                    <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="{{ old('foto_ktp') }}">
                    
                    @error('foto_ktp')
                        <div id="foto_error" class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="foto_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Foto KTP wajib diambil sebelum mengirim data
                        </div>
                    @enderror

                    @if ($errors->has('foto_error'))
                        <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <div class="flex items-start">
                                @svg('heroicon-o-x-circle', 'w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5')
                                <div class="text-sm">
                                    <p class="font-bold text-red-800 mb-1">Gagal Upload Foto</p>
                                    <pre
                                        class="text-red-700 whitespace-pre-wrap font-sans">{{ $errors->first('foto_error') }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <x-button 
                        type="submit" 
                        variant="primary" 
                        icon="phosphor-paper-plane-tilt-fill"
                        class="w-full py-3 px-6 shadow-lg hover:shadow-xl">
                        Kirim Data
                    </x-button>
                </div>
            </div>
            </div>
        </form>
    </div>
</div>

<x-modal name="webcam_modal" id="webcam_modal" title="Foto Identitas (KTP)" :useAlpine="false">
    <div id="video_container">
        <div class="video-wrapper">
            <video id="webcam_video" autoplay playsinline class="w-full rounded-lg"
                style="background: #f3f4f6;"></video>

            <div class="ktp-overlay">
                <div class="ktp-frame">
                    <div class="ktp-guide-text">Posisikan KTP di dalam frame</div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <x-button 
                variant="cancel" 
                icon="heroicon-o-x-mark"
                onclick="closeWebcamModal()"
                class="flex-1 py-3">
                Batalkan
            </x-button>
            <x-button 
                variant="primary" 
                icon="zondicon-camera"
                onclick="capturePhoto()"
                class="flex-1 py-3">
                Ambil Foto
            </x-button>
        </div>
    </div>

    <canvas id="capture_canvas" class="hidden"></canvas>
</x-modal>

<x-modal name="success_modal" id="success_modal" title="Sukses" :useAlpine="false">
    <p class="text-gray-700" id="success_message">{{ session('success') }}</p>
    <div class="mt-6 flex justify-end">
        <x-button 
            variant="primary" 
            onclick="closeModal('success_modal')">
            Tutup
        </x-button>
    </div>
</x-modal>