let loadingTimer = null;
let loadingStartTime = null;

export function showLoading() {
    loadingStartTime = Date.now();
    document.getElementById('loading-overlay').classList.add('active');
}

export function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
    loadingStartTime = null;
}

export function createInlineSpinner(text = 'Memuat...') {
    return `<div class="loading-inline">
        <div class="spinner">
            <div></div><div></div><div></div><div></div><div></div>
            <div></div><div></div><div></div><div></div><div></div>
        </div>
        <span class="loading-text">${text}</span>
    </div>`;
}

export function initLoadingSpinner() {
    showLoading();

    window.addEventListener('load', function () {
        setTimeout(hideLoading, 300);
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form:not([data-no-loading])').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.checkValidity()) {
                    showLoading();
                }
            });
        });

        document.querySelectorAll('a[data-loading]').forEach(function (link) {
            link.addEventListener('click', function () {
                showLoading();
            });
        });
    });
}