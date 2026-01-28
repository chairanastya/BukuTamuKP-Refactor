export let selectedKaryawan = [];
export let rowCounter = 0;

export function setSearchKaryawanRoute(route) {
    window.karyawanSearchRoute = route;
}

export function setEscapeHtmlFn(fn) {
    window.escapeHtmlFn = fn;
}

export function addKaryawanRow() {
    const container = document.getElementById('karyawan_rows_container');
    const rowId = rowCounter++;

    const icons = window.KARYAWAN_ICONS || {
        plus: '<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>',
        minus: '<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>'
    };

    const rowHtml = `
        <div id="karyawan-row-${rowId}" class="karyawan-search-row">
            <div class="karyawan-search-container" id="content-${rowId}">
                <div class="w-full h-full px-2 border-2 border-[#084E8F] rounded-lg transition flex items-center">
                    <input type="text" 
                        id="karyawan_input_${rowId}" 
                        placeholder="Cari nama karyawan..."
                        class="w-full karyawan-search-input"
                        autocomplete="off"
                        data-row-id="${rowId}">
                </div>
                <div id="autocomplete_dropdown_${rowId}" class="autocomplete-dropdown"></div>
            </div>
            <div class="karyawan-action-buttons">
                <button type="button" class="karyawan-add-btn" onclick="addKaryawanRow()" title="Tambah karyawan">
                    ${icons.plus}
                </button>
                <button type="button" class="karyawan-minus-btn" onclick="removeKaryawanRow(${rowId})" title="Hapus baris">
                    ${icons.minus}
                </button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', rowHtml);
    setupRowListeners(rowId);
    updateMinusButtonsVisibility();
}

export function removeKaryawanRow(rowId) {
    const rows = document.querySelectorAll('[id^="karyawan-row-"]');

    if (rows.length <= 1) {
        alert('Minimal harus ada satu karyawan yang dituju');
        return;
    }

    const row = document.getElementById(`karyawan-row-${rowId}`);
    selectedKaryawan.splice(selectedKaryawan.findIndex(k => k.rowId === rowId), 1);
    updateHiddenInput();

    if (row) row.remove();
    updateMinusButtonsVisibility();
}

export function setupRowListeners(rowId) {
    const input = document.getElementById(`karyawan_input_${rowId}`);
    const dropdown = document.getElementById(`autocomplete_dropdown_${rowId}`);
    let debounceTimeout;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimeout);

        if (query.length < 2) {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            return;
        }

        debounceTimeout = setTimeout(() => {
            searchKaryawan(query, rowId, dropdown);
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
}

export function searchKaryawan(query, rowId, dropdown) {
    if (!window.karyawanSearchRoute) {
        console.error('Karyawan search route not initialized. Call setSearchKaryawanRoute() first.');
        dropdown.innerHTML = '<div class="autocomplete-item">Error: Route not configured</div>';
        dropdown.classList.add('show');
        return;
    }

    fetch(`${window.karyawanSearchRoute}?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => displayAutocomplete(data, rowId, dropdown))
        .catch(error => {
            console.error('Error searching karyawan:', error);
            dropdown.innerHTML = '<div class="autocomplete-item">Error loading data</div>';
            dropdown.classList.add('show');
        });
}

export function displayAutocomplete(karyawans, rowId, dropdown) {
    if (karyawans.length === 0) {
        dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
        dropdown.classList.add('show');
        return;
    }

    const html = karyawans
        .filter(k => !selectedKaryawan.find(sk => sk.id_karyawan === k.id_karyawan))
        .map(k => `
            <div class="autocomplete-item" onclick="selectKaryawan(${rowId}, ${k.id_karyawan}, '${window.escapeHtmlFn(k.nama_karyawan)}', '${window.escapeHtmlFn(k.jabatan)}', '${window.escapeHtmlFn(k.departemen)}')">
                <div class="autocomplete-name">${window.escapeHtmlFn(k.nama_karyawan)}</div>
                <div class="autocomplete-detail">${window.escapeHtmlFn(k.jabatan)} - ${window.escapeHtmlFn(k.departemen)}</div>
            </div>`)
        .join('');

    dropdown.innerHTML = html;
    dropdown.classList.add('show');
}

export function selectKaryawan(rowId, id, nama, jabatan, departemen) {
    if (selectedKaryawan.find(k => k.id_karyawan === id)) {
        alert('Karyawan ini sudah dipilih di baris lain');
        return;
    }

    selectedKaryawan.splice(selectedKaryawan.findIndex(k => k.rowId === rowId), 1);
    selectedKaryawan.push({ rowId, id_karyawan: id, nama_karyawan: nama, jabatan, departemen });

    renderKaryawanCard(rowId, nama, jabatan, departemen);
    updateHiddenInput();
}

export function renderKaryawanCard(rowId, nama, jabatan, departemen) {
    const content = document.getElementById(`content-${rowId}`);
    const icons = window.KARYAWAN_ICONS || {
        edit: '<svg class="w-5 h-5 text-[#084E8F]" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>'
    };

    content.innerHTML = `
        <div class="karyawan-card w-full" onclick="resetKaryawanRow(${rowId})" title="Klik untuk mengganti karyawan">
            <div class="karyawan-card-info">
                <div class="karyawan-card-name">${window.escapeHtmlFn(nama)}</div>
                <div class="karyawan-card-detail">${window.escapeHtmlFn(jabatan)} - ${window.escapeHtmlFn(departemen)}</div>
            </div>
            ${icons.edit}
        </div>`;
}

export function updateHiddenInput() {
    const ids = selectedKaryawan.map(k => k.id_karyawan);
    document.getElementById('karyawan_ids').value = JSON.stringify(ids);
}

export function resetKaryawanRow(rowId) {
    selectedKaryawan.splice(selectedKaryawan.findIndex(k => k.rowId === rowId), 1);
    updateHiddenInput();

    const content = document.getElementById(`content-${rowId}`);
    content.innerHTML = `
        <div class="w-full h-full px-2 border-2 border-[#084E8F] rounded-lg transition flex items-center">
            <input type="text" 
                id="karyawan_input_${rowId}" 
                placeholder="Cari nama karyawan..."
                class="w-full karyawan-search-input"
                autocomplete="off"
                data-row-id="${rowId}">
        </div>
        <div id="autocomplete_dropdown_${rowId}" class="autocomplete-dropdown"></div>`;

    setupRowListeners(rowId);
}

export function updateMinusButtonsVisibility() {
    const rows = document.querySelectorAll('[id^="karyawan-row-"]');
    const minusButtons = document.querySelectorAll('.karyawan-minus-btn');
    const shouldDisable = rows.length === 1;

    minusButtons.forEach(btn => btn.disabled = shouldDisable);
}