@extends('layouts.app')
@section('title', 'Daftar Karyawan - Buku Tamu Digital')

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

        .badge-resepsionis {
            background: #DBEAFE;
            color: #193CB8;
        }

        .badge-karyawan {
            background: #E5E7EB;
            color: #374151;
        }

        .btn-primary {
            background: #0C4777;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #F59E0B;
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

        .filter-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 4px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            min-width: 200px;
            max-width: 300px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .filter-dropdown.show {
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
            gap: 16px !important;
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
                <h2 class="text-2xl font-bold text-[#084E8F]">Daftar Karyawan</h2>
                <a href="{{ route('resepsionis.karyawan.create') }}" class="btn-primary flex items-center gap-2">
                    @svg('heroicon-o-plus', 'w-5 h-5')
                    Tambah Karyawan Baru
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Total Karyawan</p>
                        <p class="text-3xl font-bold text-[#084E8F]">{{ $stats['total'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #E5E7EB;">
                        @svg('gmdi-people-r', 'w-6 h-6 text-gray-600')
                    </div>
                </div>

                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Total Departemen</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['departemen'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #DBEAFE;">
                        @svg('heroicon-o-building-office', 'w-6 h-6 text-blue-600')
                    </div>
                </div>

                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Status</p>
                        <p class="text-lg font-semibold text-green-600">Aktif</p>
                    </div>
                    <div class="stats-icon" style="background: #D1FAE5;">
                        @svg('heroicon-o-check-circle', 'w-7 h-7 text-green-600')
                    </div>
                </div>
            </div>

            <!-- DataTable -->
            <div class="bg-white rounded-lg shadow p-6">
                <table id="karyawanTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Departemen</th>
                            <th>Jabatan</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-red-600">Konfirmasi Hapus Karyawan</h3>
                <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="deleteContent" class="mb-6">
                <p class="text-gray-700">Apakah Anda yakin ingin menghapus karyawan <strong id="karyawanName"></strong>?</p>
                <p class="text-sm text-red-600 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg transition">
                    Batalkan
                </button>
                <button id="deleteButton" onclick="confirmDelete()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <span id="deleteButtonText">Hapus</span>
                    <svg id="deleteSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

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
    <script>
        let table;
        const trashIcon = `{!! svg('heroicon-s-trash', 'w-5 h-5')->toHtml() !!}`;
        let currentDepartemenFilter = [];

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                initDataTable();
            }, 100);
        });

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#karyawanTable')) {
                $('#karyawanTable').DataTable().destroy();
            }

            table = new DataTable('#karyawanTable', {
                ajax: {
                    url: '{{ route("resepsionis.karyawan.data") }}',
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
                    { data: 'nama_karyawan' },
                    { data: 'email_karyawan' },
                    { data: 'departemen' },
                    { data: 'jabatan' },
                    {
                        data: 'is_resepsionis',
                        render: function (data) {
                            if (data) {
                                return '<span class="badge badge-resepsionis">Resepsionis</span>';
                            }
                            return '<span class="badge badge-karyawan">Karyawan</span>';
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            const escapedName = data.nama_karyawan.replace(/'/g, "\\'");
                            return `<button onclick="openDeleteModal(${data.id_karyawan}, '${escapedName}')" class="text-red-600 hover:text-red-800 transition">${trashIcon}</button>`;
                        }
                    },
                    {
                        data: 'created_at',
                        visible: false
                    }
                ],
                pageLength: 10,
                order: [[7, 'desc']]
            });

            setTimeout(function() {
                addDepartemenFilter();
            }, 200);
        }

        function addDepartemenFilter() {
            let filterWrapper = $('.dataTables_filter');
            if (filterWrapper.length === 0) {
                filterWrapper = $('.dt-search');
            }
            
            if (filterWrapper.length === 0) {
                console.error('Filter wrapper not found!');
                return;
            }
            
            $('.filter-container').remove();
            
            const filterContainer = $('<div class="filter-container"></div>');
            
            const filterBtn = $(`
                <div class="filter-btn" id="departemenFilterBtn">
                    <span>Departemen</span>
                    <span id="departemenBadge"></span>
                    <span style="font-size: 10px;">▼</span>
                </div>
            `);
            
            const dropdown = $('<div class="filter-dropdown" id="departemenDropdown"></div>');
            
            filterContainer.append(filterBtn, dropdown);
            filterWrapper.parent().append(filterContainer);
            
            populateDepartemenFilter();
            
            filterBtn.on('click', function(e) {
                e.stopPropagation();
                dropdown.toggleClass('show');
            });
            
            $(document).on('click', function() {
                dropdown.removeClass('show');
            });
            
            dropdown.on('click', function(e) {
                e.stopPropagation();
            });
        }

        function populateDepartemenFilter() {
            fetch('{{ route("resepsionis.karyawan.data") }}')
                .then(res => res.json())
                .then(result => {
                    const data = result.data;
                    const departemen = [...new Set(data.map(item => item.departemen).filter(d => d && d !== '-'))].sort();
                    const dropdown = $('#departemenDropdown');
                    dropdown.empty();
                    
                    departemen.forEach(dept => {
                        const item = $(`<div class="filter-dropdown-item" data-value="${dept}">${dept}</div>`);
                        item.on('click', function(e) {
                            e.stopPropagation();
                            applyDepartemenFilter(dept);
                        });
                        dropdown.append(item);
                    });
                    
                    dropdown.append(`<div class="filter-clear" onclick="clearDepartemenFilter()">✕ Hapus Filter</div>`);
                });
        }

        function applyDepartemenFilter(departemen) {
            const index = currentDepartemenFilter.indexOf(departemen);
            const item = $(`#departemenDropdown .filter-dropdown-item[data-value="${departemen}"]`);
            
            if (index > -1) {
                currentDepartemenFilter.splice(index, 1);
                item.removeClass('active');
            } else {
                currentDepartemenFilter.push(departemen);
                item.addClass('active');
            }
            
            updateDepartemenBadge();
            applyDepartemenTableFilter();
        }

        function clearDepartemenFilter() {
            currentDepartemenFilter = [];
            $('#departemenDropdown .filter-dropdown-item').removeClass('active');
            updateDepartemenBadge();
            applyDepartemenTableFilter();
        }

        function updateDepartemenBadge() {
            const badge = $('#departemenBadge');
            const btn = $('#departemenFilterBtn');
            
            if (currentDepartemenFilter.length > 0) {
                badge.html(`<span class="active-filter-badge">${currentDepartemenFilter.length}</span>`);
                btn.addClass('active');
            } else {
                badge.html('');
                btn.removeClass('active');
            }
        }

        function applyDepartemenTableFilter() {
            if ($.fn.dataTable.ext.search.length > 0) {
                $.fn.dataTable.ext.search.pop();
            }
            
            if (currentDepartemenFilter.length > 0) {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const departemen = data[3];
                        return currentDepartemenFilter.includes(departemen);
                    }
                );
            }
            
            table.draw();
        }

        let deleteKaryawanId = null;

        function openDeleteModal(id, nama) {
            deleteKaryawanId = id;
            document.getElementById('karyawanName').textContent = nama;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            deleteKaryawanId = null;
            document.getElementById('deleteModal').classList.remove('show');
        }

        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.add('show');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('show');
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.add('show');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.remove('show');
        }

        function confirmDelete() {
            if (!deleteKaryawanId) return;

            const deleteButton = document.getElementById('deleteButton');
            const deleteButtonText = document.getElementById('deleteButtonText');
            const deleteSpinner = document.getElementById('deleteSpinner');

            deleteButton.disabled = true;
            deleteButton.classList.add('opacity-70', 'cursor-not-allowed');
            deleteButtonText.textContent = 'Menghapus...';
            deleteSpinner.classList.remove('hidden');

            fetch(`/resepsionis/karyawan/${deleteKaryawanId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    table.ajax.reload();
                    showSuccessModal(data.message);
                } else {
                    closeDeleteModal();
                    showErrorModal(data.message || 'Gagal menghapus karyawan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                closeDeleteModal();
                showErrorModal('Terjadi kesalahan saat menghapus karyawan');
            })
            .finally(() => {
                deleteButton.disabled = false;
                deleteButton.classList.remove('opacity-70', 'cursor-not-allowed');
                deleteButtonText.textContent = 'Hapus';
                deleteSpinner.classList.add('hidden');
            });
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

        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        document.getElementById('successModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });

        @if(session('success'))
            showSuccessModal('{{ session('success') }}');
        @endif
    </script>
@endpush