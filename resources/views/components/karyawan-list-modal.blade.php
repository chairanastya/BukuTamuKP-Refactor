@props(['karyawanList', 'modalId' => 'karyawan_modal'])

<div id="{{ $modalId }}" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Daftar Karyawan Tertuju</h2>
            <button type="button" class="modal-close" onclick="closeKaryawanModal()">&times;</button>
        </div>
        <div class="px-1">
            <p class="text-gray-600 mb-4">Total {{ $karyawanList->count() }} karyawan yang terlibat dalam kunjungan ini:</p>
            <div class="space-y-3">
                @foreach($karyawanList as $index => $karyawan)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-[#084E8F] text-white rounded-full flex items-center justify-center font-bold">
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

@once
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
            const karyawanModal = document.getElementById('{{ $modalId }}');

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
@endonce
