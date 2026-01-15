@extends('layouts.guest')
@section('title', 'Notulensi & Dokumentasi')
@section('header', 'Buku Tamu Digital')

@push('styles')
    <style>
        .input-wrapper {
            border: 2px solid #084E8F;
            border-radius: 8px;
            padding: 8px;
            width: 100%;
            transition: all 0.2s ease;
            background-color: white;
        }

        .input-wrapper input,
        .input-wrapper textarea,
        .input-wrapper select {
            background-color: transparent;
            width: 100%;
            border: none;
            outline: none;
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
                    Berikut adalah notulensi rapat yang telah dilaksanakan
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Baris 1: Nama Lengkap & Email -->
                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <input type="text" value="{{ $notulensi->kunjungan->tamu->nama_tamu }}" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Alamat Email</label>
                    <div class="input-wrapper">
                        <input type="text" value="{{ $notulensi->kunjungan->tamu->email_tamu }}" readonly>
                    </div>
                </div>

                <!-- Baris 2: Instansi Asal & Karyawan Tertuju -->
                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Instansi Asal</label>
                    <div class="input-wrapper">
                        <input type="text" value="{{ $notulensi->kunjungan->tamu->instansi_tamu ?? '-' }}" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Karyawan Tertuju</label>
                    @if($notulensi->kunjungan->karyawan->count() == 1)
                        <div class="input-wrapper">
                            <input type="text"
                                value="{{ $notulensi->kunjungan->karyawan->first()->nama_karyawan }} - {{ $notulensi->kunjungan->karyawan->first()->jabatan }}"
                                readonly>
                        </div>
                    @else
                        <div class="input-wrapper" style="padding: 0; overflow: hidden;">
                            <button type="button" onclick="openKaryawanModal()"
                                class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-semibold py-[16px] px-4 transition flex items-center justify-center gap-2">
                                @svg('heroicon-m-users', 'w-5 h-5')
                                Lihat Detail ({{ $notulensi->kunjungan->karyawan->count() }} Karyawan)
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Baris 3: Tujuan Kunjungan/Rapat -->
                <div class="lg:col-span-2">
                    <label class="block text-[#084E8F] font-semibold mb-2">Tujuan Kunjungan/Rapat</label>
                    <div class="input-wrapper">
                        <textarea rows="3" readonly>{{ $notulensi->kunjungan->tujuan_kunjungan }}</textarea>
                    </div>
                </div>

                <!-- Baris 4: Tanggal & Jam -->
                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Tanggal Kunjungan/Rapat</label>
                    <div class="input-wrapper">
                        <input type="text"
                            value="{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->format('l, d F Y') }}"
                            readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Jam Kunjungan/Rapat</label>
                    <div class="flex gap-2 items-center">
                        <div class="input-wrapper flex-1">
                            <input type="text" value="{{ $notulensi->kunjungan->jam_mulai }}" readonly>
                        </div>
                        <span class="text-gray-600">—</span>
                        <div class="input-wrapper flex-1">
                            <input type="text" value="{{ $notulensi->kunjungan->jam_selesai ?? '...' }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Baris 5: Anggota Kunjungan/Rapat -->
                @if($notulensi->anggota_rapat)
                    <div class="lg:col-span-2">
                        <label class="block text-[#084E8F] font-semibold mb-2">
                            Anggota Kunjungan/Rapat
                        </label>
                        <div class="input-wrapper">
                            <textarea rows="4" readonly>{{ $notulensi->anggota_rapat }}</textarea>
                        </div>
                    </div>
                @endif

                <!-- Baris 6: Notulensi Rapat -->
                <div class="lg:col-span-2">
                    <label class="block text-[#084E8F] font-semibold mb-2">
                        Notulensi Kunjungan/Rapat
                    </label>
                    <div class="input-wrapper">
                        <textarea rows="12" readonly>{{ $notulensi->isi_notulensi }}</textarea>
                    </div>
                </div>

                <!-- Baris 7: Dokumentasi Rapat -->
                @if($notulensi->kunjungan->dokumentasi && $notulensi->kunjungan->dokumentasi->count() > 0)
                    <div class="lg:col-span-2">
                        <label class="block text-[#084E8F] font-semibold mb-2">
                            Dokumentasi Kunjungan/Rapat
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($notulensi->kunjungan->dokumentasi as $doc)
                                <a href="{{ route('notulensi.dokumentasi.stream', $doc->access_token) }}" target="_blank"
                                    class="block">
                                    <img src="{{ route('notulensi.dokumentasi.stream', $doc->access_token) }}" alt="Dokumentasi"
                                        class="w-full h-32 object-cover rounded-lg border-2 border-[#084E8F] hover:opacity-90 transition duration-200 shadow hover:shadow-lg">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Info Notice -->
                <div class="lg:col-span-2">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <p class="text-yellow-800 text-sm">
                            <strong>Catatan:</strong> Notulensi ini bersifat read-only dan tidak dapat diubah. Jika ada
                            kesalahan atau perlu perubahan, silakan hubungi karyawan yang bersangkutan.
                        </p>
                    </div>
                </div>

                <!-- Button -->
                <div class="lg:col-span-2">
                    <a href="{{ url('/') }}"
                        class="block w-full text-center bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup untuk Daftar Karyawan -->
    @if($notulensi->kunjungan->karyawan->count() > 1)
        <div id="karyawan_modal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Daftar Karyawan Tertuju</h2>
                    <button type="button" class="modal-close" onclick="closeKaryawanModal()">&times;</button>
                </div>
                <div class="px-1">
                    <p class="text-gray-600 mb-4">Total {{ $notulensi->kunjungan->karyawan->count() }} karyawan yang terlibat
                        dalam kunjungan ini:</p>
                    <div class="space-y-3">
                        @foreach($notulensi->kunjungan->karyawan as $index => $karyawan)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-[#084E8F] text-white rounded-full flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $karyawan->nama_karyawan }}</p>
                                    <p class="text-sm text-gray-600">{{ $karyawan->jabatan }}</p>
                                    @if($karyawan->email_karyawan)
                                        <p class="text-sm text-gray-500 mt-1">{{ $karyawan->email_karyawan }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        <button type="button" onclick="closeKaryawanModal()"
                            class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-4 rounded-lg transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                padding: 20px;
            }

            .modal-overlay.show {
                display: flex;
            }

            .modal-content {
                background-color: white;
                border-radius: 12px;
                padding: 24px;
                max-width: 600px;
                width: 100%;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 2px solid #e5e7eb;
            }

            .modal-title {
                font-size: 1.5rem;
                font-weight: bold;
                color: #084E8F;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 2rem;
                color: #6b7280;
                cursor: pointer;
                padding: 0;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s;
            }

            .modal-close:hover {
                color: #ef4444;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const karyawanModal = document.getElementById('karyawan_modal');

            function openKaryawanModal() {
                if (karyawanModal) {
                    karyawanModal.classList.add('show');
                }
            }

            function closeKaryawanModal() {
                if (karyawanModal) {
                    karyawanModal.classList.remove('show');
                }
            }

            // Close modal on backdrop click
            if (karyawanModal) {
                karyawanModal.addEventListener('click', function (e) {
                    if (e.target === karyawanModal) closeKaryawanModal();
                });
            }
        </script>
    @endpush
@endsection