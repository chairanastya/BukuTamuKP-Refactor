@extends('layouts.app')
@section('title', 'Dashboard - Buku Tamu Digital')

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

        .btn-success {
            background: #10B981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background: #EF4444;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-danger:hover {
            background-color: #DC2626;
        }

        .btn-view {
            background: #F59E0B;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }

        .btn-view:hover {
            background-color: #D97706;
        }

        .ktp-preview {
            width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .filter-container {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }

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

            .header-buttons-container .btn-primary {
                width: 100%;
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
                <h2 class="text-2xl font-bold text-[#084E8F]">Kunjungan Hari Ini</h2>
                <div class="flex items-center gap-2 header-buttons-container">
                    <x-button variant="export" onclick="exportToExcel()">
                        Export to Excel
                    </x-button>
                    <x-button variant="export-pdf" onclick="exportToPDF()">
                        Export to PDF
                    </x-button>
                    <x-button variant="primary" href="{{ route('resepsionis.kunjungan.create') }}">
                        Buat Kunjungan Baru
                    </x-button>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <x-stats-card title="Total Kunjungan" :value="$stats['total']" valueColor="text-[#084E8F]"
                    icon="akar-people-group" iconColor="text-gray-600" bgColor="#E5E7EB" filter="all"
                    onclick="filterByStatus('all')" />

                <x-stats-card title="Pending" :value="$stats['pending']" valueColor="text-yellow-600" icon="far-clock"
                    iconColor="text-yellow-600" bgColor="#FEF3C7" filter="pending" onclick="filterByStatus('pending')" />

                <x-stats-card title="Done" :value="$stats['done']" valueColor="text-green-600"
                    icon="heroicon-o-check-circle" iconColor="text-green-600" bgColor="#D1FAE5" filter="done"
                    onclick="filterByStatus('done')" />

                <x-stats-card title="Canceled" :value="$stats['canceled']" valueColor="text-red-600"
                    icon="heroicon-o-x-circle" iconColor="text-red-600" bgColor="#FEE2E2" filter="canceled"
                    onclick="filterByStatus('canceled')" />

            </div>

            <!-- DataTable -->
            <div class="bg-white rounded-lg shadow p-6">
                <table id="myTable" class="display" style="width:100%">
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

    <!-- Modals -->
    <x-modal name="detailModal" id="detailModal" title="Detail Kunjungan" :useAlpine="false">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeModal('detailModal')">&times;</button>
        </x-slot>
        <div id="detailContent"></div>
    </x-modal>

    <!-- Reject Confirmation Modal -->
    <x-modal name="rejectModal" id="rejectModal" :showHeader="false" :useAlpine="false">
        <h3 class="text-xl font-bold mb-4">Tolak Kunjungan</h3>
        <p class="text-gray-600 mb-4">Masukkan alasan penolakan:</p>
        <textarea id="alasanBatal" class="w-full border border-gray-300 rounded-lg p-3 mb-4" rows="4"
            placeholder="Alasan pembatalan..."></textarea>
        <div class="flex gap-3 justify-end">
            <button onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
            <x-button id="rejectButton" variant="danger" onclick="confirmReject()" :loading="true" loadingId="reject">
                Tolak Kunjungan
            </x-button>
        </div>
    </x-modal>

    <!-- Accept Confirmation Modal -->
    <x-modal name="acceptModal" id="acceptModal" :useAlpine="false">
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-green-600">Konfirmasi Terima Kunjungan</h3>
        </x-slot>
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeModal('acceptModal')">&times;</button>
        </x-slot>
        <div class="mb-6">
            <p class="text-gray-700">Apakah Anda yakin ingin menerima kunjungan ini?</p>
            <p class="text-sm text-green-600 mt-2">Email notifikasi akan dikirim ke karyawan tujuan untuk mengisi
                notulensi.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeAcceptModal()"
                class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg transition">
                Batalkan
            </button>
            <x-button id="acceptButton" variant="success" onclick="confirmAccept()" :loading="true" loadingId="accept"
                class="flex-1 py-3">
                Terima
            </x-button>
        </div>
    </x-modal>

    <!-- KTP Preview Modal -->
    <x-modal name="ktpModal" id="ktpModal" title="Foto KTP" maxWidth="large" :useAlpine="false">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeModal('ktpModal')">&times;</button>
        </x-slot>
        <div id="ktpContent" class="flex justify-center items-center" style="min-height: 400px;"></div>
    </x-modal>

    <!-- Karyawan List Modal -->
    <x-modal name="karyawanListModal" id="karyawanListModal" title="Daftar Karyawan Tertuju" :useAlpine="false">
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeModal('karyawanListModal')">&times;</button>
        </x-slot>
        <div id="karyawanListContent"></div>
    </x-modal>

    <!-- Success Modal -->
    <x-modal name="successModal" id="successModal" :useAlpine="false">
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-green-600">Sukses!</h3>
        </x-slot>
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeSuccessModal()">&times;</button>
        </x-slot>
        <div id="successContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                <p class="text-gray-700" id="successMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="success" onclick="closeSuccessModal()" class="py-3 px-6">
                Tutup
            </x-button>
        </div>
    </x-modal>

    <!-- Error Modal -->
    <x-modal name="errorModal" id="errorModal" :useAlpine="false">
        <x-slot name="header">
            <h3 class="text-2xl font-bold text-red-600">Terjadi Kesalahan</h3>
        </x-slot>
        <x-slot name="closeButton">
            <button type="button" class="modal-close" onclick="window.closeErrorModal()">&times;</button>
        </x-slot>
        <div id="errorContent" class="mb-6">
            <div class="flex items-center gap-3">
                @svg('heroicon-o-exclamation-triangle', 'w-12 h-12 text-red-500')
                <p class="text-gray-700" id="errorMessage"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button variant="danger" onclick="closeErrorModal()" class="py-3 px-6">
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
        let currentDetailKunjunganId = null;
        let currentFilter = 'all';
        let currentInstansiFilter = [];
        let currentKaryawanFilter = [];
        const eyeIcon = `{!! svg('heroicon-c-eye', 'w-5 h-5 inline')->toHtml() !!}`;
        const filterIcon = `{!! svg('akar-settings-horizontal', 'w-5 h-5 inline')->toHtml() !!}`;
        let activeFilters = {
            status: 'all',
            instansi: null,
            karyawan: null,
            tanggal: null
        };
        // Expose to window for component access
        window.activeFilters = activeFilters;

        document.addEventListener('DOMContentLoaded', function () {
            initModals();

            const filterByStatus = createStatusFilter({
                currentFilterVar: 'currentFilter',
                activeFiltersVar: 'activeFilters',
                tableVar: 'table',
                columnIndex: 7,
                useRegex: false
            });
            window.filterByStatus = filterByStatus;

            // Set initial filter selection
            document.querySelector('[data-filter="all"]').classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');

            setTimeout(function () {
                initDataTable();
            }, 100);
        });

        window.closeSuccessModal = function () {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.classList.remove('show');
            }
        };

        // Excel export using ExcelExporter component
        function exportToExcel() {
            const exporter = new ExcelExporter({
                table: table,
                activeFilters: activeFilters,
                title: 'LAPORAN KUNJUNGAN HARI INI',
                sheetName: 'Kunjungan Hari Ini',
                filePrefix: 'Laporan_Kunjungan',
                columns: [
                    { header: 'No', width: 5 },
                    { header: 'Tanggal', width: 12 },
                    { header: 'Jam', width: 10 },
                    { header: 'Nama Tamu', width: 20 },
                    { header: 'Email Tamu', width: 25 },
                    { header: 'Instansi', width: 20 },
                    { header: 'Tujuan Kunjungan', width: 30 },
                    { header: 'PIC Karyawan', width: 20 },
                    { header: 'Jabatan PIC', width: 20 },
                    { header: 'Departemen PIC', width: 20 },
                    { header: 'Status', width: 12 },
                    { header: 'Alasan Batal', width: 30 }
                ],
                dataMapper: (row, index) => {
                    let karyawanNama = '-';
                    let karyawanJabatan = '-';
                    let karyawanDepartemen = '-';

                    if (row.karyawan && row.karyawan.length > 0) {
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
                }
            });
            exporter.export();
        }

        function exportToPDF() {
            // Ambil data yang sedang ditampilkan (setelah filter)
            if (!table) {
                alert('Tabel belum dimuat, silakan tunggu...');
                return;
            }

            const filteredData = table.rows({ search: 'applied' }).data().toArray();

            if (filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            console.log('Filtered data count:', filteredData.length);
            console.log('First row sample:', filteredData[0]);

            // Generate tanggal dan filter info
            const today = new Date();
            const dateStr = today.toISOString().split('T')[0];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const monthYear = `${monthNames[today.getMonth()]} ${today.getFullYear()}`;

            // Build filter description
            let filterParts = [];
            if (currentFilter !== 'all') {
                filterParts.push(`Status: ${currentFilter.charAt(0).toUpperCase() + currentFilter.slice(1)}`);
            }
            if (currentInstansiFilter && currentInstansiFilter.length > 0) {
                filterParts.push(`Instansi: ${currentInstansiFilter.join(', ')}`);
            }
            if (currentKaryawanFilter && currentKaryawanFilter.length > 0) {
                filterParts.push(`Karyawan: ${currentKaryawanFilter.length} terpilih`);
            }

            const filterDescription = filterParts.length > 0 ? filterParts.join(' | ') : 'Semua Data';
            const subtitle = `Periode: ${monthYear} | Filter: ${filterDescription}`;

            // Transform data for PDF export - IMPORTANT: must be array of arrays matching columns order
            const pdfData = filteredData.map((row, index) => {
                const karyawanList = row.karyawan && row.karyawan.length > 0 
                    ? row.karyawan.map(k => `${k.nama}\n${k.jabatan} - ${k.departemen}`).join('\n---\n')
                    : '-';

                return [
                    index + 1,                          // No
                    row.tanggal || '-',                 // Tanggal
                    row.jam || '-',                     // Jam
                    row.nama_tamu || '-',               // Nama Tamu
                    row.instansi || '-',                // Instansi
                    row.tujuan_kunjungan || '-',        // Tujuan
                    karyawanList,                       // PIC Karyawan
                    (row.status || '').toUpperCase()    // Status
                ];
            });

            console.log('PDF data count:', pdfData.length);
            console.log('First PDF row:', pdfData[0]);

            // Generate filename
            const filenameParts = ['Laporan_Kunjungan', dateStr];
            if (currentFilter !== 'all') filenameParts.push(currentFilter);
            if (currentInstansiFilter && currentInstansiFilter.length > 0) filenameParts.push(currentInstansiFilter[0].replace(/\s+/g, '_'));
            const filename = filenameParts.join('_');

            // Use PDF export component dengan error handling
            try {
                if (typeof exportDataTablePDF !== 'function') {
                    console.error('exportDataTablePDF tidak ditemukan. window.exportDataTablePDF:', window.exportDataTablePDF);
                    alert('Error: PDF export component tidak tersedia. Silakan refresh halaman.');
                    return;
                }

                if (!window.jspdf) {
                    console.error('jsPDF tidak ditemukan. window.jspdf:', window.jspdf);
                    alert('Error: jsPDF library tidak tersedia. Silakan refresh halaman.');
                    return;
                }

                console.log('Memanggil exportDataTablePDF dengan data:', {
                    title: 'LAPORAN KUNJUNGAN HARI INI',
                    subtitle: subtitle,
                    dataCount: pdfData.length,
                    filename: filename,
                    columnCount: 8
                });

                exportDataTablePDF({
                    title: 'LAPORAN KUNJUNGAN HARI INI',
                    subtitle: subtitle,
                    filename: filename,
                    columns: [['No', 'Tanggal', 'Jam', 'Nama Tamu', 'Instansi', 'Tujuan', 'PIC Karyawan', 'Status']],
                    data: pdfData,
                    footerText: 'Kunjungan'
                });
            } catch (error) {
                console.error('PDF Export Error:', error);
                console.error('Stack:', error.stack);
                alert('Terjadi kesalahan saat membuat PDF: ' + error.message);
            }
        }

        // Initialize DataTable using DataTableManager component
        function initDataTable() {
            const dtManager = new DataTableManager({
                tableId: 'myTable',
                ajaxUrl: '{{ route("resepsionis.kunjungan.data") }}',
                columns: [
                    {
                        data: null,
                        responsivePriority: 1,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'tanggal', visible: false },
                    { data: 'jam', responsivePriority: 4 },
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

                            if (data.length <= 3) {
                                return data.map(k =>
                                    `${k.nama}<br><span class="text-xs text-gray-500">${k.jabatan} - ${k.departemen}</span>`
                                ).join('<br><div class="border-t border-gray-200 my-1"></div>');
                            }

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
                            return data;
                        }
                    },
                    {
                        data: null,
                        responsivePriority: 9,
                        render: function (data) {
                            if (data.status === 'done') {
                                return '<button onclick="viewHasil(' + data.id_kunjungan + ')" id="viewHasilBtn_' + data.id_kunjungan + '" class="btn-view flex items-center justify-center gap-2">' +
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
                onInitComplete: function () {
                    console.log('DataTable initialized, calling addCustomFilters');
                    setTimeout(function () {
                        addCustomFilters();
                    }, 200);
                }
            });

            table = dtManager.init();
            // Expose to window for component access
            window.table = table;
        }

        // Initialize custom filters using initDatatableFilter component
        function addCustomFilters() {
            console.log('Adding custom filters...');

            // Initialize Instansi filter
            const instansiFilter = initDatatableFilter({
                filterId: 'instansiFilter',
                filterName: 'Instansi',
                tableInstance: table,
                columnIndex: 5, // Instansi column
                multiple: true,
                dataFetcher: async () => {
                    try {
                        const response = await fetch('{{ route("resepsionis.kunjungan.data") }}');
                        const result = await response.json();
                        const data = result.data || [];
                        return [...new Set(data.map(item => item.instansi))].sort();
                    } catch (error) {
                        console.error('Error fetching instansi data:', error);
                        return [];
                    }
                }
            });

            // Initialize Karyawan filter
            const karyawanFilter = initDatatableFilter({
                filterId: 'karyawanFilter',
                filterName: 'Karyawan',
                tableInstance: table,
                columnIndex: 6, // Karyawan column
                multiple: true,
                dataFetcher: async () => {
                    try {
                        const response = await fetch('{{ route("resepsionis.kunjungan.data") }}');
                        const result = await response.json();
                        const data = result.data || [];

                        const karyawanMap = new Map();
                        data.forEach(item => {
                            if (item.karyawan && item.karyawan.length > 0) {
                                item.karyawan.forEach(k => {
                                    const key = `${k.nama}|${k.departemen}|${k.jabatan}`;
                                    if (!karyawanMap.has(key)) {
                                        karyawanMap.set(key, k.nama);
                                    }
                                });
                            }
                        });

                        return [...karyawanMap.values()].sort();
                    } catch (error) {
                        console.error('Error fetching karyawan data:', error);
                        return [];
                    }
                }
            });

            console.log('Custom filters initialized successfully');
        }

        function viewDetail(id) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');

            if (!modal || !content) {
                console.error('Modal detail tidak ditemukan');
                return;
            }

            currentDetailKunjunganId = id;
            window.currentDetailId = id;

            content.innerHTML = createInlineSpinner('Memuat Detail Kunjungan...');
            modal.classList.add('show');

            fetch(`{{ route('resepsionis.kunjungan.data') }}`)
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
                                                            <button onclick="acceptKunjungan(${id})" class="btn-success flex-1">Terima</button>
                                                            <button onclick="openRejectModal(${id})" class="btn-danger flex-1">Tolak</button>
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
            const buttonText = document.getElementById('accept_text');
            const spinner = document.getElementById('accept_spinner');

            button.disabled = true;
            buttonText.classList.add('hidden');
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
                        // Close modals immediately
                        closeAcceptModal();

                        // Reload table first
                        if (table) {
                            table.ajax.reload(null, false);
                        }

                        // Wait a bit for table to reload, then refresh modal detail
                        setTimeout(() => {
                            if (currentDetailKunjunganId) {
                                viewDetail(currentDetailKunjunganId);
                            }
                        }, 500);

                        showSuccessModal('Kunjungan berhasil diterima. Email telah dikirim ke karyawan tujuan untuk mengisi notulensi.');
                    }
                })
                .catch(error => {
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    closeAcceptModal();
                    showErrorModal('Terjadi kesalahan saat menerima kunjungan');
                });
        }

        function openRejectModal(id) {
            currentKunjunganId = id;
            window.closeModal('detailModal');
            window.showModal('rejectModal');
        }

        function confirmReject() {
            const alasan = document.getElementById('alasanBatal').value.trim();
            if (!alasan) {
                showErrorModal('Alasan pembatalan harus diisi');
                return;
            }

            const button = document.getElementById('rejectButton');
            const buttonText = document.getElementById('reject_text');
            const spinner = document.getElementById('reject_spinner');

            button.disabled = true;
            buttonText.classList.add('hidden');
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
                        // Close modals immediately
                        closeRejectModal();

                        // Reload table first
                        if (table) {
                            table.ajax.reload(null, false);
                        }

                        // Wait a bit for table to reload, then refresh modal detail
                        setTimeout(() => {
                            if (currentDetailKunjunganId) {
                                viewDetail(currentDetailKunjunganId);
                            }
                        }, 500);

                        showSuccessModal('Kunjungan berhasil ditolak.');
                    }
                })
                .catch(error => {
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
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

        function closeModal() {
            window.closeModal('detailModal');
        }

        function closeRejectModal() {
            const textarea = document.getElementById('alasanBatal');
            if (textarea) {
                textarea.value = '';
            }
            window.closeModal('rejectModal');
        }

        function closeAcceptModal() {
            window.closeModal('acceptModal');
        }

        function closeKtpModal() {
            window.closeModal('ktpModal');
        }

        function closeKaryawanListModal() {
            window.closeModal('karyawanListModal');
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

        function viewKtp(ktpToken) {
            const modal = document.getElementById('ktpModal');
            const content = document.getElementById('ktpContent');

            if (!modal || !content) {
                console.error('Modal KTP tidak ditemukan');
                return;
            }

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
            const modal = document.getElementById('ktpModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Sidebar navigation loading handled by loading-spinner.js
        document.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', function (e) {
                if (this.href && !this.classList.contains('active')) {
                    showLoading();
                }
            });
        });

        // Supabase Realtime using supabase-realtime.js component
        initSupabaseRealtime({
            channelName: 'kunjungan-realtime',
            tableName: 'kunjungan',
            configUrl: '/api/supabase-config',
            onPayload: (payload) => {
                console.log('Perubahan terdeteksi:', payload.eventType);

                // Reload DataTable tanpa reset pagination
                if (table) {
                    table.ajax.reload(null, false);
                }

                // Update stats cards
                fetch('{{ route("resepsionis.kunjungan.data") }}')
                    .then(res => res.json())
                    .then(result => {
                        if (result.data) {
                            const data = result.data;

                            // Update each stats card value
                            const totalCard = document.querySelector('[data-filter="all"] .stats-value');
                            const pendingCard = document.querySelector('[data-filter="pending"] .stats-value');
                            const doneCard = document.querySelector('[data-filter="done"] .stats-value');
                            const canceledCard = document.querySelector('[data-filter="canceled"] .stats-value');

                            if (totalCard) totalCard.textContent = data.length;
                            if (pendingCard) pendingCard.textContent = data.filter(r => r.status === 'pending').length;
                            if (doneCard) doneCard.textContent = data.filter(r => r.status === 'done').length;
                            if (canceledCard) canceledCard.textContent = data.filter(r => r.status === 'canceled').length;
                        }
                    })
                    .catch(error => console.error('Error updating stats:', error));
            }
        });
    </script>
@endpush