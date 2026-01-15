@extends('layouts.app')
@section('title', 'Riwayat - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <div class="relative">
        <button onclick="toggleDropdown()" class="flex items-center gap-2">
            <span>{{ Auth::user()->nama_resepsionis }}</span>
            @svg('uiw-down', 'w-5 h-5')
        </button>
        <div id="dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg overflow-hidden z-0">
            <form method="POST" action="{{ route('resepsionis.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700">
                    Log Out
                </button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f9fafb;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }

        .stats-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #FEF9C2;
            color: #D08700;
        }

        .badge-accepted {
            background: #DBEAFE;
            color: #193CB8;
        }

        .badge-done {
            background: #DCFCE7;
            color: #008236;
        }

        .badge-canceled {
            background: #FFE2E2;
            color: #C10007;
        }

        .btn-export {
            background: #059669;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-export:hover {
            background: #047857;
        }

        .btn-export-pdf {
            background: #DC2626;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-export-pdf:hover {
            background: #B91C1C;
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
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content.large {
            max-width: 900px;
        }

        .ktp-preview {
            width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }

        table.dataTable {
            width: 100% !important;
        }

        .dt-length select.dt-input {
            padding-right: 28px !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dt-search input.dt-input {
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            padding: 2px 12px !important;
            font-size: 14px !important;
            background-color: white !important;
            transition: all 0.2s !important;
            min-width: 200px;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder,
        .dt-search input.dt-input::placeholder {
            color: #9CA3AF;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dt-search input.dt-input:focus {
            outline: none !important;
            border-color: #47B9AE !important;
            box-shadow: 0 0 0 3px rgba(71, 185, 174, 0.1) !important;
        }

        .filter-container {
            display: flex;
            gap: 8px;
            align-items: center;
            position: relative;
        }

        .filter-btn {
            background: white;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .filter-btn:hover {
            background: #f3f4f6;
            border-color: #47B9AE;
        }

        .filter-btn.active {
            background: #0C4777;
            color: white;
            border-color: #0C4777;
        }

        .filter-main-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 4px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            min-width: 180px;
            z-index: 100;
            display: none;
        }

        .filter-main-dropdown.show {
            display: block;
        }

        .filter-category-item {
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.15s;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 500;
        }

        .filter-category-item:hover {
            background: #f3f4f6;
        }

        .filter-category-item:first-child {
            border-radius: 8px 8px 0 0;
        }

        .filter-category-item:last-child {
            border-radius: 0 0 8px 8px;
        }

        .filter-sub-dropdown {
            position: absolute;
            top: 0;
            right: 100%;
            margin-right: 4px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            min-width: 200px;
            max-width: 300px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 101;
            display: none;
        }

        .date-range-filter {
            padding: 16px;
            min-width: 280px;
        }

        .date-input-group {
            margin-bottom: 12px;
        }

        .date-input-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .date-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .date-input:focus {
            outline: none;
            border-color: #47B9AE;
            box-shadow: 0 0 0 3px rgba(71, 185, 174, 0.1);
        }

        .date-filter-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .date-filter-btn {
            flex: 1;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .date-filter-btn-apply {
            background: #0C4777;
            color: white;
        }

        .date-filter-btn-apply:hover {
            background: #084E8F;
        }

        .date-filter-btn-clear {
            background: #EF4444;
            color: white;
        }

        .date-filter-btn-clear:hover {
            background: #DC2626;
        }

        .filter-sub-dropdown.show {
            display: block;
        }

        .filter-dropdown-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.15s;
            font-size: 14px;
        }

        .filter-dropdown-item:hover {
            background: #f3f4f6;
        }

        .filter-dropdown-item.active {
            background: #DBEAFE;
            color: #1E40AF;
            font-weight: 600;
        }

        .karyawan-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid #f3f4f6;
        }

        .karyawan-item:last-of-type {
            border-bottom: none;
        }

        .karyawan-item:hover {
            background: #f3f4f6;
        }

        .karyawan-item.active {
            background: #DBEAFE;
        }

        .karyawan-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 2px;
        }

        .karyawan-detail {
            font-size: 12px;
            color: #6B7280;
        }

        .filter-clear {
            padding: 10px 16px;
            border-top: 1px solid #e5e7eb;
            cursor: pointer;
            color: #EF4444;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }

        .filter-clear:hover {
            background: #FEE2E2;
        }

        .active-filter-badge {
            background: #0C4777;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 4px;
        }

        .dataTables_wrapper .dt-layout-row:first-child {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 24px !important;
            margin-bottom: 16px !important;
        }
        
        .dataTables_wrapper .dt-layout-start {
            flex: 0 0 auto !important;
        }
        
        .dataTables_wrapper .dt-layout-end {
            display: flex !important;
            align-items: center !important;
            gap: 50px !important;
            flex: 0 0 auto !important;
        }
        
        .dataTables_wrapper .dt-search {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
        }

        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dt-search label {
            margin: 0 !important;
            flex-shrink: 0 !important;
        }
        
        .dataTables_wrapper .dt-search input.dt-input {
            flex-shrink: 0 !important;
        }
        
        .filter-container {
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            position: relative !important;
        }
    </style>
@endpush

@section('sidebar')
    <a href="{{ route('resepsionis.dashboard') }}"
        class="sidebar-item {{ request()->routeIs('resepsionis.dashboard') ? 'active' : '' }}">
        @svg('fluentui-home-24', 'w-8 h-8')
        <span>Beranda</span>
    </a>
    <a href="{{ route('resepsionis.riwayat') }}"
        class="sidebar-item {{ request()->routeIs('resepsionis.riwayat') ? 'active' : '' }}">
        @svg('gmdi-history', 'w-8 h-8')
        <span>Riwayat</span>
    </a>
    <a href="{{ route('resepsionis.karyawan') }}"
        class="sidebar-item {{ request()->routeIs('resepsionis.karyawan') ? 'active' : '' }}">
        @svg('gmdi-people-r', 'w-8 h-8')
        <span>Daftar Karyawan</span>
    </a>
@endsection

@section('content')
    <div class="main-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-[#084E8F]">Riwayat Kunjungan</h2>
                <div class="flex items-center gap-3">
                    <button onclick="exportToExcel()" class="btn-export flex items-center gap-2">
                        @svg('heroicon-o-arrow-down-tray', 'w-5 h-5')
                        Export to Excel
                    </button>
                    <button onclick="exportToPDF()" class="btn-export-pdf flex items-center gap-2">
                        @svg('heroicon-o-document-text', 'w-5 h-5')
                        Export to PDF
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stats-card" data-filter="all" onclick="filterByStatus('all')">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Total Kunjungan</p>
                        <p class="text-3xl font-bold text-[#084E8F]">{{ $allTimeStats['total'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #E5E7EB;">
                        @svg('akar-people-group', 'w-6 h-6 text-gray-600')
                    </div>
                </div>

                <div class="stats-card" data-filter="pending" onclick="filterByStatus('pending')">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $allTimeStats['pending'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #FEF3C7;">
                        @svg('far-clock', 'w-6 h-6 text-yellow-600')
                    </div>
                </div>

                <div class="stats-card" data-filter="done" onclick="filterByStatus('done')">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Done</p>
                        <p class="text-3xl font-bold text-green-600">{{ $allTimeStats['done'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #D1FAE5;">
                        @svg('heroicon-o-check-circle', 'w-7 h-7 text-green-600')
                    </div>
                </div>

                <div class="stats-card" data-filter="canceled" onclick="filterByStatus('canceled')">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Canceled</p>
                        <p class="text-3xl font-bold text-red-600">{{ $allTimeStats['canceled'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #FEE2E2;">
                        @svg('heroicon-o-x-circle', 'w-7 h-7 text-red-600')
                    </div>
                </div>
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

    <div id="detailModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Detail Kunjungan</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="detailContent"></div>
        </div>
    </div>

    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="text-xl font-bold mb-4">Tolak Kunjungan</h3>
            <p class="text-gray-600 mb-4">Masukkan alasan penolakan:</p>
            <textarea id="alasanBatal" class="w-full border border-gray-300 rounded-lg p-3 mb-4" rows="4"
                placeholder="Alasan pembatalan..."></textarea>
            <div class="flex gap-3 justify-end">
                <button onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button id="rejectButton" onclick="confirmReject()" class="btn-danger flex items-center justify-center gap-2">
                    <span id="rejectButtonText">Tolak Kunjungan</span>
                    <svg id="rejectSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Accept Confirmation Modal -->
    <div id="acceptModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-green-600">Konfirmasi Terima Kunjungan</h3>
                <button onclick="closeAcceptModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="mb-6">
                <p class="text-gray-700">Apakah Anda yakin ingin menerima kunjungan ini?</p>
                <p class="text-sm text-green-600 mt-2">Email notifikasi akan dikirim ke karyawan tujuan untuk mengisi notulensi.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeAcceptModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg transition">
                    Batalkan
                </button>
                <button id="acceptButton" onclick="confirmAccept()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <span id="acceptButtonText">Terima</span>
                    <svg id="acceptSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="ktpModal" class="modal-overlay">
        <div class="modal-content large">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold">Foto KTP</h3>
                <button onclick="closeKtpModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="ktpContent" class="flex justify-center items-center" style="min-height: 400px;">
                <!-- Content akan di-replace dengan createInlineSpinner saat viewKtp dipanggil -->
            </div>
        </div>
    </div>

    <!-- Karyawan List Modal -->
    <div id="karyawanListModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Daftar Karyawan Tertuju</h3>
                <button onclick="closeKaryawanListModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="karyawanListContent"></div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-green-600">Sukses!</h3>
                <button onclick="closeSuccessModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="successContent" class="mb-6">
                <div class="flex items-center gap-3">
                    @svg('heroicon-o-check-circle', 'w-12 h-12 text-green-500')
                    <p class="text-gray-700" id="successMessage"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="closeSuccessModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-red-600">Terjadi Kesalahan</h3>
                <button onclick="closeErrorModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="errorContent" class="mb-6">
                <div class="flex items-center gap-3">
                    @svg('heroicon-o-exclamation-triangle', 'w-12 h-12 text-red-500')
                    <p class="text-gray-700" id="errorMessage"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="closeErrorModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
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
                { wch: 5 },  { wch: 12 }, { wch: 10 }, { wch: 20 }, { wch: 25 }, { wch: 20 },
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
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'tanggal' },
                    { data: 'jam' },
                    { data: 'nama_tamu' },
                    {
                        data: null,
                        render: function (data) {
                            if (!data.has_ktp || !data.ktp_token) return '-';
                            return `<button onclick="viewKtp('${data.ktp_token}')" class="text-blue-600 hover:underline font-regular inline-flex items-center gap-1">${eyeIcon} Lihat KTP</button>`;
                        }
                    },
                    { data: 'instansi' },
                    {
                        data: 'karyawan',
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
                        data: 'status',
                        render: function (data, type, row) {
                            
                            if (type === 'filter' || type === 'sort') {
                                return data;
                            }
                            
                            const badges = {
                                pending: '<span class="badge badge-pending">Pending</span>',
                                accepted: '<span class="badge badge-accepted">Accepted</span>',
                                approved: '<span class="badge badge-accepted">Accepted</span>',
                                done: '<span class="badge badge-done">Done</span>',
                                canceled: '<span class="badge badge-canceled">Canceled</span>'
                            };
                            return badges[data] || data;
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            if (data.status === 'done') {
                                return '<button onclick="viewHasil(' + data.id_kunjungan + ')" class="btn-view">Lihat Hasil</button>';
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
                initComplete: function() {
                    console.log('DataTable initialized, calling addCustomFilters');
                    setTimeout(function() {
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
                <div class="filter-sub-dropdown date-range-filter" id="dateSubDropdown">
                    <div class="date-input-group">
                        <label class="date-input-label">Dari Tanggal:</label>
                        <input type="date" id="dateFilterStart" class="date-input">
                    </div>
                    <div class="date-input-group">
                        <label class="date-input-label">Sampai Tanggal:</label>
                        <input type="date" id="dateFilterEnd" class="date-input">
                    </div>
                    <div class="date-filter-actions">
                        <button class="date-filter-btn date-filter-btn-apply" onclick="applyDateFilter()">Terapkan</button>
                        <button class="date-filter-btn date-filter-btn-clear" onclick="clearDateFilter()">Hapus</button>
                    </div>
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
            
            filterBtn.on('click', function(e) {
                e.stopPropagation();
                mainDropdown.toggleClass('show');
            });
            
            $('.filter-category-item').on('mouseenter', function() {
                $('.filter-sub-dropdown').removeClass('show');
                const subDropdown = $(this).find('.filter-sub-dropdown');
                subDropdown.addClass('show');
            });
            
            mainDropdown.on('mouseleave', function() {
                $('.filter-sub-dropdown').removeClass('show');
            });
            
            $(document).on('click', function() {
                mainDropdown.removeClass('show');
                $('.filter-sub-dropdown').removeClass('show');
            });
            
            mainDropdown.on('click', function(e) {
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
                        item.on('click', function(e) {
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
                        item.on('click', function(e) {
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
                function(settings, data, dataIndex) {
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

                    const statusBadges = {
                        pending: '<span class="badge badge-pending">Pending</span>',
                        accepted: '<span class="badge badge-accepted">Accepted</span>',
                        approved: '<span class="badge badge-accepted">Accepted</span>',
                        done: '<span class="badge badge-done">Done</span>',
                        canceled: '<span class="badge badge-canceled">Canceled</span>'
                    };
                    const statusBadge = statusBadges[kunjungan.status] || kunjungan.status;

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

        function viewHasil(id) {
            alert('Fitur notulensi & dokumentasi dalam pengembangan');
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
        (function() {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2';
            script.onload = function() {
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
                                    console.log('✨ Perubahan terdeteksi:', payload.eventType);
                                    table.ajax.reload(null, false);
                                }
                            )
                            .subscribe((status) => {
                                if (status === 'SUBSCRIBED') {
                                    console.log('🟢 Realtime active - akan auto-reload saat ada perubahan');
                                }
                            });

                        window.addEventListener('beforeunload', () => channel.unsubscribe());
                    });
            };
            document.head.appendChild(script);
        })();
    </script>
@endpush