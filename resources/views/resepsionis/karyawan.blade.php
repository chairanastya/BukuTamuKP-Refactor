@extends('layouts.app')
@section('title', 'Daftar Karyawan - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <x-user-dropdown 
        :userName="Auth::user()->nama_resepsionis" 
        :logoutRoute="route('resepsionis.logout')" 
    />
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

        .btn-toggle-active {
            color: #DC2626;
            transition: color 0.2s;
        }

        .btn-toggle-active:hover {
            color: #991B1B;
        }

        .btn-toggle-inactive {
            color: #059669;
            transition: color 0.2s;
        }

        .btn-toggle-inactive:hover {
            color: #047857;
        }


        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .header-buttons-container {
                width: 100%;
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
                <h2 class="text-2xl font-bold text-[#084E8F]">Daftar Karyawan</h2>
                <div class="header-buttons-container">
                    <x-button variant="primary" :href="route('resepsionis.karyawan.create')">
                        Tambah Karyawan Baru
                    </x-button>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <x-stats-card
                    title="Total Karyawan"
                    :value="$stats['total']"
                    icon="gmdi-people-r"
                    iconColor="text-gray-600"
                    valueColor="text-[#084E8F]"
                    bgColor="#E5E7EB"
                    filter="all"
                    onclick="filterByStatus('all')"
                /> 

                <x-stats-card
                    title="Karyawan Aktif"
                    :value="$stats['aktif']"
                    icon="heroicon-o-check-circle"
                    iconColor="text-green-600"
                    valueColor="text-green-600"
                    bgColor="#D1FAE5"
                    filter="aktif"
                    onclick="filterByStatus('aktif')"
                />

                <x-stats-card
                    title="Karyawan Nonaktif"
                    :value="$stats['nonaktif']"
                    icon="heroicon-o-x-circle"
                    iconColor="text-red-600"
                    valueColor="text-red-600"
                    bgColor="#FEE2E2"
                    filter="nonaktif"
                    onclick="filterByStatus('nonaktif')"
                />

                <x-stats-card
                    title="Total Departemen"
                    :value="$stats['departemen']"
                    icon="heroicon-o-building-office"
                    iconColor="text-blue-600"
                    valueColor="text-blue-600"
                    bgColor="#DBEAFE"
                    filter="all"
                    onclick="filterByStatus('all')"
                />
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
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Toggle Status Modal -->
    <x-modal name="toggleStatusModal" id="toggleStatusModal" :useAlpine="false">
        <x-slot name="closeButton">
            <button onclick="closeToggleStatusModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </x-slot>
        <x-slot name="header">
            <h3 class="text-2xl font-bold" id="modalTitle">Konfirmasi Ubah Status</h3>
        </x-slot>
        <div id="toggleContent" class="mb-6">
            <p class="text-gray-700" id="toggleMessage"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeToggleStatusModal()"
                class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg transition">
                Batalkan
            </button>
            <button id="toggleButton" onclick="confirmToggleStatus()"
                class="flex-1 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                <span id="toggleButtonText">Konfirmasi</span>
                <svg id="toggleSpinner" class="hidden animate-spin h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </button>
        </div>
    </x-modal>

    <!-- Success Modal -->
    <x-modal name="successModal" id="successModal" :useAlpine="false">
        <x-slot name="closeButton">
            <button onclick="closeSuccessModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
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
            <button onclick="closeSuccessModal()"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                Tutup
            </button>
        </div>
    </x-modal>

    <!-- Error Modal -->
    <x-modal name="errorModal" id="errorModal" :useAlpine="false">
        <x-slot name="closeButton">
            <button onclick="closeErrorModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
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
            <button onclick="closeErrorModal()"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition">
                Tutup
            </button>
        </div>
    </x-modal>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
    <script>
        let table;
        const trashIcon = `{!! svg('heroicon-s-trash', 'w-5 h-5')->toHtml() !!}`;
        const toggleIcon = `{!! svg('heroicon-o-x-circle', 'w-5 h-5')->toHtml() !!}`;
        const activateIcon = `{!! svg('heroicon-o-check-circle', 'w-5 h-5')->toHtml() !!}`;
        let currentDepartemenFilter = [];
        let currentFilter = 'all';
        let toggleKaryawanData = null;

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                showSuccessModal('{{ session('success') }}');
            @endif

            setTimeout(function () {
                initDataTable();
            }, 100);
        });

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#karyawanTable')) {
                $('#karyawanTable').DataTable().destroy();
            }

            table = new DataTable('#karyawanTable', {
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
                        responsivePriority: 1,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'nama_karyawan', responsivePriority: 2 },
                    { data: 'email_karyawan', responsivePriority: 4 },
                    { data: 'departemen', responsivePriority: 3 },
                    { data: 'jabatan', responsivePriority: 5 },
                    {
                        data: 'role_badge',
                        responsivePriority: 6
                    },
                    {
                        data: 'status_badge',
                        responsivePriority: 7
                    },
                    {
                        data: null,
                        responsivePriority: 8,
                        render: function (data) {
                            const escapedName = data.nama_karyawan.replace(/'/g, "\\'");
                            const status = data.status;
                            const icon = status === 'aktif' ? toggleIcon : activateIcon;
                            const colorClass = status === 'aktif' ? 'btn-toggle-active' : 'btn-toggle-inactive';
                            return `<button onclick="openToggleStatusModal(${data.id_karyawan}, '${escapedName}', '${status}')" class="${colorClass}">${icon}</button>`;
                        }
                    },
                    {
                        data: 'created_at',
                        visible: false
                    }
                ],
                pageLength: 10,
                order: [[8, 'desc']]
            });

            setTimeout(function () {
                addDepartemenFilter();
            }, 200);
        }

        function filterByStatus(status) {
            currentFilter = status;

            // Remove ring from all cards
            document.querySelectorAll('.stats-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
            });

            // Add ring to selected card(s) with matching filter
            document.querySelectorAll(`[data-filter="${status}"]`).forEach(card => {
                card.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
            });

            // Apply filter to table - Column 6 is status
            if (status === 'all') {
                table.column(6).search('').draw();
            } else if (status === 'aktif') {
                table.column(6).search('^Aktif$', true, false).draw();
            } else if (status === 'nonaktif') {
                table.column(6).search('^Nonaktif$', true, false).draw();
            }
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

            filterBtn.on('click', function (e) {
                e.stopPropagation();
                dropdown.toggleClass('show');
            });

            $(document).on('click', function () {
                dropdown.removeClass('show');
            });

            dropdown.on('click', function (e) {
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
                        item.on('click', function (e) {
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
                    function (settings, data, dataIndex) {
                        const departemen = data[3];
                        return currentDepartemenFilter.includes(departemen);
                    }
                );
            }

            table.draw();
        }

        function openToggleStatusModal(id, nama, currentStatus) {
            toggleKaryawanData = { id, nama, currentStatus };

            const modal = document.getElementById('toggleStatusModal');
            const modalTitle = document.getElementById('modalTitle');
            const toggleMessage = document.getElementById('toggleMessage');
            const toggleButton = document.getElementById('toggleButton');

            if (currentStatus === 'aktif') {
                modalTitle.textContent = 'Nonaktifkan Karyawan';
                modalTitle.className = 'text-2xl font-bold text-red-600';
                toggleMessage.innerHTML = `Apakah Anda yakin ingin menonaktifkan karyawan <strong>${nama}</strong>?`;
                toggleButton.className = 'flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2';
            } else {
                modalTitle.textContent = 'Aktifkan Karyawan';
                modalTitle.className = 'text-2xl font-bold text-green-600';
                toggleMessage.innerHTML = `Apakah Anda yakin ingin mengaktifkan kembali karyawan <strong>${nama}</strong>?`;
                toggleButton.className = 'flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2';
            }

            modal.classList.add('show');
        }

        function closeToggleStatusModal() {
            toggleKaryawanData = null;
            document.getElementById('toggleStatusModal').classList.remove('show');
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

        function confirmToggleStatus() {
            if (!toggleKaryawanData) return;

            const toggleButton = document.getElementById('toggleButton');
            const toggleButtonText = document.getElementById('toggleButtonText');
            const toggleSpinner = document.getElementById('toggleSpinner');

            toggleButton.disabled = true;
            toggleButton.classList.add('opacity-70', 'cursor-not-allowed');
            toggleButtonText.textContent = 'Memproses...';
            toggleSpinner.classList.remove('hidden');

            fetch(`/resepsionis/karyawan/${toggleKaryawanData.id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeToggleStatusModal();
                        table.ajax.reload();
                        showSuccessModal(data.message);

                        // Update stats
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        closeToggleStatusModal();
                        showErrorModal(data.message || 'Gagal mengubah status karyawan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    closeToggleStatusModal();
                    showErrorModal('Terjadi kesalahan saat mengubah status karyawan');
                })
                .finally(() => {
                    toggleButton.disabled = false;
                    toggleButton.classList.remove('opacity-70', 'cursor-not-allowed');
                    toggleButtonText.textContent = 'Konfirmasi';
                    toggleSpinner.classList.add('hidden');
                });
        }

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