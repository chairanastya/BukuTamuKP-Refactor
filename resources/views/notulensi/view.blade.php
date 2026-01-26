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
                    <div class="input-wrapper notulensi-content">
                        {!! $notulensi->isi_notulensi !!}
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

                <!-- Buttons -->
                <div class="lg:col-span-2 flex gap-4">
                    <button onclick="exportToPDF()" id="exportBtn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        @svg('heroicon-o-document-text', 'w-5 h-5')
                        <span id="exportBtnText">Export to PDF</span>
                    </button>
                    <a href="{{ route('resepsionis.dashboard') }}"
                        class="flex-1 text-center bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
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

    @push('styles')
        <style>
            /* Make rendered notulensi look similar to Quill editor */
            .notulensi-content {
                background: white;
                padding: 12px;
                min-height: 220px;
                border-radius: 6px;
                font-size: 16px; /* increased font size for notulensi text only */
                line-height: 1.6; /* improve readability */
            }

            /* Headings coming from Quill editor (h1-h6) */
            .notulensi-content h1,
            .notulensi-content h2,
            .notulensi-content h3,
            .notulensi-content h4,
            .notulensi-content h5,
            .notulensi-content h6 {
                margin: 0 0 0.75rem;
                line-height: 1.25;
                font-weight: 600;
            }

            .notulensi-content h1 { font-size: 1.5rem; }
            .notulensi-content h2 { font-size: 1.25rem; }
            .notulensi-content h3 { font-size: 1.1rem; }
            .notulensi-content h4 { font-size: 1rem; }
            .notulensi-content h5 { font-size: 0.95rem; }
            .notulensi-content h6 { font-size: 0.9rem; }

            .notulensi-content p {
                margin: 0 0 0.75rem;
            }

            .notulensi-content ul,
            .notulensi-content ol {
                margin: 0 0 0.75rem 1.25rem;
                padding-left: 1.25rem;
                list-style-position: outside;
            }

            .notulensi-content ul {
                list-style-type: disc;
            }

            .notulensi-content ol {
                list-style-type: decimal;
            }

            .notulensi-content strong,
            .notulensi-content b {
                font-weight: 600;
            }

            /* Link styling for rendered notulensi: blue + underline, visited -> pink/purple */
            .notulensi-content a.notulensi-link,
            .notulensi-content a {
                color: #1a73e8;
                text-decoration: underline;
                text-decoration-color: #1a73e8;
                text-underline-offset: 2px;
                transition: color 0.15s ease;
            }

            .notulensi-content a:hover {
                color: #0b5ed7;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

            async function exportToPDF() {
                // Disable button and show loading
                const exportBtn = document.getElementById('exportBtn');
                const exportBtnText = document.getElementById('exportBtnText');
                const originalText = exportBtnText.textContent;

                exportBtn.disabled = true;
                exportBtnText.textContent = 'Membuat PDF...';

                try {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('p', 'mm', 'a4');

                    // Data dari blade
                    const namaTamu = '{{ $notulensi->kunjungan->tamu->nama_tamu }}';
                    const emailTamu = '{{ $notulensi->kunjungan->tamu->email_tamu }}';
                    const instansiTamu = '{{ $notulensi->kunjungan->tamu->instansi_tamu ?? "-" }}';
                    const tujuanKunjungan = `{{ $notulensi->kunjungan->tujuan_kunjungan }}`;
                    const tanggalKunjungan = '{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->format("l, d F Y") }}';
                    const hariTanggal = '{{ \Carbon\Carbon::parse($notulensi->kunjungan->tanggal_kunjungan)->locale("id")->isoFormat("dddd, D MMMM YYYY") }}';
                    const jamMulai = '{{ $notulensi->kunjungan->jam_mulai }}';
                    const jamSelesai = '{{ $notulensi->kunjungan->jam_selesai ?? "..." }}';
                    const anggotaRapat = `{{ $notulensi->anggota_rapat ?? "" }}`;
                    const isiNotulensi = `{{ $notulensi->isi_notulensi }}`;

                    // Dokumentasi data
                    const dokumentasiList = [
                        @if($notulensi->kunjungan->dokumentasi && $notulensi->kunjungan->dokumentasi->count() > 0)
                            @foreach($notulensi->kunjungan->dokumentasi as $doc)
                                '{{ route('notulensi.dokumentasi.stream', $doc->access_token) }}',
                            @endforeach
                        @endif
                            ];

                    // Karyawan data
                    const karyawanList = [
                        @foreach($notulensi->kunjungan->karyawan as $k)
                                        {
                                nama: '{{ $k->nama_karyawan }}',
                                jabatan: '{{ $k->jabatan }}',
                                email: '{{ $k->email_karyawan ?? "" }}'
                            },
                        @endforeach
                            ];

                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();
                    const margin = 15; // Reduced margin for smaller file size
                    const contentWidth = pageWidth - (margin * 2);
                    let yPos = margin;

                    // ========== HEADER - FORMAL STYLE ==========
                    doc.setFontSize(14); // Smaller title for mobile-like appearance
                    doc.setTextColor(0, 0, 0);
                    doc.setFont(undefined, 'bold');
                    doc.text('NOTULENSI', pageWidth / 2, yPos, { align: 'center' });

                    // Underline
                    const titleWidth = doc.getTextWidth('NOTULENSI');
                    doc.setLineWidth(0.5);
                    doc.line(pageWidth / 2 - titleWidth / 2, yPos + 2, pageWidth / 2 + titleWidth / 2, yPos + 2);

                    yPos += 15;

                    // ========== INFO DASAR (Hari/Tanggal, Waktu, Tempat) ==========
                    doc.setFontSize(10); 
                    doc.setFont(undefined, 'normal');

                    const labelWidth = 30;

                    // Hari/Tanggal
                    doc.setFont(undefined, 'bold');
                    doc.text('Hari/Tanggal', margin, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(': ' + hariTanggal, margin + labelWidth, yPos);
                    yPos += 7;

                    // Waktu
                    doc.setFont(undefined, 'bold');
                    doc.text('Waktu', margin, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(`: Pukul ${jamMulai} - ${jamSelesai} WIB`, margin + labelWidth, yPos);
                    yPos += 7;

                    // Tempat/Instansi
                    doc.setFont(undefined, 'bold');
                    doc.text('Tempat', margin, yPos);
                    doc.setFont(undefined, 'normal');
                    const tempatText = instansiTamu !== '-' ? instansiTamu : 'Kantor Perusahaan';
                    doc.text(': ' + tempatText, margin + labelWidth, yPos);
                    yPos += 10;

                    // ========== PESERTA ==========
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'bold');
                    doc.text('• PESERTA', margin, yPos);
                    yPos += 6;

                    doc.setFont(undefined, 'normal');
                    doc.setFontSize(9);

                    // Tamu
                    doc.text(`Tamu:`, margin + 3, yPos);
                    yPos += 6;
                    doc.text(`- ${namaTamu}`, margin + 8, yPos);
                    if (instansiTamu !== '-') {
                        yPos += 5;
                        doc.text(`  (${instansiTamu})`, margin + 10, yPos);
                    }
                    yPos += 7;

                    // Karyawan Tertuju
                    doc.text(`Karyawan Tertuju:`, margin + 3, yPos);
                    yPos += 6;

                    karyawanList.forEach((karyawan, index) => {
                        if (yPos > pageHeight - 30) {
                            doc.addPage();
                            yPos = margin;
                        }
                        doc.text(`- ${karyawan.nama} (${karyawan.jabatan})`, margin + 8, yPos);
                        yPos += 5;
                    });

                    // Anggota Rapat
                    if (anggotaRapat) {
                        yPos += 2;
                        doc.text(`Anggota Lain yang Hadir:`, margin + 3, yPos);
                        yPos += 6;

                        const anggotaLines = doc.splitTextToSize(anggotaRapat, contentWidth - 15);
                        anggotaLines.forEach(line => {
                            if (yPos > pageHeight - 30) {
                                doc.addPage();
                                yPos = margin;
                            }
                            doc.text('- ' + line, margin + 8, yPos);
                            yPos += 5;
                        });
                    }

                    yPos += 5;

                    // ========== TOPIK ==========
                    if (yPos > pageHeight - 40) {
                        doc.addPage();
                        yPos = margin;
                    }

                    doc.setFontSize(10);
                    doc.setFont(undefined, 'bold');
                    doc.text('• TOPIK', margin, yPos);
                    yPos += 6;

                    doc.setFontSize(9);
                    doc.setFont(undefined, 'normal');
                    const topikLines = doc.splitTextToSize(tujuanKunjungan, contentWidth - 5);
                    topikLines.forEach(line => {
                        if (yPos > pageHeight - 30) {
                            doc.addPage();
                            yPos = margin;
                        }
                        doc.text(line, margin + 3, yPos);
                        yPos += 5;
                    });
                    yPos += 5;

                    // ========== AGENDA / PEMBAHASAN ==========
                    if (yPos > pageHeight - 40) {
                        doc.addPage();
                        yPos = margin;
                    }

                    doc.setFontSize(10);
                    doc.setFont(undefined, 'bold');
                    doc.text('• AGENDA / PEMBAHASAN', margin, yPos);
                    yPos += 6;

                    doc.setFontSize(9);
                    doc.setFont(undefined, 'normal');

                    // Render notulensi HTML as text with WYSIWYG styling
                    try {
                        const notulensiEl = document.querySelector('.notulensi-content');
                        if (notulensiEl) {
                            // Function to parse and render HTML content recursively with computed styles
                            function renderElement(element, currentY, indent = 0, linkUrl = null) {
                                let yPos = currentY;
                                const tagName = element.tagName ? element.tagName.toLowerCase() : 'text';
                                const textContent = element.textContent || element.nodeValue || '';

                                // Handle text nodes
                                if (element.nodeType === Node.TEXT_NODE && textContent.trim()) {
                                    const computedStyle = window.getComputedStyle(element.parentElement || element);
                                    const fontWeight = computedStyle.fontWeight;
                                    const fontStyle = computedStyle.fontStyle;
                                    const textDecoration = computedStyle.textDecorationLine || '';

                                    // Set font style based on computed style
                                    let pdfFontStyle = 'normal';
                                    if (fontWeight >= 600 || fontWeight === 'bold') {
                                        pdfFontStyle = 'bold';
                                    }
                                    if (fontStyle === 'italic') {
                                        pdfFontStyle = pdfFontStyle === 'bold' ? 'bolditalic' : 'italic';
                                    }

                                    doc.setFont(undefined, pdfFontStyle);

                                    // Set text color from computed style if available (but links override it)
                                    if (!linkUrl) {
                                        const color = computedStyle.color;
                                        if (color) {
                                            const rgb = color.match(/\d+/g);
                                            if (rgb && rgb.length >= 3) {
                                                doc.setTextColor(parseInt(rgb[0]), parseInt(rgb[1]), parseInt(rgb[2]));
                                            }
                                        }
                                    }

                                    const textLines = doc.splitTextToSize(textContent.trim(), contentWidth - 5 - indent);
                                    textLines.forEach((line, lineIndex) => {
                                        if (yPos > pageHeight - 25) {
                                            doc.addPage();
                                            yPos = margin;
                                        }

                                        const x = margin + 3 + indent;

                                        if (linkUrl) {
                                            // Render linked text with blue color
                                            doc.setTextColor(26, 115, 232); // light blue for links
                                            doc.text(line, x, yPos);

                                            // Add underline for links
                                            const textWidth = doc.getTextWidth(line);
                                            doc.setLineWidth(0.2);
                                            doc.setDrawColor(26, 115, 232); // Same blue color for underline
                                            doc.line(x, yPos + 1, x + textWidth, yPos + 1);

                                            doc.setTextColor(0, 0, 0);

                                            // Add clickable link area for this line
                                            const textHeight = 4;
                                            doc.link(x, yPos - textHeight, textWidth, textHeight + 2, { url: linkUrl });
                                        } else {
                                            doc.text(line, x, yPos);

                                            // Add underline if needed
                                            if (textDecoration.includes('underline')) {
                                                const textWidth = doc.getTextWidth(line);
                                                doc.setLineWidth(0.2); // Thinner line for underline
                                                doc.setDrawColor(0, 0, 0);
                                                doc.line(x, yPos + 1, x + textWidth, yPos + 1);
                                            }

                                            // Add strikethrough if needed
                                            if (textDecoration.includes('line-through')) {
                                                const textWidth = doc.getTextWidth(line);
                                                const textHeight = doc.getTextDimensions(line).h;
                                                doc.setLineWidth(0.2); // Thinner line for strikethrough
                                                doc.setDrawColor(0, 0, 0);
                                                doc.line(x, yPos - textHeight / 4, x + textWidth, yPos - textHeight / 4);
                                            }
                                        }

                                        yPos += 4;
                                    });

                                    // Reset text color
                                    doc.setTextColor(0, 0, 0);
                                    return yPos;
                                }

                                // Handle element nodes
                                if (element.nodeType === Node.ELEMENT_NODE) {
                                    switch (tagName) {
                                        case 'h1':
                                            doc.setFontSize(14);
                                            doc.setFont(undefined, 'bold');
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            yPos += 4;
                                            doc.setFontSize(9); // Reset font size
                                            return yPos;

                                        case 'h2':
                                            doc.setFontSize(12);
                                            doc.setFont(undefined, 'bold');
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            yPos += 3;
                                            doc.setFontSize(9);
                                            return yPos;

                                        case 'h3':
                                            doc.setFontSize(11);
                                            doc.setFont(undefined, 'bold');
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            yPos += 3;
                                            doc.setFontSize(9);
                                            return yPos;

                                        case 'h4':
                                        case 'h5':
                                        case 'h6':
                                            doc.setFontSize(10);
                                            doc.setFont(undefined, 'bold');
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            yPos += 2;
                                            doc.setFontSize(9);
                                            return yPos;

                                        case 'p':
                                            doc.setFontSize(9);
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            yPos += 2;
                                            return yPos;

                                        case 'a':
                                            const href = element.getAttribute('href');
                                            if (href) {
                                                // Render children but indicate they are links so text is rendered with link annotations
                                                yPos = renderChildren(element, yPos, indent, href);
                                            } else {
                                                yPos = renderChildren(element, yPos, indent, linkUrl);
                                            }
                                            return yPos;

                                        case 'ul':
                                            yPos = renderList(element, yPos, indent, 'bullet', linkUrl);
                                            return yPos;

                                        case 'ol':
                                            yPos = renderList(element, yPos, indent, 'numbered', linkUrl);
                                            return yPos;

                                        case 'li':
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            return yPos;

                                        case 'blockquote':
                                            doc.setFontSize(9);
                                            doc.setTextColor(100, 100, 100);
                                            yPos = renderChildren(element, yPos, indent + 5, linkUrl);
                                            doc.setTextColor(0, 0, 0);
                                            yPos += 2;
                                            return yPos;

                                        case 'br':
                                            return yPos + 4;

                                        default:
                                            yPos = renderChildren(element, yPos, indent, linkUrl);
                                            return yPos;
                                    }
                                }

                                return yPos;
                            }
                            function renderChildren(element, currentY, indent = 0, linkUrl = null) {
                                let yPos = currentY;
                                const children = element.childNodes;

                                for (let i = 0; i < children.length; i++) {
                                    yPos = renderElement(children[i], yPos, indent, linkUrl);
                                }

                                return yPos;
                            }

                            function renderList(listElement, currentY, indent, type, linkUrl = null) {
                                let yPos = currentY;
                                const items = listElement.children;

                                for (let i = 0; i < items.length; i++) {
                                    const item = items[i];
                                    if (item.tagName && item.tagName.toLowerCase() === 'li') {
                                        if (yPos > pageHeight - 25) {
                                            doc.addPage();
                                            yPos = margin;
                                        }

                                        const bullet = type === 'bullet' ? '•' : `${i + 1}.`;
                                        doc.setFontSize(9);
                                        doc.setFont(undefined, 'normal');

                                        // Render bullet/number
                                        doc.text(bullet, margin + 3 + indent, yPos);

                                        // Render list item content
                                        yPos = renderElement(item, yPos, indent + 7, linkUrl);
                                    }
                                }

                                return yPos + 2;
                            }

                            yPos = renderElement(notulensiEl, yPos, 0, null);
                        }
                    } catch (err) {
                        console.error('Error rendering notulensi HTML:', err);
                        // Fallback: render plain text
                        const notulensiParagraphs = isiNotulensi.split('\n').filter(p => p.trim() !== '');

                        notulensiParagraphs.forEach((paragraph, index) => {
                            if (yPos > pageHeight - 30) {
                                doc.addPage();
                                yPos = margin;
                            }

                            const paragraphLines = doc.splitTextToSize(paragraph.trim(), contentWidth - 5);
                            paragraphLines.forEach(line => {
                                if (yPos > pageHeight - 25) {
                                    doc.addPage();
                                    yPos = margin;
                                }
                                doc.text(line, margin + 3, yPos);
                                yPos += 5;
                            });

                            if (index < notulensiParagraphs.length - 1) {
                                yPos += 3;
                            }
                        });

                        yPos += 8;
                    }

                    // ========== DOKUMENTASI ==========
                    if (dokumentasiList.length > 0) {
                        if (yPos > pageHeight - 50) {
                            doc.addPage();
                            yPos = margin;
                        }

                        doc.setFontSize(10);
                        doc.setFont(undefined, 'bold');
                        doc.text('• DOKUMENTASI', margin, yPos);
                        yPos += 8;

                        exportBtnText.textContent = `Memuat Gambar (0/${dokumentasiList.length})...`;

                        // Display images in a 2-column grid
                        const imagesPerRow = 2;
                        const imageSpacing = 5;
                        const availableWidth = contentWidth - (imageSpacing * (imagesPerRow - 1));
                        const imageWidth = availableWidth / imagesPerRow;
                        const imageHeight = 60; // Fixed height for grid layout

                        let currentImageIndex = 0;

                        for (let row = 0; currentImageIndex < dokumentasiList.length; row++) {
                            if (yPos + imageHeight + 15 > pageHeight - 20) {
                                doc.addPage();
                                yPos = margin;
                            }

                            // Process images for this row
                            for (let col = 0; col < imagesPerRow && currentImageIndex < dokumentasiList.length; col++) {
                                exportBtnText.textContent = `Memuat Gambar (${currentImageIndex + 1}/${dokumentasiList.length})...`;

                                try {
                                    const imgUrl = dokumentasiList[currentImageIndex];

                                    const img = await Promise.race([
                                        new Promise((resolve, reject) => {
                                            const image = new Image();
                                            image.crossOrigin = 'Anonymous';
                                            image.onload = () => resolve(image);
                                            image.onerror = reject;
                                            image.src = imgUrl;
                                        }),
                                        new Promise((_, reject) =>
                                            setTimeout(() => reject(new Error('Timeout')), 10000)
                                        )
                                    ]);

                                    // Calculate image position in grid
                                    const xPos = margin + (col * (imageWidth + imageSpacing));
                                    const yPosImage = yPos;

                                    // Calculate image size to fit in grid cell while maintaining aspect ratio
                                    let imgDisplayWidth = imageWidth;
                                    let imgDisplayHeight = (img.height / img.width) * imageWidth;

                                    // If image is taller than grid cell, scale it down
                                    if (imgDisplayHeight > imageHeight) {
                                        const scaleRatio = imageHeight / imgDisplayHeight;
                                        imgDisplayWidth *= scaleRatio;
                                        imgDisplayHeight = imageHeight;
                                    }

                                    // Center image horizontally in its grid cell
                                    const centeredX = xPos + (imageWidth - imgDisplayWidth) / 2;

                                    // Add image
                                    doc.addImage(img, 'JPEG', centeredX, yPosImage, imgDisplayWidth, imgDisplayHeight, undefined, 'FAST');

                                    // Add small label below image
                                    doc.setFontSize(7);
                                    doc.setFont(undefined, 'italic');
                                    doc.setTextColor(100, 100, 100);
                                    doc.text(`Gambar ${currentImageIndex + 1}`, centeredX + imgDisplayWidth / 2, yPosImage + imgDisplayHeight + 3, { align: 'center' });
                                    doc.setTextColor(0, 0, 0);

                                } catch (error) {
                                    console.error('Error loading image:', error);
                                    // Draw placeholder rectangle
                                    doc.setDrawColor(200, 200, 200);
                                    doc.setLineWidth(0.5);
                                    const xPos = margin + (col * (imageWidth + imageSpacing));
                                    doc.rect(xPos, yPos, imageWidth, imageHeight);

                                    // Add error text
                                    doc.setFontSize(7);
                                    doc.setFont(undefined, 'italic');
                                    doc.setTextColor(150, 150, 150);
                                    doc.text(`Gambar ${currentImageIndex + 1}: Gagal dimuat`, xPos + imageWidth / 2, yPos + imageHeight / 2, { align: 'center' });
                                    doc.setTextColor(0, 0, 0);
                                }

                                currentImageIndex++;
                            }

                            // Move to next row
                            yPos += imageHeight + 15;
                        }
                    }

                    // ========== FOOTER ==========
                    const totalPages = doc.internal.pages.length - 1;
                    const today = new Date();
                    const footerDate = today.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    for (let i = 1; i <= totalPages; i++) {
                        doc.setPage(i);

                        // Footer line
                        doc.setDrawColor(180, 180, 180);
                        doc.setLineWidth(0.3);
                        doc.line(margin, pageHeight - 20, pageWidth - margin, pageHeight - 20);

                        // Footer text
                        doc.setFontSize(7); // Smaller footer font
                        doc.setTextColor(100, 100, 100);
                        doc.setFont(undefined, 'italic');

                        doc.text(
                            `Halaman ${i} dari ${totalPages}`,
                            margin,
                            pageHeight - 15
                        );

                        doc.text(
                            `Dicetak pada: ${footerDate}`,
                            pageWidth / 2,
                            pageHeight - 15,
                            { align: 'center' }
                        );

                        doc.text(
                            'Buku Tamu Digital',
                            pageWidth - margin,
                            pageHeight - 15,
                            { align: 'right' }
                        );
                    }

                    // Save PDF
                    exportBtnText.textContent = 'Menyimpan PDF...';
                    const fileName = `Notulensi_${namaTamu.replace(/\s+/g, '_')}_${tanggalKunjungan.replace(/\s+/g, '_')}.pdf`;
                    doc.save(fileName);

                    setTimeout(() => {
                        exportBtn.disabled = false;
                        exportBtnText.textContent = originalText;
                    }, 1000);

                } catch (error) {
                    console.error('Error generating PDF:', error);
                    alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
                    exportBtn.disabled = false;
                    exportBtnText.textContent = originalText;
                }
            }
        </script>
        <script>
            // Normalize and style links inside rendered Quill HTML
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.querySelector('.notulensi-content');
                if (!container) return;

                container.querySelectorAll('a').forEach(function (a) {
                    try {
                        let rawHref = a.getAttribute('href') || '';

                        // If href looks like plain domain without scheme, prepend https://
                        if (rawHref && !/^[a-zA-Z][a-zA-Z0-9+.-]*:/.test(rawHref) && !rawHref.startsWith('#')) {
                            a.setAttribute('href', 'https://' + rawHref);
                        }

                        // Open in new tab and safe settings
                        a.setAttribute('target', '_blank');
                        a.setAttribute('rel', 'noopener noreferrer');

                        // Shorten visible text: remove protocol and trailing slash
                        const visible = (a.textContent || a.getAttribute('href') || '').toString();
                        const cleaned = visible.replace(/^https?:\/\//i, '').replace(/^www\./i, '').replace(/\/$/, '');
                        a.textContent = cleaned;

                        // Ensure styling class applied
                        a.classList.add('notulensi-link');
                    } catch (err) {
                        console.error('Error normalizing link in notulensi view:', err);
                    }
                });
            });
        </script>
    @endpush
@endsection