<x-message-page 
    :type="$type === 'terima' ? 'accept' : 'reject'" 
    :title="$type === 'terima' ? 'Kunjungan Diterima' : 'Kunjungan Ditolak'" 
    :message="$message" 
    :kunjungan="$kunjungan"
    pageTitle="Konfirmasi Berhasil"
    :showButton="false"
/>
