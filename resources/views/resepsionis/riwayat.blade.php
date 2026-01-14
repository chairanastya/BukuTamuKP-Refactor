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

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.5rem;
        }
    </style>
@endpush

@section('sidebar')
    <a href="{{ route('resepsionis.dashboard') }}" class="sidebar-item {{ request()->routeIs('resepsionis.dashboard') ? 'active' : '' }}">
        @svg('fluentui-home-24', 'w-8 h-8')
        <span>Beranda</span>
    </a>
    <a href="{{ route('resepsionis.riwayat') }}" class="sidebar-item {{ request()->routeIs('resepsionis.riwayat') ? 'active' : '' }}">
        @svg('gmdi-history', 'w-8 h-8')
        <span>Riwayat</span>
    </a>
    <a href="{{ route('resepsionis.karyawan') }}" class="sidebar-item {{ request()->routeIs('resepsionis.karyawan') ? 'active' : '' }}">
        @svg('gmdi-people-r', 'w-8 h-8')
        <span>Daftar Karyawan</span>
    </a>
@endsection

@section('content')
    <div class="main-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-900">Riwayat Kunjungan</h2>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Total Kunjungan</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $allTimeStats['total'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #E5E7EB;">
                        @svg('akar-people-group', 'w-6 h-6 text-gray-600')
                    </div>
                </div>

                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $allTimeStats['pending'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #FEF3C7;">
                        @svg('far-clock', 'w-6 h-6 text-yellow-600')
                    </div>
                </div>

                <div class="stats-card">
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Done</p>
                        <p class="text-3xl font-bold text-green-600">{{ $allTimeStats['done'] }}</p>
                    </div>
                    <div class="stats-icon" style="background: #D1FAE5;">
                        @svg('heroicon-o-check-circle', 'w-7 h-7 text-green-600')
                    </div>
                </div>

                <div class="stats-card">
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
                <button onclick="confirmReject()" class="btn-danger">Tolak Kunjungan</button>
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
                <div class="text-gray-500">Memuat...</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
    <script>
        let table;
        let currentKunjunganId = null;

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function() {
                initRiwayatTable();
            }, 100);
        });

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
                            setTimeout(function() {
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
                            return `<button onclick="viewKtp('${data.ktp_token}')" class="text-blue-600 hover:underline font-regular">👁 Lihat KTP</button>`;
                        }
                    },
                    { data: 'instansi' },
                    {
                        data: 'karyawan',
                        render: function (data) {
                            if (!data || data.length === 0) return '-';
                            const first = data[0];
                            return `${first.nama}<br><span class="text-xs text-gray-500">${first.jabatan} - ${first.departemen}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function (data) {
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
                            return '<button onclick="viewDetail(' + data.id_kunjungan + ')" class="text-blue-600 hover:underline">👁 Detail</button>';
                        }
                    }
                ],
                pageLength: 10,
                order: [[1, 'desc']]
            });
        }

        function viewDetail(id) {
            fetch(`{{ route('resepsionis.riwayat.data') }}`)
                .then(res => res.json())
                .then(result => {
                    const kunjungan = result.data.find(k => k.id_kunjungan === id);
                    if (!kunjungan) return;

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

                    document.getElementById('detailContent').innerHTML = `
                            <div class="space-y-3">
                                <div><strong>Tanggal:</strong> ${kunjungan.tanggal}</div>
                                <div><strong>Jam:</strong> ${kunjungan.jam}</div>
                                <div><strong>Nama Tamu:</strong> ${kunjungan.nama_tamu}</div>
                                <div><strong>Email:</strong> ${kunjungan.email_tamu}</div>
                                <div><strong>Instansi:</strong> ${kunjungan.instansi}</div>
                                <div><strong>Tujuan Kunjungan:</strong> ${kunjungan.tujuan_kunjungan}</div>
                                <div><strong>Karyawan Tujuan:</strong><ul class="list-disc ml-6">${karyawanList}</ul></div>
                                <div><strong>Status:</strong> ${kunjungan.status}</div>
                                ${cancelReason}
                                ${actions}
                            </div>
                        `;

                    document.getElementById('detailModal').classList.add('show');
                });
        }

        function acceptKunjungan(id) {
            if (!confirm('Terima kunjungan ini? Email notifikasi akan dikirim ke karyawan tujuan untuk mengisi notulensi.')) return;

            fetch(`/resepsionis/kunjungan/${id}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Kunjungan berhasil diterima. Email telah dikirim ke karyawan tujuan untuk mengisi notulensi.');
                        closeModal();
                        table.ajax.reload();
                        location.reload();
                    }
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
                alert('Alasan pembatalan harus diisi');
                return;
            }

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
                        alert('Kunjungan berhasil ditolak');
                        closeRejectModal();
                        table.ajax.reload();
                        location.reload();
                    }
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

            content.innerHTML = '<div class="flex flex-col items-center gap-3"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div><p class="text-gray-600">Memuat KTP...</p></div>';
            modal.classList.add('show');

            const streamUrl = `/resepsionis/ktp/${ktpToken}/stream`;
            const img = new Image();

            img.onload = function () {
                content.innerHTML = `<img src="${streamUrl}" alt="KTP" class="ktp-preview rounded-lg shadow-lg">`;
            };

            img.onerror = function () {
                content.innerHTML = '<div class="text-red-600"><p class="font-semibold mb-2">❌ Gagal memuat KTP</p><p class="text-sm">Terjadi kesalahan saat memuat gambar</p></div>';
            };

            img.src = streamUrl;
        }

        function closeKtpModal() {
            document.getElementById('ktpModal').classList.remove('show');
        }

        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('button[onclick="toggleDropdown()"]')) {
                document.getElementById('dropdown').classList.add('hidden');
            }
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
    </script>
@endpush
