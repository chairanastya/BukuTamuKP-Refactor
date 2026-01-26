<x-message-page 
    type="success" 
    title="Notulensi Berhasil Disimpan!" 
    :message="$message" 
    :kunjungan="$kunjungan"
    pageTitle="Notulensi Berhasil Disimpan"
    :showButton="false"
>

    @push('scripts')
    <script>
        // Clear saved images from localStorage when reaching success page
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentUrl = window.location.pathname;
            try {
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith('notulensi_images_')) {
                        localStorage.removeItem(key);
                        console.log('Cleared:', key);
                    }
                });
                
                Object.keys(sessionStorage).forEach(key => {
                    if (key.startsWith('form_submitted_')) {
                        sessionStorage.removeItem(key);
                    }
                });
            } catch (e) {
                console.error('Error clearing storage:', e);
            }
        });
    </script>
    @endpush
</x-message-page>