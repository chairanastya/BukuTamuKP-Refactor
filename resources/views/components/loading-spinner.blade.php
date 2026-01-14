<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay">
    <div class="spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>

<script>
    // Global loading functions with 1 second delay
    let loadingTimer = null;
    let loadingStartTime = null;

    window.showLoading = function () {
        loadingStartTime = Date.now();

        // Set timer untuk menampilkan loading setelah 1 detik
        loadingTimer = setTimeout(() => {
            document.getElementById('loading-overlay').classList.add('active');
        }, 1000);
    }

    window.hideLoading = function () {
        // Jika loading belum muncul (belum 1 detik), cancel timer
        if (loadingTimer) {
            clearTimeout(loadingTimer);
            loadingTimer = null;
        }

        // Jika loading sudah muncul, sembunyikan
        document.getElementById('loading-overlay').classList.remove('active');
        loadingStartTime = null;
    }

    // Function to create inline loading spinner (for modals, cards, etc)
    window.createInlineSpinner = function (text = 'Memuat...') {
        return `
            <div class="loading-inline">
                <div class="spinner">
                    <div></div><div></div><div></div><div></div><div></div>
                    <div></div><div></div><div></div><div></div><div></div>
                </div>
                <span class="loading-text">${text}</span>
            </div>
        `;
    }

    // Show loading on initial page load
    showLoading();

    // Auto-hide loading on page load
    window.addEventListener('load', function () {
        // Small delay to ensure everything is rendered
        setTimeout(hideLoading, 300);
    });

    // Show loading on page navigation (for SPAs or AJAX)
    document.addEventListener('DOMContentLoaded', function () {
        // Intercept form submissions (for full page forms)
        document.querySelectorAll('form:not([data-no-loading])').forEach(function (form) {
            form.addEventListener('submit', function () {
                showLoading();
            });
        });

        // Intercept link clicks with data-loading attribute
        document.querySelectorAll('a[data-loading]').forEach(function (link) {
            link.addEventListener('click', function () {
                showLoading();
            });
        });
    });
</script>