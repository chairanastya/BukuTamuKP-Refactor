export class DatatableMultiFilter {
    constructor(options = {}) {
        this.options = {
            tableInstance: null,
            filterContainerId: 'multiFilterContainer',
            dataFetcher: null,
            filters: {
                date: false,
                instansi: false,
                karyawan: false,
                custom: []
            },
            onFiltersChanged: null,
            ...options
        };

        this.state = {
            dateStart: null,
            dateEnd: null,
            instansi: [],
            karyawan: [],
            custom: {}
        };

        this.elements = {};
        this.init();
    }

    init() {
        if (!this.options.tableInstance) {
            console.error('[DatatableMultiFilter] tableInstance is required');
            return;
        }

        this.createFilterUI();
        this.setupDataTableFilter();
    }

    createFilterUI() {
        let filterWrapper = document.querySelector('.dataTables_filter');
        if (!filterWrapper) {
            filterWrapper = document.querySelector('.dt-search');
        }

        if (!filterWrapper) {
            console.error('[DatatableMultiFilter] Filter wrapper not found');
            return;
        }

        // Remove existing filter containers
        document.querySelectorAll('.filter-container').forEach(el => el.remove());

        const container = document.createElement('div');
        container.className = 'filter-container';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'filter-btn';
        button.id = 'multiFilterBtn';
        button.innerHTML = `
            <span class="inline-flex items-center gap-1">
                ${window.filterIcon || '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>'}
                Filter By
            </span>
            <span id="multiFilterBadge"></span>
            <span style="font-size: 10px;">▼</span>
        `;

        const dropdown = document.createElement('div');
        dropdown.className = 'filter-main-dropdown';
        dropdown.id = 'multiFilterDropdown';

        container.appendChild(button);
        container.appendChild(dropdown);
        filterWrapper.parentElement.appendChild(container);

        this.elements.button = button;
        this.elements.dropdown = dropdown;
        this.elements.badge = document.getElementById('multiFilterBadge');

        // Create UI structure first
        this.updateDropdownContent();
        
        // Now setup events so elements exist
        this.setupEventListeners();
        
        // Then populate data
        if (this.options.dataFetcher) {
            this.populateFilters();
        }
    }

    updateDropdownContent() {
        this.elements.dropdown.innerHTML = '';

        if (this.options.filters.date) {
            this.elements.dropdown.appendChild(this.createDateFilterUI());
        }

        if (this.options.filters.instansi) {
            const instansiItem = document.createElement('div');
            instansiItem.className = 'filter-category-item';
            instansiItem.innerHTML = `<span>Instansi</span><span style="font-size: 10px;">▶</span>`;
            instansiItem.dataset.category = 'instansi';

            const instansiDropdown = document.createElement('div');
            instansiDropdown.className = 'filter-sub-dropdown';
            instansiDropdown.id = 'instansiSubDropdown';
            instansiItem.appendChild(instansiDropdown);

            this.elements.dropdown.appendChild(instansiItem);
            this.elements.instansiDropdown = instansiDropdown;
        }

        if (this.options.filters.karyawan) {
            const karyawanItem = document.createElement('div');
            karyawanItem.className = 'filter-category-item';
            karyawanItem.innerHTML = `<span>Karyawan</span><span style="font-size: 10px;">▶</span>`;
            karyawanItem.dataset.category = 'karyawan';

            const karyawanDropdown = document.createElement('div');
            karyawanDropdown.className = 'filter-sub-dropdown';
            karyawanDropdown.id = 'karyawanSubDropdown';
            karyawanItem.appendChild(karyawanDropdown);

            this.elements.dropdown.appendChild(karyawanItem);
            this.elements.karyawanDropdown = karyawanDropdown;
        }
    }

    createDateFilterUI() {
        const container = document.createElement('div');
        container.className = 'filter-category-item';
        container.dataset.category = 'tanggal';
        container.innerHTML = '<span>Tanggal</span><span style="font-size: 10px;">▶</span>';

        const subDropdown = document.createElement('div');
        subDropdown.className = 'filter-sub-dropdown date-range-filter';

        const startGroup = document.createElement('div');
        startGroup.className = 'date-input-group';
        startGroup.innerHTML = `
            <label class="date-input-label">Dari Tanggal:</label>
            <input type="date" id="dateFilterStart" class="date-input">
        `;

        const endGroup = document.createElement('div');
        endGroup.className = 'date-input-group';
        endGroup.innerHTML = `
            <label class="date-input-label">Sampai Tanggal:</label>
            <input type="date" id="dateFilterEnd" class="date-input">
        `;

        const actions = document.createElement('div');
        actions.className = 'date-filter-actions';
        const applyBtn = document.createElement('button');
        applyBtn.className = 'date-filter-btn date-filter-btn-apply';
        applyBtn.textContent = 'Terapkan';
        
        const clearBtn = document.createElement('button');
        clearBtn.className = 'date-filter-btn date-filter-btn-clear';
        clearBtn.textContent = 'Hapus';
        
        applyBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.applyDateFilter();
        });
        clearBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.clearDateFilter();
        });

        actions.appendChild(applyBtn);
        actions.appendChild(clearBtn);
        
        subDropdown.appendChild(startGroup);
        subDropdown.appendChild(endGroup);
        subDropdown.appendChild(actions);
        container.appendChild(subDropdown);

        this.elements.dateFilterStart = startGroup.querySelector('input');
        this.elements.dateFilterEnd = endGroup.querySelector('input');

        return container;
    }

    async populateFilters() {
        try {
            const data = await this.options.dataFetcher();

            if (this.options.filters.instansi && this.elements.instansiDropdown) {
                this.populateInstansiFilter(data);
            }

            if (this.options.filters.karyawan && this.elements.karyawanDropdown) {
                this.populateKaryawanFilter(data);
            }
        } catch (error) {
            console.error('[DatatableMultiFilter] Error populating filters:', error);
        }
    }

    populateInstansiFilter(data) {
        const instansi = [...new Set(data.map(item => item.instansi || item.instansi_tamu || ''))].filter(Boolean).sort();

        this.elements.instansiDropdown.innerHTML = '';
        instansi.forEach(inst => {
            const item = document.createElement('div');
            item.className = 'filter-dropdown-item';
            item.dataset.value = inst;
            item.textContent = inst;
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleInstansiFilter(inst);
            });
            this.elements.instansiDropdown.appendChild(item);
        });

        const clearBtn = document.createElement('div');
        clearBtn.className = 'filter-clear';
        clearBtn.textContent = '✕ Hapus Filter';
        clearBtn.addEventListener('click', () => this.clearInstansiFilter());
        this.elements.instansiDropdown.appendChild(clearBtn);
    }

    populateKaryawanFilter(data) {
        const karyawanMap = new Map();

        data.forEach(item => {
            const karyawanArray = item.karyawan || [];
            karyawanArray.forEach(k => {
                const key = `${k.nama_karyawan || k.nama}|${k.departemen || ''}|${k.jabatan || ''}`;
                if (!karyawanMap.has(key)) {
                    karyawanMap.set(key, {
                        nama: k.nama_karyawan || k.nama,
                        departemen: k.departemen || '',
                        jabatan: k.jabatan || ''
                    });
                }
            });
        });

        const karyawan = [...karyawanMap.values()].sort((a, b) => a.nama.localeCompare(b.nama));

        this.elements.karyawanDropdown.innerHTML = '';
        karyawan.forEach(kary => {
            const uniqueKey = `${kary.nama}|${kary.departemen}|${kary.jabatan}`;
            const item = document.createElement('div');
            item.className = 'karyawan-item';
            item.dataset.value = uniqueKey;
            item.innerHTML = `
                <div class="karyawan-name">${kary.nama}</div>
                <div class="karyawan-detail">${kary.departemen || 'N/A'} • ${kary.jabatan || 'N/A'}</div>
            `;
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleKaryawanFilter(uniqueKey);
            });
            this.elements.karyawanDropdown.appendChild(item);
        });

        const clearBtn = document.createElement('div');
        clearBtn.className = 'filter-clear';
        clearBtn.textContent = '✕ Hapus Filter';
        clearBtn.addEventListener('click', () => this.clearKaryawanFilter());
        this.elements.karyawanDropdown.appendChild(clearBtn);
    }

    setupEventListeners() {
        // Toggle main dropdown
        this.elements.button.addEventListener('click', (e) => {
            e.stopPropagation();
            this.elements.dropdown.classList.toggle('show');
        });

        // Show sub-dropdown on CLICK for all category items
        document.querySelectorAll('.filter-category-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const subDropdown = item.querySelector('.filter-sub-dropdown');
                if (subDropdown) {
                    // Toggle this sub-dropdown
                    const isOpen = subDropdown.classList.contains('show');
                    // Close all sub-dropdowns first
                    document.querySelectorAll('.filter-sub-dropdown').forEach(el => el.classList.remove('show'));
                    // If wasn't open, open it now
                    if (!isOpen) {
                        subDropdown.classList.add('show');
                        // Auto-focus first date input if it's a date filter
                        if (item.dataset.category === 'tanggal') {
                            setTimeout(() => {
                                this.elements.dateFilterStart?.focus();
                            }, 50);
                        }
                    }
                }
            });
        });

        // Stop propagation on date inputs to prevent dropdown closing
        if (this.elements.dateFilterStart) {
            this.elements.dateFilterStart.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
        if (this.elements.dateFilterEnd) {
            this.elements.dateFilterEnd.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Close dropdown on outside click
        document.addEventListener('click', (e) => {
            const isInsideFilterContainer = e.target.closest('.filter-container');
            
            // Only close if click is truly outside the filter container
            if (!isInsideFilterContainer) {
                this.elements.dropdown.classList.remove('show');
                document.querySelectorAll('.filter-sub-dropdown').forEach(el => el.classList.remove('show'));
            }
        });

        // Prevent dropdown close on click inside
        this.elements.dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    setupDataTableFilter() {
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            // Date filter
            if (this.state.dateStart || this.state.dateEnd) {
                const tanggalStr = data[1]; // Assuming tanggal is in column 1
                if (tanggalStr) {
                    const parts = tanggalStr.split('/');
                    if (parts.length === 3) {
                        const tanggalFormatted = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        if (this.state.dateStart && tanggalFormatted < this.state.dateStart) return false;
                        if (this.state.dateEnd && tanggalFormatted > this.state.dateEnd) return false;
                    }
                }
            }

            // Instansi filter
            if (this.state.instansi.length > 0) {
                const instansi = data[5]; // Adjust column index as needed
                if (!this.state.instansi.includes(instansi)) return false;
            }

            // Karyawan filter
            if (this.state.karyawan.length > 0) {
                const rowData = this.options.tableInstance.row(dataIndex).data();
                const karyawanArray = rowData.karyawan || [];
                const hasMatch = this.state.karyawan.some(filterKey => {
                    const [filterNama, filterDepartemen, filterJabatan] = filterKey.split('|');
                    return karyawanArray.some(k =>
                        (k.nama_karyawan || k.nama) === filterNama &&
                        (k.departemen || '') === filterDepartemen &&
                        (k.jabatan || '') === filterJabatan
                    );
                });
                if (!hasMatch) return false;
            }

            return true;
        });
    }

    applyDateFilter() {
        const startDate = this.elements.dateFilterStart.value;
        const endDate = this.elements.dateFilterEnd.value;

        if (!startDate && !endDate) {
            alert('Silakan pilih minimal satu tanggal (dari atau sampai)');
            return;
        }

        if (startDate && endDate && startDate > endDate) {
            alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            return;
        }

        this.state.dateStart = startDate || null;
        this.state.dateEnd = endDate || null;

        this.updateBadge();
        this.applyFilters();
    }

    clearDateFilter() {
        this.state.dateStart = null;
        this.state.dateEnd = null;
        this.elements.dateFilterStart.value = '';
        this.elements.dateFilterEnd.value = '';

        this.updateBadge();
        this.applyFilters();
    }

    toggleInstansiFilter(instansi) {
        const index = this.state.instansi.indexOf(instansi);
        const item = this.elements.instansiDropdown.querySelector(`[data-value="${instansi}"]`);

        if (index > -1) {
            this.state.instansi.splice(index, 1);
            item.classList.remove('active');
        } else {
            this.state.instansi.push(instansi);
            item.classList.add('active');
        }

        this.updateBadge();
        this.applyFilters();
    }

    clearInstansiFilter() {
        this.state.instansi = [];
        document.querySelectorAll('#instansiSubDropdown .filter-dropdown-item').forEach(el => {
            el.classList.remove('active');
        });

        this.updateBadge();
        this.applyFilters();
    }

    toggleKaryawanFilter(uniqueKey) {
        const index = this.state.karyawan.indexOf(uniqueKey);
        const item = this.elements.karyawanDropdown.querySelector(`[data-value="${uniqueKey}"]`);

        if (index > -1) {
            this.state.karyawan.splice(index, 1);
            item.classList.remove('active');
        } else {
            this.state.karyawan.push(uniqueKey);
            item.classList.add('active');
        }

        this.updateBadge();
        this.applyFilters();
    }

    clearKaryawanFilter() {
        this.state.karyawan = [];
        document.querySelectorAll('#karyawanSubDropdown .karyawan-item').forEach(el => {
            el.classList.remove('active');
        });

        this.updateBadge();
        this.applyFilters();
    }

    updateBadge() {
        let count = 0;
        if (this.state.dateStart || this.state.dateEnd) count++;
        count += this.state.instansi.length;
        count += this.state.karyawan.length;

        if (count > 0) {
            this.elements.badge.innerHTML = `<span class="active-filter-badge">${count}</span>`;
            this.elements.button.classList.add('active');
        } else {
            this.elements.badge.innerHTML = '';
            this.elements.button.classList.remove('active');
        }
    }

    applyFilters() {
        // Remove old filter if exists
        if ($.fn.dataTable.ext.search.length > 1) {
            $.fn.dataTable.ext.search.pop();
        }

        this.setupDataTableFilter();
        this.options.tableInstance.draw();

        if (this.options.onFiltersChanged) {
            this.options.onFiltersChanged(this.state);
        }
    }

    getState() {
        return { ...this.state };
    }

    clearAll() {
        this.clearDateFilter();
        this.clearInstansiFilter();
        this.clearKaryawanFilter();
    }

    // Method untuk refresh filter options dengan data terbaru
    async refreshFilterOptions() {
        if (this.options.dataFetcher) {
            console.log('[DatatableMultiFilter] Refreshing filter options...');
            await this.populateFilters();
        }
    }
}

export default DatatableMultiFilter;
