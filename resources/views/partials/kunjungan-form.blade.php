<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#084E8F] mb-6">Buat Kunjungan Baru</h1>
    <form action="{{ route('tamu.submit') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto"
        novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label for="nama_lengkap" class="block text-[#084E8F] font-bold mb-2">
                        Nama Lengkap
                    </label>
                    <div class="input-wrapper {{ $errors->has('nama_lengkap') ? 'border-red-500 bg-red-50' : '' }}">
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            placeholder="Tuliskan nama lengkap anda" 
                            value="{{ old('nama_lengkap') }}"
                            required>
                    </div>
                    @error('nama_lengkap')
                        <div class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="nama_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Nama lengkap wajib diisi
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-[#084E8F] font-bold mb-2">
                        Alamat Email
                    </label>
                    <div class="input-wrapper {{ $errors->has('email') ? 'border-red-500 bg-red-50' : '' }}">
                        <input type="email" id="email" name="email" 
                            placeholder="Tuliskan alamat email anda" 
                            value="{{ old('email') }}"
                            required>
                    </div>
                    @error('email')
                        <div class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="email_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Email tidak valid
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="instansi" class="block text-[#084E8F] font-bold mb-2">
                        Instansi Asal
                    </label>
                    <div class="input-wrapper {{ $errors->has('instansi') ? 'border-red-500 bg-red-50' : '' }}">
                        <input type="text" id="instansi" name="instansi" 
                            placeholder="Tuliskan instansi asal anda"
                            value="{{ old('instansi') }}"
                            required>
                    </div>
                    @error('instansi')
                        <div class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="instansi_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Instansi asal wajib diisi
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="tujuan" class="block text-[#084E8F] font-bold mb-2">
                        Tujuan Kedatangan
                    </label>
                    <div class="input-wrapper {{ $errors->has('tujuan') ? 'border-red-500 bg-red-50' : '' }}">
                        <textarea id="tujuan" name="tujuan" rows="4" 
                            placeholder="Jelaskan tujuan kedatangan anda"
                            class="resize-none" 
                            required>{{ old('tujuan') }}</textarea>
                    </div>
                    @error('tujuan')
                        <div class="error-message show">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            {{ $message }}
                        </div>
                    @else
                        <div id="tujuan_error" class="error-message">
                            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                            Tujuan kedatangan wajib diisi
                        </div>
                    @enderror
                </div>

                <div>
                    <label class="block text-[#084E8F] font-bold mb-2">
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

            <div class="lg:col-span-1 space-y-6">
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
                        <button type="button" onclick="openWebcamModal()"
                            class="mt-3 w-full bg-[#47B9AE] hover:bg-[#F7B218] text-white font-bold py-2 px-4 rounded-lg transition">
                            Foto Ulang
                        </button>
                    </div>

                    <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="{{ old('foto_ktp') }}">
                    
                    @error('foto_ktp')
                        <div class="error-message show">
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

                <button type="submit"
                    class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    @svg('phosphor-paper-plane-tilt-fill', 'w-5 h-5')
                    Kirim Data
                </button>
            </div>
        </div>
    </form>
</div>

<div id="webcam_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Foto Identitas (KTP)</h2>
            <button type="button" class="modal-close" onclick="closeWebcamModal()">&times;</button>
        </div>

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
                <button type="button" onclick="closeWebcamModal()" style="background-color: #D9D9D9; color: #000000;"
                    class="flex-1 font-semibold py-3 px-4 rounded-lg transition hover:opacity-90 flex items-center justify-center gap-2">
                    @svg('heroicon-o-x-mark', 'w-5 h-5')
                    Batalkan
                </button>
                <button type="button" onclick="capturePhoto()" style="background-color: #084E8F; color: white;"
                    class="flex-1 font-semibold py-3 px-4 rounded-lg transition hover:bg-[#F7B218] flex items-center justify-center gap-2">
                    @svg('zondicon-camera', 'w-5 h-5')
                    Ambil Foto
                </button>
            </div>
        </div>

        <canvas id="capture_canvas" class="hidden"></canvas>
    </div>
</div>

<div id="success_modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Sukses</h2>
            <button type="button" class="modal-close" onclick="closeSuccessModal()">&times;</button>
        </div>
        <div class="px-1">
            <p class="text-gray-700" id="success_message">{{ session('success') }}</p>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeSuccessModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Tutup</button>
            </div>
        </div>
    </div>
</div>