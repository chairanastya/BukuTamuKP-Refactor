@extends('layouts.app')
@section('title', 'Riwayat - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <x-user-dropdown :userName="Auth::user()->nama_resepsionis" :logoutRoute="route('resepsionis.logout')" />
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">
    @vite(['resources/css/datatables-custom.css'])
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f9fafb;
        }

        .ktp-preview {
            width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .main-content {
                padding-top: 120px;
            }

            .header-container {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .header-buttons-container {
                width: 100%;
                flex-wrap: wrap;
            }

            .header-buttons-container .btn-export,
            .header-buttons-container .btn-export-pdf {
                flex: 1 1 calc(50% - 0.375rem);
                justify-content: center;
            }
        }
    </style>
@endpush

@section('sidebar')
    @include('partials.resepsionis-sidebar')
@endsection

@section('content')
    <div class="main-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6 header-container">
                <h2 class="text-2xl font-bold text-[#084E8F]">Riwayat Kunjungan</h2>
                <div class="flex items-center gap-2 header-buttons-container">
                    <x-button variant="export" onclick="exportToExcel()">
                        Export to Excel
                    </x-button>
                    <x-button variant="export-pdf" onclick="exportToPDF()">
                        Export to PDF
                    </x-button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <x-stats-card title="Total Kunjungan" :value="$allTimeStats['total']" icon="akar-people-group"
                    iconColor="text-gray-600" valueColor="text-[#084E8F]" bgColor="#E5E7EB" filter="all"
                    onclick="filterByStatus('all')" />

                <x-stats-card title="Pending" :value="$allTimeStats['pending']" icon="far-clock" iconColor="text-yellow-600"
                    valueColor="text-yellow-600" bgColor="#FEF3C7" filter="pending" onclick="filterByStatus('pending')" />

                <x-stats-card title="Done" :value="$allTimeStats['done']" icon="heroicon-o-check-circle"
                    iconColor="text-green-600" valueColor="text-green-600" bgColor="#D1FAE5" filter="done"
                    onclick="filterByStatus('done')" />

                <x-stats-card title="Canceled" :value="$allTimeStats['canceled']" icon="heroicon-o-x-circle"
                    iconColor="text-red-600" valueColor="text-red-600" bgColor="#FEE2E2" filter="canceled"
                    onclick="filterByStatus('canceled')" />
            </div>

            <!-- DataTable -->
            <div class="bg-white rounded-lg shadow p-6">
                <table id="riwayatTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nama Tamu</th>
                            <th>KTP</th>
                            <th>Instansi</th>
                            <th>PIC Karyawan</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal name="detailModal" id="detailModal" :useAlpine="false" title="Detail Kunjungan" :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </x-slot>
        <div id="detailContent"></div>
    </x-modal>

    <x-modal name="rejectModal" id="rejectModal" :useAlpine="false" title="Tolak Kunjungan" :showCloseButton="false">
        <p class="text-gray-600 mb-4">Masukkan alasan penolakan:</p>
        <textarea id="alasanBatal" class="w-full border border-gray-300 rounded-lg p-3 mb-4" rows="4"
            placeholder="Alasan pembatalan..."></textarea>
        <div class="flex gap-3 justify-end">
            <x-secondary-button onclick="closeRejectModal()">Batal</x-secondary-button>
            <x-button variant="danger" id="rejectButton" onclick="confirmReject()">
                <span id="rejectButtonText">Tolak Kunjungan</span>
                <svg id="rejectSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </x-button>
        </div>
    </x-modal>

    <!-- Accept Confirmation Modal -->
    <x-modal name="acceptModal" id="acceptModal" :useAlpine="false" :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeAcceptModal()">&times;</button>
        </x-slot>
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-green-600">Konfirmasi Terima Kunjungan</h3>
        </x-slot>
        <div class="mb-6">
            <p class="text-gray-700">Apakah Anda yakin ingin menerima kunjungan ini?</p>
            <p class="text-sm text-green-600 mt-2">Email notifikasi akan dikirim ke karyawan tujuan untuk mengisi
                notulensi.</p>
        </div>
        <div class="flex gap-3">
            <x-secondary-button onclick="closeAcceptModal()" class="flex-1">
                Batalkan
            </x-secondary-button>
            <x-button variant="success" id="acceptButton" onclick="confirmAccept()" class="flex-1">
                <span id="acceptButtonText">Terima</span>
                <svg id="acceptSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </x-button>
        </div>
    </x-modal>

    <x-modal name="ktpModal" id="ktpModal" :useAlpine="false" maxWidth="3xl" title="Foto KTP" :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeKtpModal()">&times;</button>
        </x-slot>
        <div id="ktpContent" class="flex justify-center items-center" style="min-height: 400px;">
            <!-- Content akan di-replace dengan createInlineSpinner saat viewKtp dipanggil -->
        </div>
    </x-modal>

    <!-- Karyawan List Modal -->
    <x-modal name="karyawanListModal" id="karyawanListModal" :useAlpine="false" title="Daftar Karyawan Tertuju"
        :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeKaryawanListModal()">&times;</button>
        </x-slot>
        <div id="karyawanListContent"></div>
    </x-modal>

    <!-- Success Modal -->
    <x-modal name="successModal" id="successModal" :useAlpine="false" :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeSuccessModal()">&times;</button>
        </x-slot>
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-green-600">Sukses!</h3>
        </x-slot>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700" id="successMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="success" onclick="closeSuccessModal()">
                Tutup
            </x-button>
        </div>
    </x-modal>

    <!-- Error Modal -->
    <x-modal name="errorModal" id="errorModal" :useAlpine="false" :showCloseButton="true">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="closeErrorModal()">&times;</button>
        </x-slot>
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-red-600">Terjadi Kesalahan</h3>
        </x-slot>
        <div id="errorContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-exclamation-triangle', 'w-12 h-12 text-red-500')
                <p class="text-gray-700" id="errorMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="danger" onclick="closeErrorModal()">
                Tutup
            </x-button>
        </div>
    </x-modal>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script>
        let table;
        let currentKunjunganId = null;
        let currentFilter = 'all';
        const eyeIcon = `{!! svg('heroicon-c-eye', 'w-5 h-5 inline')->toHtml() !!}`;
        const filterIcon = `{!! svg('akar-settings-horizontal', 'w-5 h-5 inline')->toHtml() !!}`;
        let activeFilters = {
            status: 'all',
            instansi: null,
            karyawan: null,
            tanggal: null
        };

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                initRiwayatTable();
            }, 100);
        });

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('show');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
            location.reload();
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.add('show');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.remove('show');
        }

        function filterByStatus(status) {
            currentFilter = status;
            activeFilters.status = status;


            document.querySelectorAll('.stats-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
            });
            document.querySelector(`[data-filter="${status}"]`).classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');


            if (status === 'all') {
                table.column(7).search('').draw();
            } else {

                table.column(7).search(status).draw();
            }
        }

        function exportToExcel() {
            // Ambil data yang sedang ditampilkan (setelah filter)
            const filteredData = table.rows({ search: 'applied' }).data().toArray();

            if (filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            // Generate tanggal untuk header dan filename
            const today = new Date();
            const dateStr = today.toISOString().split('T')[0];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const monthYear = `${monthNames[today.getMonth()]} ${today.getFullYear()}`;

            // Build filter description
            let filterParts = [];

            // Status filter
            if (activeFilters.status !== 'all') {
                filterParts.push(`Status: ${activeFilters.status.charAt(0).toUpperCase() + activeFilters.status.slice(1)}`);
            }

            // Instansi filter
            if (activeFilters.instansi) {
                filterParts.push(`Instansi: ${activeFilters.instansi}`);
            }

            // Karyawan filter
            if (activeFilters.karyawan) {
                filterParts.push(`Karyawan: ${activeFilters.karyawan}`);
            }

            // Tanggal filter
            if (activeFilters.tanggal) {
                filterParts.push(`Tanggal: ${activeFilters.tanggal}`);
            }

            const filterDescription = filterParts.length > 0
                ? filterParts.join(' | ')
                : 'Semua Data';

            const periodeText = `Periode: ${monthYear} | Filter: ${filterDescription}`;

            // Siapkan data untuk Excel
            const excelData = filteredData.map((row, index) => {
                let karyawanNama = '-';
                let karyawanJabatan = '-';
                let karyawanDepartemen = '-';

                if (row.karyawan && row.karyawan.length > 0) {
                    // Gabungkan semua karyawan
                    karyawanNama = row.karyawan.map(k => k.nama).join(', ');
                    karyawanJabatan = row.karyawan.map(k => k.jabatan).join(', ');
                    karyawanDepartemen = row.karyawan.map(k => k.departemen).join(', ');
                }

                return [
                    index + 1,
                    row.tanggal,
                    row.jam,
                    row.nama_tamu,
                    row.email_tamu,
                    row.instansi,
                    row.tujuan_kunjungan,
                    karyawanNama,
                    karyawanJabatan,
                    karyawanDepartemen,
                    row.status.toUpperCase(),
                    row.alasan_batal || '-'
                ];
            });

            // Buat workbook baru
            const wb = XLSX.utils.book_new();
            const ws = {};

            // Title dan Periode
            ws['A1'] = { v: 'LAPORAN RIWAYAT KUNJUNGAN', t: 's' };
            ws['A2'] = { v: periodeText, t: 's' };

            // Header kolom
            const headers = ['No', 'Tanggal', 'Jam', 'Nama Tamu', 'Email Tamu', 'Instansi', 'Tujuan Kunjungan', 'PIC Karyawan', 'Jabatan PIC', 'Departemen PIC', 'Status', 'Alasan Batal'];
            headers.forEach((header, idx) => {
                const cellRef = XLSX.utils.encode_cell({ r: 3, c: idx });
                ws[cellRef] = { v: header, t: 's' };
            });

            // Data rows
            excelData.forEach((row, rowIdx) => {
                row.forEach((cell, colIdx) => {
                    const cellRef = XLSX.utils.encode_cell({ r: rowIdx + 4, c: colIdx });
                    ws[cellRef] = { v: cell, t: typeof cell === 'number' ? 'n' : 's' };
                });
            });

            // Total row
            const totalRow = excelData.length + 4;
            ws[XLSX.utils.encode_cell({ r: totalRow, c: 0 })] = { v: 'TOTAL', t: 's' };
            ws[XLSX.utils.encode_cell({ r: totalRow, c: 1 })] = { v: `${excelData.length} Kunjungan`, t: 's' };

            // Set range
            const range = { s: { r: 0, c: 0 }, e: { r: totalRow, c: 11 } };
            ws['!ref'] = XLSX.utils.encode_range(range);

            // Column widths
            ws['!cols'] = [
                { wch: 5 }, { wch: 12 }, { wch: 10 }, { wch: 20 }, { wch: 25 }, { wch: 20 },
                { wch: 30 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 12 }, { wch: 30 }
            ];

            // Styling
            const headerFill = { patternType: 'solid', fgColor: { rgb: '4472C4' } };
            const headerFont = { bold: true, color: { rgb: 'FFFFFF' } };
            const totalFill = { patternType: 'solid', fgColor: { rgb: 'FFEB9C' } };
            const totalFont = { bold: true };
            const border = {
                top: { style: 'thin', color: { rgb: '000000' } },
                bottom: { style: 'thin', color: { rgb: '000000' } },
                left: { style: 'thin', color: { rgb: '000000' } },
                right: { style: 'thin', color: { rgb: '000000' } }
            };

            // Apply styles to title
            ws['A1'].s = { font: { bold: true, sz: 16 }, alignment: { horizontal: 'center', vertical: 'center' } };
            ws['A2'].s = { font: { italic: true, sz: 11 }, alignment: { horizontal: 'center' } };

            // Merge title cells
            ws['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 11 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 11 } },
                { s: { r: totalRow, c: 1 }, e: { r: totalRow, c: 11 } }
            ];

            // Apply styles to headers
            headers.forEach((_, idx) => {
                const cellRef = XLSX.utils.encode_cell({ r: 3, c: idx });
                ws[cellRef].s = {
                    fill: headerFill,
                    font: headerFont,
                    alignment: { horizontal: 'center', vertical: 'center' },
                    border: border
                };
            });

            // Apply borders to data cells
            excelData.forEach((row, rowIdx) => {
                row.forEach((_, colIdx) => {
                    const cellRef = XLSX.utils.encode_cell({ r: rowIdx + 4, c: colIdx });
                    if (ws[cellRef]) {
                        ws[cellRef].s = {
                            border: border,
                            alignment: { vertical: 'center', wrapText: colIdx >= 6 }
                        };
                    }
                });
            });

            // Apply styles to total row
            ws[XLSX.utils.encode_cell({ r: totalRow, c: 0 })].s = {
                fill: totalFill,
                font: totalFont,
                alignment: { horizontal: 'center', vertical: 'center' },
                border: border
            };
            ws[XLSX.utils.encode_cell({ r: totalRow, c: 1 })].s = {
                fill: totalFill,
                font: totalFont,
                alignment: { horizontal: 'left', vertical: 'center' },
                border: border
            };

            // Row heights
            ws['!rows'] = [
                { hpt: 24 }, // Title
                { hpt: 18 }, // Periode
                { hpt: 6 },  // Empty row
                { hpt: 30 }  // Header
            ];

            XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Kunjungan');

            // Download file dengan nama yang deskriptif
            const filenameParts = ['Laporan_Riwayat', dateStr];
            if (activeFilters.status !== 'all') filenameParts.push(activeFilters.status);
            if (activeFilters.instansi) filenameParts.push(activeFilters.instansi.replace(/\s+/g, '_'));
            if (activeFilters.karyawan) filenameParts.push(activeFilters.karyawan.replace(/\s+/g, '_'));
            const filename = filenameParts.join('_') + '.xlsx';
            XLSX.writeFile(wb, filename);
        }

        function exportToPDF() {
            // Ambil data yang sedang ditampilkan (setelah filter)
            const filteredData = table.rows({ search: 'applied' }).data().toArray();

            if (filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // landscape

            // Generate tanggal dan filter info
            const today = new Date();
            const dateStr = today.toISOString().split('T')[0];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const monthYear = `${monthNames[today.getMonth()]} ${today.getFullYear()}`;

            // Build filter description
            let filterParts = [];
            if (activeFilters.status !== 'all') {
                filterParts.push(`Status: ${activeFilters.status.charAt(0).toUpperCase() + activeFilters.status.slice(1)}`);
            }
            if (activeFilters.instansi) filterParts.push(`Instansi: ${activeFilters.instansi}`);
            if (activeFilters.karyawan) filterParts.push(`Karyawan: ${activeFilters.karyawan}`);
            if (activeFilters.tanggal) filterParts.push(`Tanggal: ${activeFilters.tanggal}`);

            const filterDescription = filterParts.length > 0 ? filterParts.join(' | ') : 'Semua Data';

            // Title
            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.text('LAPORAN RIWAYAT KUNJUNGAN', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

            // Subtitle
            doc.setFontSize(10);
            doc.setFont('helvetica', 'italic');
            doc.text(`Periode: ${monthYear} | Filter: ${filterDescription}`, doc.internal.pageSize.getWidth() / 2, 22, { align: 'center' });

            // Prepare data for table
            const tableData = filteredData.map((row, index) => {
                let karyawanInfo = '-';
                if (row.karyawan && row.karyawan.length > 0) {
                    // Gabungkan semua karyawan dengan newline
                    karyawanInfo = row.karyawan.map(k => `${k.nama}\n${k.jabatan} - ${k.departemen}`).join('\n---\n');
                }

                return [
                    index + 1,
                    row.tanggal,
                    row.jam,
                    row.nama_tamu,
                    row.instansi,
                    row.tujuan_kunjungan,
                    karyawanInfo,
                    row.status.toUpperCase()
                ];
            });

            // Generate table
            doc.autoTable({
                startY: 28,
                head: [['No', 'Tanggal', 'Jam', 'Nama Tamu', 'Instansi', 'Tujuan', 'PIC Karyawan', 'Status']],
                body: tableData,
                theme: 'grid',
                headStyles: {
                    fillColor: [68, 114, 196],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center',
                    valign: 'middle',
                    fontSize: 9,
                    cellPadding: 3
                },
                bodyStyles: {
                    fontSize: 8,
                    valign: 'middle',
                    cellPadding: 3,
                    minCellHeight: 10
                },
                columnStyles: {
                    0: { cellWidth: 10, halign: 'center' },   // No
                    1: { cellWidth: 25, halign: 'center' },   // Tanggal
                    2: { cellWidth: 20, halign: 'center' },   // Jam
                    3: { cellWidth: 40 },                      // Nama Tamu
                    4: { cellWidth: 38 },                      // Instansi
                    5: { cellWidth: 60 },                      // Tujuan
                    6: { cellWidth: 58 },                      // PIC Karyawan
                    7: { cellWidth: 26, halign: 'center' }    // Status
                },
                styles: {
                    lineColor: [0, 0, 0],
                    lineWidth: 0.1,
                    overflow: 'linebreak',
                    cellWidth: 'wrap'
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                margin: { top: 28, left: 10, right: 10, bottom: 15 },
                tableWidth: 'auto'
            });

            // Footer with total
            const finalY = doc.lastAutoTable.finalY || 28;
            const tableWidth = 277; // Total column widths
            const startX = (doc.internal.pageSize.getWidth() - tableWidth) / 2;

            doc.setFillColor(255, 235, 156);
            doc.rect(startX, finalY + 2, tableWidth, 10, 'F');
            doc.setDrawColor(0, 0, 0);
            doc.rect(startX, finalY + 2, tableWidth, 10, 'S');
            doc.setFontSize(10);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(0, 0, 0);
            doc.text(`TOTAL: ${filteredData.length} Kunjungan`, startX + 4, finalY + 8);

            // Download PDF
            const filenameParts = ['Laporan_Riwayat', dateStr];
            if (activeFilters.status !== 'all') filenameParts.push(activeFilters.status);
            if (activeFilters.instansi) filenameParts.push(activeFilters.instansi.replace(/\s+/g, '_'));
            if (activeFilters.karyawan) filenameParts.push(activeFilters.karyawan.replace(/\s+/g, '_'));
            const filename = filenameParts.join('_') + '.pdf';

            doc.save(filename);
        }

        function initRiwayatTable() {
            if ($.fn.DataTable.isDataTable('#riwayatTable')) {
                $('#riwayatTable').DataTable().destroy();
            }

            table = new DataTable('#riwayatTable', {
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                columnDefs: [
                    {
                        className: 'dtr-control',
                        orderable: false,
                        targets: 0
                    }
                ],
                ajax: {
                    url: '{{ route("resepsionis.riwayat.data") }}',
                    dataSrc: 'data',
                    error: function (xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        if (xhr.status === 0) {
                            setTimeout(function () {
                                table.ajax.reload();
                            }, 500);
                        }
                    }
                },
                columns: [
                    {
                        data: null,
                        responsivePriority: 1,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'tanggal', responsivePriority: 4 },
                    { data: 'jam', responsivePriority: 5 },
                    { data: 'nama_tamu', responsivePriority: 2 },
                    {
                        data: null,
                        responsivePriority: 3,
                        render: function (data) {
                            if (!data.has_ktp || !data.ktp_token) return '-';
                            return `<button onclick="viewKtp('${data.ktp_token}')" class="text-blue-600 hover:underline font-regular inline-flex items-center gap-1">${eyeIcon} Lihat KTP</button>`;
                        }
                    },
                    { data: 'instansi', responsivePriority: 6 },
                    {
                        data: 'karyawan',
                        responsivePriority: 7,
                        render: function (data) {
                            if (!data || data.length === 0) return '-';

                            // Jika 3 atau kurang, tampilkan semua
                            if (data.length <= 3) {
                                return data.map(k =>
                                    `${k.nama}<br><span class="text-xs text-gray-500">${k.jabatan} - ${k.departemen}</span>`
                                ).join('<br><div class="border-t border-gray-200 my-1"></div>');
                            }

                            // Jika lebih dari 3, tampilkan button
                            return `<button onclick="showKaryawanList(${JSON.stringify(data).replace(/"/g, '&quot;')})" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                                                                                                                Lihat Detail (${data.length} Karyawan)
                                                                                                            </button>`;
                        }
                    },
                    {
                        data: 'status_badge',
                        responsivePriority: 8,
                        render: function (data, type, row) {

                            if (type === 'filter' || type === 'sort') {
                                return row.status;
                            }

                            // Server-rendered badge HTML
                            return data;
                        }
                    },
                    {
                        data: null,
                        responsivePriority: 9,
                        render: function (data) {
                            if (data.status === 'done') {
                                // Gunakan class Tailwind yang sama dengan component button variant='view'
                                return '<button onclick="viewHasil(' + data.id_kunjungan + ')" id="viewHasilBtn_' + data.id_kunjungan + '" class="flex items-center gap-2 justify-center font-semibold border-none cursor-pointer transition-all duration-200 bg-[#F59E0B] hover:bg-[#D97706] text-white px-4 py-2 rounded-md text-sm">' +
                                    '<span id="viewHasilText_' + data.id_kunjungan + '">Lihat Hasil</span>' +
                                    '<svg id="viewHasilSpinner_' + data.id_kunjungan + '" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                                    '</svg>' +
                                    '</button>';
                            }
                            return '<button onclick="viewDetail(' + data.id_kunjungan + ')" class="text-blue-600 hover:underline inline-flex items-center gap-1">' + eyeIcon + ' Detail</button>';
                        }
                    },
                    {
                        data: 'id_kunjungan',
                        visible: false
                    }
                ],
                pageLength: 10,
                order: [[9, 'desc']],
                initComplete: function () {
                    console.log('DataTable initialized, calling addCustomFilters');
                    setTimeout(function () {
                        addCustomFilters();
                    }, 200);
                }
            });
        }

        let currentDateFilterStart = null;
        let currentDateFilterEnd = null;
        let currentInstansiFilter = [];
        let currentKaryawanFilter = [];

        function addCustomFilters() {
            console.log('Adding custom filters...');

            let filterWrapper = $('.dataTables_filter');
            if (filterWrapper.length === 0) {
                filterWrapper = $('.dt-search');
            }
            if (filterWrapper.length === 0) {
                filterWrapper = $('#myTable_wrapper').find('.dataTables_filter, .dt-search');
            }

            console.log('Filter wrapper found:', filterWrapper.length);

            if (filterWrapper.length === 0) {
                console.error('Filter wrapper not found!');
                return;
            }

            $('.filter-container').remove();

            const filterContainer = $('<div class="filter-container"></div>');

            const filterBtn = $(`
                    <div class="filter-btn" id="filterByBtn">
                        <span class="inline-flex items-center gap-1">${filterIcon} Filter By</span>
                        <span id="filterBadge"></span>
                        <span style="font-size: 10px;">▼</span>
                    </div>
                `);

            const mainDropdown = $(`
                <div class="filter-main-dropdown" id="mainFilterDropdown">
                    <div class="filter-category-item" data-category="tanggal">
                        <span>Tanggal</span>
                        <span style="font-size: 10px;">▶</span>
                    </div>
                    <div class="filter-category-item" data-category="instansi">
                        <span>Instansi</span>
                        <span style="font-size: 10px;">▶</span>
                    </div>
                    <div class="filter-category-item" data-category="karyawan">
                        <span>Karyawan</span>
                        <span style="font-size: 10px;">▶</span>
                    </div>
                </div>
            `);

            const dateSubDropdown = $(`
            <div class="filter-sub-dropdown" id="dateSubDropdown">
                {!! view('components.date-range-filter', [
        'startInputId' => 'dateFilterStart',
        'endInputId' => 'dateFilterEnd',
        'applyFunction' => 'applyDateFilter()',
        'clearFunction' => 'clearDateFilter()'
    ])->render() !!}
                                                                                        </div>
                                                                                    `);
            const instansiSubDropdown = $('<div class="filter-sub-dropdown" id="instansiSubDropdown"></div>');
            const karyawanSubDropdown = $('<div class="filter-sub-dropdown" id="karyawanSubDropdown"></div>');

            mainDropdown.find('[data-category="tanggal"]').append(dateSubDropdown);
            mainDropdown.find('[data-category="instansi"]').append(instansiSubDropdown);
            mainDropdown.find('[data-category="karyawan"]').append(karyawanSubDropdown);

            filterContainer.append(filterBtn, mainDropdown);

            filterWrapper.parent().append(filterContainer);

            console.log('Filter button added to layout');

            populateFilterDropdowns();

            filterBtn.on('click', function (e) {
                e.stopPropagation();
                mainDropdown.toggleClass('show');
            });

            $('.filter-category-item').on('mouseenter', function () {
                $('.filter-sub-dropdown').removeClass('show');
                const subDropdown = $(this).find('.filter-sub-dropdown');
                subDropdown.addClass('show');
            });

            mainDropdown.on('mouseleave', function () {
                $('.filter-sub-dropdown').removeClass('show');
            });

            $(document).on('click', function () {
                mainDropdown.removeClass('show');
                $('.filter-sub-dropdown').removeClass('show');
            });

            mainDropdown.on('click', function (e) {
                e.stopPropagation();
            });

            console.log('Custom filters added successfully');
        }

        function populateFilterDropdowns() {
            fetch('{{ route("resepsionis.riwayat.data") }}')
                .then(res => res.json())
                .then(result => {
                    const data = result.data;

                    const instansi = [...new Set(data.map(item => item.instansi))].sort();
                    const instansiDropdown = $('#instansiSubDropdown');
                    instansiDropdown.empty();
                    instansi.forEach(inst => {
                        const item = $(`<div class="filter-dropdown-item" data-value="${inst}">${inst}</div>`);
                        item.on('click', function (e) {
                            e.stopPropagation();
                            applyInstansiFilter(inst);
                        });
                        instansiDropdown.append(item);
                    });
                    instansiDropdown.append(`<div class="filter-clear" onclick="clearInstansiFilter()">✕ Hapus Filter</div>`);

                    const karyawanMap = new Map();
                    data.forEach(item => {
                        if (item.karyawan && item.karyawan.length > 0) {
                            item.karyawan.forEach(k => {
                                const key = `${k.nama}|${k.departemen}|${k.jabatan}`;
                                if (!karyawanMap.has(key)) {
                                    karyawanMap.set(key, {
                                        nama: k.nama,
                                        departemen: k.departemen,
                                        jabatan: k.jabatan
                                    });
                                }
                            });
                        }
                    });
                    const karyawan = [...karyawanMap.values()].sort((a, b) => a.nama.localeCompare(b.nama));
                    const karyawanDropdown = $('#karyawanSubDropdown');
                    karyawanDropdown.empty();
                    karyawan.forEach(kary => {
                        const uniqueKey = `${kary.nama}|${kary.departemen}|${kary.jabatan}`;
                        const item = $(`
                                                                                                    <div class="karyawan-item" data-value="${uniqueKey}">
                                                                                                        <div class="karyawan-name">${kary.nama}</div>
                                                                                                        <div class="karyawan-detail">${kary.departemen} • ${kary.jabatan}</div>
                                                                                                    </div>
                                                                                                `);
                        item.on('click', function (e) {
                            e.stopPropagation();
                            applyKaryawanFilter(uniqueKey, kary.nama, kary.departemen, kary.jabatan);
                        });
                        karyawanDropdown.append(item);
                    });
                    karyawanDropdown.append(`<div class="filter-clear" onclick="clearKaryawanFilter()">✕ Hapus Filter</div>`);
                });
        }

        function updateFilterBadge() {
            let count = 0;
            if (currentDateFilterStart || currentDateFilterEnd) count++;
            count += currentInstansiFilter.length;
            count += currentKaryawanFilter.length;

            const badge = $('#filterBadge');
            if (count > 0) {
                badge.html(`<span class="active-filter-badge">${count}</span>`);
                $('#filterByBtn').addClass('active');
            } else {
                badge.html('');
                $('#filterByBtn').removeClass('active');
            }
        }

        function applyDateFilter() {
            const startDate = $('#dateFilterStart').val();
            const endDate = $('#dateFilterEnd').val();

            if (!startDate && !endDate) {
                alert('Silakan pilih minimal satu tanggal (dari atau sampai)');
                return;
            }

            if (startDate && endDate && startDate > endDate) {
                alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            currentDateFilterStart = startDate || null;
            currentDateFilterEnd = endDate || null;

            updateFilterBadge();
            applyAllFilters();
        }

        function clearDateFilter() {
            currentDateFilterStart = null;
            currentDateFilterEnd = null;
            $('#dateFilterStart').val('');
            $('#dateFilterEnd').val('');
            updateFilterBadge();
            applyAllFilters();
        }

        function applyInstansiFilter(instansi) {
            const index = currentInstansiFilter.indexOf(instansi);
            const item = $(`#instansiSubDropdown .filter-dropdown-item[data-value="${instansi}"]`);

            if (index > -1) {
                currentInstansiFilter.splice(index, 1);
                item.removeClass('active');
            } else {
                currentInstansiFilter.push(instansi);
                item.addClass('active');
            }

            updateFilterBadge();
            applyAllFilters();
        }

        function clearInstansiFilter() {
            currentInstansiFilter = [];
            $('#instansiSubDropdown .filter-dropdown-item').removeClass('active');
            updateFilterBadge();
            applyAllFilters();
        }

        function applyKaryawanFilter(uniqueKey, nama, departemen, jabatan) {
            const index = currentKaryawanFilter.indexOf(uniqueKey);
            const item = $(`#karyawanSubDropdown .karyawan-item[data-value="${uniqueKey}"]`);

            if (index > -1) {
                currentKaryawanFilter.splice(index, 1);
                item.removeClass('active');
            } else {
                currentKaryawanFilter.push(uniqueKey);
                item.addClass('active');
            }

            updateFilterBadge();
            applyAllFilters();
        }

        function clearKaryawanFilter() {
            currentKaryawanFilter = [];
            $('#karyawanSubDropdown .karyawan-item').removeClass('active');
            updateFilterBadge();
            applyAllFilters();
        }

        function applyAllFilters() {
            if ($.fn.dataTable.ext.search.length > 0) {
                $.fn.dataTable.ext.search.pop();
            }

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    const tanggalStr = data[1];
                    const instansi = data[5];
                    const rowData = table.row(dataIndex).data();
                    const karyawanArray = rowData.karyawan;

                    if (currentDateFilterStart || currentDateFilterEnd) {
                        const parts = tanggalStr.split('/');
                        if (parts.length === 3) {
                            const tanggalFormatted = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;

                            if (currentDateFilterStart && tanggalFormatted < currentDateFilterStart) {
                                return false;
                            }
                            if (currentDateFilterEnd && tanggalFormatted > currentDateFilterEnd) {
                                return false;
                            }
                        }
                    }

                    if (currentInstansiFilter.length > 0 && !currentInstansiFilter.includes(instansi)) {
                        return false;
                    }

                    if (currentKaryawanFilter.length > 0 && karyawanArray && karyawanArray.length > 0) {
                        const hasMatch = currentKaryawanFilter.some(filterKey => {
                            const [filterNama, filterDepartemen, filterJabatan] = filterKey.split('|');
                            return karyawanArray.some(k =>
                                k.nama === filterNama &&
                                k.departemen === filterDepartemen &&
                                k.jabatan === filterJabatan
                            );
                        });
                        if (!hasMatch) {
                            return false;
                        }
                    }

                    return true;
                }
            );

            table.draw();
        }

        function viewDetail(id) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');

            if (!modal || !content) {
                console.error('Modal detail tidak ditemukan');
                return;
            }

            content.innerHTML = createInlineSpinner('Memuat Detail Kunjungan...');
            modal.classList.add('show');

            fetch(`{{ route('resepsionis.riwayat.data') }}`)
                .then(res => res.json())
                .then(result => {
                    const kunjungan = result.data.find(k => k.id_kunjungan === id);
                    if (!kunjungan) {
                        content.innerHTML = '<div class="text-red-600"><p class="font-semibold mb-2">Detail tidak ditemukan</p><p class="text-sm">Kunjungan tidak ditemukan dalam sistem</p></div>';
                        return;
                    }

                    let karyawanList = kunjungan.karyawan.map(k =>
                        `<li>${k.nama} - ${k.jabatan} (${k.departemen})</li>`
                    ).join('');

                    let actions = '';
                    if (kunjungan.status === 'pending') {
                        actions = `
                                                                                                                <div class="flex gap-3 mt-6">
                                                                                                                    <button onclick="acceptKunjungan(${id})" class="flex-1 flex items-center gap-2 justify-center font-semibold border-none cursor-pointer transition-all duration-200 bg-[#10B981] hover:bg-[#059669] text-white px-4 py-2 rounded-md text-sm">Terima</button>
                                                                                                                    <button onclick="openRejectModal(${id})" class="flex-1 flex items-center gap-2 justify-center font-semibold border-none cursor-pointer transition-all duration-200 bg-[#EF4444] hover:bg-[#DC2626] text-white px-4 py-2 rounded-md text-sm">Tolak</button>
                                                                                                                </div>
                                                                                                            `;
                    }

                    let cancelReason = '';
                    if (kunjungan.status === 'canceled' && kunjungan.alasan_batal) {
                        cancelReason = `
                                                                                                                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                                                                                    <p class="font-semibold text-red-800">Alasan Pembatalan:</p>
                                                                                                                    <p class="text-red-700">${kunjungan.alasan_batal}</p>
                                                                                                                </div>
                                                                                                            `;
                    }

                    // Gunakan status_badge dari server (sudah di-generate oleh BadgeHelper)
                    const statusBadge = kunjungan.status_badge || kunjungan.status;

                    document.getElementById('detailContent').innerHTML = `
                                                                                                            <div class="space-y-3">
                                                                                                                <div><strong>Tanggal:</strong> ${kunjungan.tanggal}</div>
                                                                                                                <div><strong>Jam:</strong> ${kunjungan.jam}</div>
                                                                                                                <div><strong>Nama Tamu:</strong> ${kunjungan.nama_tamu}</div>
                                                                                                                <div><strong>Email:</strong> ${kunjungan.email_tamu}</div>
                                                                                                                <div><strong>Instansi:</strong> ${kunjungan.instansi}</div>
                                                                                                                <div><strong>Tujuan Kunjungan:</strong> ${kunjungan.tujuan_kunjungan}</div>
                                                                                                                <div><strong>Karyawan Tujuan:</strong><ul class="list-disc ml-6">${karyawanList}</ul></div>
                                                                                                                <div><strong>Status:</strong> ${statusBadge}</div>
                                                                                                                ${cancelReason}
                                                                                                                ${actions}
                                                                                                            </div>
                                                                                                        `;
                })
                .catch(error => {
                    console.error('Error fetching detail:', error);
                    content.innerHTML = '<div class="text-red-600"><p class="font-semibold mb-2">Gagal memuat detail</p><p class="text-sm">Terjadi kesalahan saat memuat data</p></div>';
                });
        }

        function acceptKunjungan(id) {
            currentKunjunganId = id;
            closeModal();
            document.getElementById('acceptModal').classList.add('show');
        }

        function closeAcceptModal() {
            document.getElementById('acceptModal').classList.remove('show');
        }

        function confirmAccept() {
            const button = document.getElementById('acceptButton');
            const buttonText = document.getElementById('acceptButtonText');
            const spinner = document.getElementById('acceptSpinner');

            // Disable button and show spinner
            button.disabled = true;
            buttonText.textContent = 'Memproses...';
            spinner.classList.remove('hidden');

            fetch(`/resepsionis/kunjungan/${currentKunjunganId}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        closeAcceptModal();
                        showSuccessModal('Kunjungan berhasil diterima. Email telah dikirim ke karyawan tujuan untuk mengisi notulensi.');
                    }
                })
                .catch(error => {
                    // Re-enable button on error
                    button.disabled = false;
                    buttonText.textContent = 'Terima';
                    spinner.classList.add('hidden');
                    closeAcceptModal();
                    showErrorModal('Terjadi kesalahan saat menerima kunjungan');
                });
        }

        function openRejectModal(id) {
            currentKunjunganId = id;
            closeModal();
            document.getElementById('rejectModal').classList.add('show');
        }

        function confirmReject() {
            const alasan = document.getElementById('alasanBatal').value.trim();
            if (!alasan) {
                showErrorModal('Alasan pembatalan harus diisi');
                return;
            }

            const button = document.getElementById('rejectButton');
            const buttonText = document.getElementById('rejectButtonText');
            const spinner = document.getElementById('rejectSpinner');

            // Disable button and show spinner
            button.disabled = true;
            buttonText.textContent = 'Memproses...';
            spinner.classList.remove('hidden');

            fetch(`/resepsionis/kunjungan/${currentKunjunganId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ alasan_batal: alasan })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        closeRejectModal();
                        showSuccessModal('Kunjungan berhasil ditolak.');
                    }
                })
                .catch(error => {
                    // Re-enable button on error
                    button.disabled = false;
                    buttonText.textContent = 'Tolak Kunjungan';
                    spinner.classList.add('hidden');
                    closeRejectModal();
                    showErrorModal('Terjadi kesalahan saat menolak kunjungan');
                });
        }

        let isLoadingNotulensi = false;

        function viewHasil(kunjunganId) {
            // Prevent rapid clicks
            if (isLoadingNotulensi) {
                return;
            }

            isLoadingNotulensi = true;

            // Get button elements
            const button = document.getElementById('viewHasilBtn_' + kunjunganId);
            const buttonText = document.getElementById('viewHasilText_' + kunjunganId);
            const spinner = document.getElementById('viewHasilSpinner_' + kunjunganId);

            // Show spinner
            if (button && buttonText && spinner) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-not-allowed');
                buttonText.textContent = 'Memuat...';
                spinner.classList.remove('hidden');
            }

            // Fetch token notulensi berdasarkan kunjungan ID
            fetch(`/resepsionis/notulensi/${kunjunganId}/token`)
                .then(response => response.json())
                .then(data => {
                    isLoadingNotulensi = false;

                    if (data.success && data.token) {
                        // Buka halaman notulensi view dengan token yang didapat
                        window.open(`/notulensi/view/${data.token}`, '_blank');
                    } else {
                        showErrorModal(data.message || 'Notulensi tidak ditemukan');
                    }
                })
                .catch(error => {
                    console.error('Error fetching notulensi token:', error);
                    isLoadingNotulensi = false;
                    showErrorModal('Terjadi kesalahan saat mengambil data notulensi');
                })
                .finally(() => {
                    // Hide spinner
                    if (button && buttonText && spinner) {
                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-not-allowed');
                        buttonText.textContent = 'Lihat Hasil';
                        spinner.classList.add('hidden');
                    }
                });
        }

        function showLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }

        function hideLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        function closeModal() {
            document.getElementById('detailModal').classList.remove('show');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('show');
            document.getElementById('alasanBatal').value = '';
        }

        function viewKtp(ktpToken) {
            const modal = document.getElementById('ktpModal');
            const content = document.getElementById('ktpContent');

            content.innerHTML = createInlineSpinner('Memuat KTP...');
            modal.classList.add('show');

            const streamUrl = `/resepsionis/ktp/${ktpToken}/stream`;
            const img = new Image();

            img.onload = function () {
                content.innerHTML = `<img src="${streamUrl}" alt="KTP" class="ktp-preview rounded-lg shadow-lg">`;
            };

            img.onerror = function () {
                content.innerHTML = '<div class="text-red-600"><p class="font-semibold mb-2">Gagal memuat KTP</p><p class="text-sm">Terjadi kesalahan saat memuat gambar</p></div>';
            };

            img.src = streamUrl;
        }

        function closeKtpModal() {
            document.getElementById('ktpModal').classList.remove('show');
        }

        function showKaryawanList(karyawanData) {
            const modal = document.getElementById('karyawanListModal');
            const content = document.getElementById('karyawanListContent');

            let html = `<p class="text-gray-600 mb-4">Total ${karyawanData.length} karyawan yang terlibat:</p>`;
            html += '<div class="space-y-3">';

            karyawanData.forEach((karyawan, index) => {
                html += `
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-shrink-0 w-8 h-8 bg-[#084E8F] text-white rounded-full flex items-center justify-center font-bold">
                                ${index + 1}
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">${karyawan.nama}</p>
                                <p class="text-sm text-gray-600">${karyawan.jabatan}</p>
                                <p class="text-sm text-gray-500">${karyawan.departemen}</p>
                            </div>
                        </div>
                    `;
            });

            html += '</div>';
            html += '<div class="mt-6"><button onclick="closeKaryawanListModal()" class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-4 rounded-lg transition">Tutup</button></div>';

            content.innerHTML = html;
            modal.classList.add('show');
        }

        function closeKaryawanListModal() {
            const modal = document.getElementById('karyawanListModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('button[onclick="toggleDropdown()"]')) {
                document.getElementById('dropdown').classList.add('hidden');
            }
        });


        let navigationTimeout = null;
        document.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', function (e) {
                if (this.href && !this.classList.contains('active')) {

                    if (navigationTimeout) {
                        clearTimeout(navigationTimeout);
                    }

                    navigationTimeout = setTimeout(() => {
                        showLoading();
                    }, 50);
                }
            });
        });

        document.getElementById('ktpModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeKtpModal();
            }
        });

        document.getElementById('detailModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('rejectModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });

        // Supabase Realtime - Auto reload hanya ketika ada perubahan
        (function () {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2';
            script.onload = function () {
                fetch('/api/supabase-config')
                    .then(res => res.json())
                    .then(config => {
                        const { createClient } = supabase;
                        const supabaseClient = createClient(config.url, config.key);

                        const channel = supabaseClient
                            .channel('riwayat-realtime')
                            .on('postgres_changes',
                                { event: '*', schema: 'public', table: 'kunjungan' },
                                (payload) => {
                                    console.log('Perubahan terdeteksi:', payload.eventType);
                                    table.ajax.reload(null, false);
                                }
                            )
                            .subscribe((status) => {
                                if (status === 'SUBSCRIBED') {
                                    console.log('Realtime active - akan auto-reload saat ada perubahan');
                                }
                            });

                        window.addEventListener('beforeunload', () => channel.unsubscribe());
                    });
            };
            document.head.appendChild(script);
        })();
    </script>
@endpush