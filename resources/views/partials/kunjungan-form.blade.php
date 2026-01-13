<!-- Main Form -->
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('tamu.submit') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto"
        novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-[#084E8F] font-bold mb-2">
                        Nama Lengkap
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            placeholder="Tuliskan nama lengkap anda" required>
                    </div>
                    <div id="nama_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Nama lengkap wajib diisi
                    </div>
                </div>

                <!-- Alamat Email -->
                <div>
                    <label for="email" class="block text-[#084E8F] font-bold mb-2">
                        Alamat Email
                    </label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Tuliskan alamat email anda"
                            required>
                    </div>
                    <div id="email_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Email tidak valid
                    </div>
                </div>

                <!-- Instansi Asal -->
                <div>
                    <label for="instansi" class="block text-[#084E8F] font-bold mb-2">
                        Instansi Asal
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="instansi" name="instansi" placeholder="Tuliskan instansi asal anda"
                            required>
                    </div>
                    <div id="instansi_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Instansi asal wajib diisi
                    </div>
                </div>

                <!-- Tujuan Kedatangan -->
                <div>
                    <label for="tujuan" class="block text-[#084E8F] font-bold mb-2">
                        Tujuan Kedatangan
                    </label>
                    <div class="input-wrapper">
                        <textarea id="tujuan" name="tujuan" rows="4" placeholder="Jelaskan tujuan kedatangan anda"
                            class="resize-none" required></textarea>
                    </div>
                    <div id="tujuan_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Tujuan kedatangan wajib diisi
                    </div>
                </div>

                <!-- Karyawan yang Anda Tuju -->
                <div>
                    <label class="block text-[#084E8F] font-bold mb-2">
                        Karyawan yang Anda Tuju
                    </label>

                    <!-- Container untuk search rows -->
                    <div id="karyawan_rows_container" class="space-y-3"></div>

                    <!-- Hidden input untuk menyimpan ID karyawan yang dipilih -->
                    <input type="hidden" id="karyawan_ids" name="karyawan_ids" value="[]">

                    <!-- Error message -->
                    <div id="karyawan_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Minimal pilih 1 karyawan yang dituju
                    </div>
                </div>
            </div>

            <!-- Right Column - Webcam KTP & Submit -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Webcam Foto KTP -->
                <div>
                    <label class="block text-[#084E8F] font-bold mb-2">
                        Foto Identitas (KTP)
                    </label>

                    <!-- Webcam Area (default state) -->
                    <div id="webcam_area" class="upload-area" onclick="openWebcamModal()">
                        @svg('zondicon-camera', 'upload-icon')
                        <p class="text-[#084E8F] font-bold">Klik untuk ambil foto</p>
                    </div>

                    <!-- Preview Captured Image (shown after capture) -->
                    <div id="image_preview" class="hidden">
                        <img id="preview_img" src="" alt="Preview KTP"
                            class="w-full rounded-lg border-2 border-[#084E8F]">
                        <button type="button" onclick="openWebcamModal()"
                            class="mt-3 w-full bg-[#47B9AE] hover:bg-[#F7B218] text-white font-bold py-2 px-4 rounded-lg transition">
                            Foto Ulang
                        </button>
                    </div>

                    <input type="hidden" id="foto_ktp_base64" name="foto_ktp" value="">
                    <div id="foto_error" class="error-message">
                        @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
                        Foto KTP wajib diambil sebelum mengirim data
                    </div>

                    <!-- Error dari backend khusus upload foto -->
                    @if ($errors->has('foto_error'))
                        <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <div class="flex items-start">
                                @svg('heroicon-o-x-circle', 'w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5')
                                <div class="text-sm">
                                    <p class="font-bold text-red-800 mb-1">Gagal Upload Foto</p>
                                    <pre class="text-red-700 whitespace-pre-wrap font-sans">{{ $errors->first('foto_error') }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    @svg('phosphor-paper-plane-tilt-fill', 'w-5 h-5')
                    Kirim Data
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Popup untuk Webcam -->
<div id="webcam_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Foto Identitas (KTP)</h2>
            <button type="button" class="modal-close" onclick="closeWebcamModal()">&times;</button>
        </div>

        <!-- Video Preview -->
        <div id="video_container">
            <video id="webcam_video" autoplay playsinline class="w-full rounded-lg mb-4"
                style="background: #f3f4f6;"></video>
            <div class="flex gap-3">
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

        <!-- Canvas (hidden, untuk capture) -->
        <canvas id="capture_canvas" class="hidden"></canvas>
    </div>
</div>

<!-- Modal Popup untuk Success -->
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
