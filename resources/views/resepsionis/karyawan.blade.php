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
                <button onclick="closeDeleteModal()"
                    class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg transition">
                    Batalkan
                </button>
                <button id="deleteButton" onclick="confirmDelete()"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <span id="deleteButtonText">Hapus</span>
                    <svg id="deleteSpinner" class="hidden animate-spin h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
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
                <button onclick="closeSuccessModal()"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
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

        function confirmDelete() {
            if (!deleteKaryawanId) return;

            const deleteButton = document.getElementById('deleteButton');
            const deleteButtonText = document.getElementById('deleteButtonText');
            const deleteSpinner = document.getElementById('deleteSpinner');

            // Disable button and show spinner
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
                        alert(data.message || 'Gagal menghapus karyawan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus karyawan');
                })
                .finally(() => {
                    // Re-enable button and hide spinner
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

        // Check for session success message
        @if(session('success'))
            showSuccessModal('{{ session('success') }}');
        @endif

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
                            .channel('karyawan-realtime')
                            .on('postgres_changes',
                                { event: '*', schema: 'public', table: 'karyawan' },
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