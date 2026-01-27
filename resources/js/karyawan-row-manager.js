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
                    @svg('heroicon-o-plus', 'w-7 h-7')
                </button>
                <button type="button" class="karyawan-minus-btn" onclick="removeKaryawanRow(${rowId})" title="Hapus baris">
                    @svg('heroicon-o-minus', 'w-7 h-7')
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
    fetch(`${window.karyawanSearchRoute}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => displayAutocomplete(data, rowId, dropdown))
        .catch(error => console.error('Error searching karyawan:', error));
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
    content.innerHTML = `
        <div class="karyawan-card w-full" onclick="resetKaryawanRow(${rowId})" title="Klik untuk mengganti karyawan">
            <div class="karyawan-card-info">
                <div class="karyawan-card-name">${window.escapeHtmlFn(nama)}</div>
                <div class="karyawan-card-detail">${window.escapeHtmlFn(jabatan)} - ${window.escapeHtmlFn(departemen)}</div>
            </div>
            @svg('zondicon-edit-pencil', 'w-5 h-5 text-[#084E8F]')
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