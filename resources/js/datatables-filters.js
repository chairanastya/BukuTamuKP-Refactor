export function initDatatableFilter(options = {}) {
    const {
        filterId = 'departemenFilter',
        filterName = 'Departemen',
        tableInstance = null,
        columnIndex = 3,
        multiple = true,
        dataFetcher = null
    } = options;

    if (!tableInstance) {
        console.error('[initDatatableFilter] tableInstance is required');
        return;
    }

    let selectedFilters = [];

    function createFilterUI() {
        let filterWrapper = document.querySelector('.dataTables_filter');
        if (!filterWrapper) {
            filterWrapper = document.querySelector('.dt-search');
        }

        if (!filterWrapper) {
            console.error('[initDatatableFilter] Filter wrapper not found');
            return;
        }

        document.querySelectorAll('.filter-container').forEach(el => el.remove());

        const container = document.createElement('div');
        container.className = 'filter-container';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'filter-btn';
        button.id = filterId + 'Btn';
        button.innerHTML = `
            <span>${filterName}</span>
            <span id="${filterId}Badge"></span>
            <span style="font-size: 10px;">▼</span>
        `;
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = document.getElementById(filterId + 'Dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        });

        const dropdown = document.createElement('div');
        dropdown.id = filterId + 'Dropdown';
        dropdown.className = 'filter-main-dropdown';
        dropdown.addEventListener('click', (e) => e.stopPropagation());

        container.appendChild(button);
        container.appendChild(dropdown);
        filterWrapper.parentElement.appendChild(container);

        document.addEventListener('click', function closeDropdown() {
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });

        return dropdown;
    }

    async function populateFilter() {
        if (!dataFetcher) {
            console.error('[initDatatableFilter] dataFetcher function is required');
            return;
        }

        try {
            const data = await dataFetcher();
            const dropdown = document.getElementById(filterId + 'Dropdown');
            if (!dropdown) return;

            dropdown.innerHTML = '';

            data.forEach(item => {
                const element = document.createElement('div');
                element.className = 'filter-category-item';
                if (selectedFilters.includes(item)) {
                    element.classList.add('active');
                }
                element.textContent = item;
                element.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleFilter(item, element);
                });
                dropdown.appendChild(element);
            });

            const clearBtn = document.createElement('div');
            clearBtn.className = 'filter-category-item';
            clearBtn.style.borderTop = '1px solid #e5e7eb';
            clearBtn.style.color = '#ef4444';
            clearBtn.textContent = '✕ Hapus Filter';
            clearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                clearFilters();
            });
            dropdown.appendChild(clearBtn);
        } catch (error) {
            console.error('[initDatatableFilter] Error populating filter:', error);
        }
    }

    function toggleFilter(value, element) {
        const index = selectedFilters.indexOf(value);

        if (index > -1) {
            selectedFilters.splice(index, 1);
            element.classList.remove('active');
        } else {
            if (!multiple) {
                selectedFilters = [value];
                document.querySelectorAll(`#${filterId}Dropdown .filter-category-item:not(:last-child)`).forEach(el => {
                    el.classList.remove('active');
                });
                element.classList.add('active');
            } else {
                selectedFilters.push(value);
                element.classList.add('active');
            }
        }

        updateBadge();
        applyTableFilter();
    }

    function clearFilters() {
        selectedFilters = [];
        document.querySelectorAll(`#${filterId}Dropdown .filter-category-item`).forEach(el => {
            el.classList.remove('active');
        });
        updateBadge();
        applyTableFilter();
    }

    function updateBadge() {
        const badge = document.getElementById(filterId + 'Badge');
        const button = document.getElementById(filterId + 'Btn');

        if (selectedFilters.length > 0) {
            badge.innerHTML = `<span class="active-filter-badge">${selectedFilters.length}</span>`;
            button.classList.add('active');
        } else {
            badge.innerHTML = '';
            button.classList.remove('active');
        }
    }

    function applyTableFilter() {
        if ($.fn.dataTable.ext.search.length > 0) {
            $.fn.dataTable.ext.search.pop();
        }

        if (selectedFilters.length > 0) {
            $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
                const cellValue = data[columnIndex];
                return selectedFilters.includes(cellValue);
            });
        }

        tableInstance.draw();
    }

    createFilterUI();
    populateFilter();

    return {
        refresh: populateFilter,
        clear: clearFilters,
        getSelected: () => selectedFilters
    };
}

export default initDatatableFilter;
