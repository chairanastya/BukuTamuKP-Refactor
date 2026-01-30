@extends('layouts.guest')
@section('title', 'Notulensi & Dokumentasi')
@section('header', 'Buku Tamu Digital')

@section('content')
    <div class="container mx-auto px-4 py-8 mt-24">
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
                <x-input-wrapper label="Nama Lengkap" type="text" value="{{ $notulensi->kunjungan->tamu->nama_tamu }}"
                    readonly />

                <x-input-wrapper label="Alamat Email" type="text" value="{{ $notulensi->kunjungan->tamu->email_tamu }}"
                    readonly />

                <!-- Baris 2: Instansi Asal & Karyawan Tertuju -->
                <x-input-wrapper label="Instansi Asal" type="text"
                    value="{{ $notulensi->kunjungan->tamu->instansi_tamu ?? '-' }}" readonly />

                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Karyawan Tertuju</label>
                    @if($notulensi->kunjungan->karyawan->count() == 1)
                        <div class="input-wrapper readonly">
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
                <x-input-wrapper label="Tujuan Kunjungan/Rapat" type="textarea"
                    value="{{ $notulensi->kunjungan->tujuan_kunjungan }}" rows="3" readonly class="lg:col-span-2" />

                <!-- Baris 4: Tanggal & Jam -->
                <x-input-wrapper label="Tanggal Kunjungan/Rapat" type="text"
                    value="{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->format('l, d F Y') }}"
                    readonly />

                <div>
                    <label class="block text-[#084E8F] font-semibold mb-2">Jam Kunjungan/Rapat</label>
                    <div class="flex gap-2 items-center">
                        <div class="input-wrapper readonly flex-1">
                            <input type="text" value="{{ $notulensi->kunjungan->jam_mulai }}" readonly>
                        </div>
                        <span class="text-gray-600">—</span>
                        <div class="input-wrapper readonly flex-1">
                            <input type="text" value="{{ $notulensi->kunjungan->jam_selesai ?? '...' }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Baris 5: Anggota Kunjungan/Rapat -->
                @if($notulensi->anggota_rapat)
                    <div class="lg:col-span-2">
                        <label class="block text-[#084E8F] font-semibold mb-2">Anggota Kunjungan/Rapat</label>
                        <div class="input-wrapper">
                            @php
                                $lines = preg_split('/\r\n|\r|\n/', $notulensi->anggota_rapat);
                            @endphp
                            <ol class="view-anggota-list" style="margin:0; padding-left:1.35rem; list-style-type:decimal;">
                                @foreach($lines as $line)
                                    @if(trim($line) !== '')
                                        <li style="margin-bottom:6px; color:#0b2e4a">{{ trim($line) }}</li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @endif

                <!-- Baris 6: Notulensi Rapat -->
                <div class="lg:col-span-2">
                    <label class="block text-[#084E8F] font-semibold mb-2">
                        Notulensi Kunjungan/Rapat
                    </label>
                    <x-notulensi-viewer :content="$notulensi->isi_notulensi" />
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

                <!-- Buttons -->
                <div class="lg:col-span-2">
                    <x-button variant="export-pdf" type="button" id="exportBtn" icon="heroicon-o-document-text"
                        iconClass="w-5 h-5" class="w-full py-3">
                        <span id="exportBtnText">Export to PDF</span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Use Karyawan List Modal Component -->
    @if($notulensi->kunjungan->karyawan->count() > 1)
        <x-karyawan-list-modal :karyawanList="$notulensi->kunjungan->karyawan" />
    @endif


    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const exportBtn = document.getElementById('exportBtn');
                if (exportBtn) {
                    exportBtn.addEventListener('click', function () {
                        const notulensiData = {
                            namaTamu: '{{ $notulensi->kunjungan->tamu->nama_tamu }}',
                            emailTamu: '{{ $notulensi->kunjungan->tamu->email_tamu }}',
                            instansiTamu: '{{ $notulensi->kunjungan->tamu->instansi_tamu ?? "-" }}',
                            tujuanKunjungan: `{{ $notulensi->kunjungan->tujuan_kunjungan }}`,
                            tanggalKunjungan: '{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->format("l, d F Y") }}',
                            hariTanggal: '{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->locale("id")->isoFormat("dddd, D MMMM YYYY") }}',
                            jamMulai: '{{ $notulensi->kunjungan->jam_mulai }}',
                            jamSelesai: '{{ $notulensi->kunjungan->jam_selesai ?? "..." }}',
                            anggotaRapat: `{{ $notulensi->anggota_rapat ?? "" }}`,
                            isiNotulensi: `{{ $notulensi->isi_notulensi }}`
                        };

                        const dokumentasiList = [
                            @if($notulensi->kunjungan->dokumentasi && $notulensi->kunjungan->dokumentasi->count() > 0)
                                @foreach($notulensi->kunjungan->dokumentasi as $doc)
                                    '{{ route('notulensi.dokumentasi.stream', $doc->access_token) }}',
                                @endforeach
                            @endif
                                        ];

                        const karyawanList = [
                            @foreach($notulensi->kunjungan->karyawan as $k)
                                                        {
                                    nama: '{{ $k->nama_karyawan }}',
                                    jabatan: '{{ $k->jabatan }}',
                                    email: '{{ $k->email_karyawan ?? "" }}'
                                },
                            @endforeach
                                        ];

                        window.exportNotulensiPDF({
                            buttonId: 'exportBtn',
                            buttonTextId: 'exportBtnText',
                            notulensiData: notulensiData,
                            dokumentasiList: dokumentasiList,
                            karyawanList: karyawanList
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection