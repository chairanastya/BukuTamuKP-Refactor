import { createInlineSpinner } from './loading-spinner.js';

export function renderKaryawanListModal(karyawanData) {
    const content = document.getElementById('karyawanListContent');
    if (!content) {
        console.warn('[renderKaryawanListModal] karyawanListContent element not found');
        return;
    }

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
    html += '<div class="mt-6"><button onclick="window.closeModal(\'karyawanListModal\')" class="w-full bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-4 rounded-lg transition">Tutup</button></div>';

    content.innerHTML = html;
    window.showModal('karyawanListModal');
}

export function renderDetailModal(kunjungan) {
    const content = document.getElementById('detailContent');
    if (!content) {
        console.warn('[renderDetailModal] detailContent element not found');
        return;
    }

    let karyawanList = kunjungan.karyawan.map(k =>
        `<li>${k.nama} - ${k.jabatan} (${k.departemen})</li>`
    ).join('');

    let actions = '';
    if (kunjungan.status === 'pending') {
        actions = `
            <div class="flex gap-3 mt-6">
                <button onclick="acceptKunjungan(${kunjungan.id_kunjungan})" class="btn-success flex-1">Terima</button>
                <button onclick="openRejectModal(${kunjungan.id_kunjungan})" class="btn-danger flex-1">Tolak</button>
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

    content.innerHTML = `
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

    window.showModal('detailModal');
}

export function renderKtpModal(streamUrl) {
    const content = document.getElementById('ktpContent');
    if (!content) {
        console.warn('[renderKtpModal] ktpContent element not found');
        return;
    }

    content.innerHTML = createInlineSpinner('Memuat KTP...');
    window.showModal('ktpModal');

    const img = new Image();

    img.onload = function () {
        content.innerHTML = `<img src="${streamUrl}" alt="KTP" class="ktp-preview rounded-lg shadow-lg">`;
    };

    img.onerror = function () {
        content.innerHTML = '<div class="text-red-600"><p class="font-semibold mb-2">Gagal memuat KTP</p><p class="text-sm">Terjadi kesalahan saat memuat gambar</p></div>';
    };

    img.src = streamUrl;
}

export default {
    renderKaryawanListModal,
    renderDetailModal,
    renderKtpModal
};
