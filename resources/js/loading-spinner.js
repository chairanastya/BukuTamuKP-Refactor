let loadingTimer = null;
let loadingStartTime = null;

window.showLoading = function () {
    loadingStartTime = Date.now();
    document.getElementById('loading-overlay').classList.add('active');
}

window.hideLoading = function () {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
    loadingStartTime = null;
}

window.createInlineSpinner = function (text = 'Memuat...') {
    return `<div class="loading-inline">
        <div class="spinner">
            <div></div><div></div><div></div><div></div><div></div>
            <div></div><div></div><div></div><div></div><div></div>
        </div>
        <span class="loading-text">${text}</span>
    </div>`;
}

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