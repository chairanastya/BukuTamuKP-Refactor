export function createAutocomplete(config) {
    const { input, dropdown, searchRoute, validateFn, label } = config;
    let allItems = [];
    let debounceTimeout;
    let isPreloaded = false;

    const toggle = () => {
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        } else {
            displayPreloaded();
        }
    };

    // Just preload data without showing dropdown
    const preloadData = () => {
        if (isPreloaded && allItems.length > 0) {
            return;
        }

        fetch(`${searchRoute}?q=`)
            .then(response => response.json())
            .then(data => {
                allItems = data;
                isPreloaded = true;
            })
            .catch(error => console.error(`Error loading ${label}:`, error));
    };

    // Load and display preloaded data
    const loadAll = () => {
        if (!isPreloaded) {
            preloadData();
            return;
        }
        displayPreloaded();
    };

    // Display preloaded data with query filter
    const displayPreloaded = () => {
        display(allItems, input.value.trim());
    };

    // Fuzzy search on preloaded data
    const fuzzySearch = (query) => {
        if (!query) return allItems;
        
        const q = query.toLowerCase();
        return allItems
            .filter(item => 
                (item.name || item.label || item)
                    .toString()
                    .toLowerCase()
                    .includes(q)
            )
            .sort((a, b) => {
                const aStr = (a.name || a.label || a).toString().toLowerCase();
                const bStr = (b.name || b.label || b).toString().toLowerCase();
                // Prioritize prefix matches
                const aStart = aStr.startsWith(q) ? 0 : 1;
                const bStart = bStr.startsWith(q) ? 0 : 1;
                if (aStart !== bStart) return aStart - bStart;
                return aStr.localeCompare(bStr);
            });
    };

    const display = (items, query = '') => {
        dropdown.innerHTML = '';
        if (items.length === 0) {
            dropdown.classList.remove('show');
            return;
        }

        // Use fuzzy search for client-side filtering (instant!)
        const filteredItems = query ? fuzzySearch(query) : items.slice(0, 20);

        if (filteredItems.length === 0) {
            dropdown.classList.remove('show');
            return;
        }

        // Render filtered items
        filteredItems.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'autocomplete-item';
            itemDiv.textContent = item.name || item.label || item;
            itemDiv.addEventListener('click', () => select(item));
            dropdown.appendChild(itemDiv);
        });

        // Render "Add New" option
        const addNewDiv = document.createElement('div');
        addNewDiv.className = 'autocomplete-item';
        addNewDiv.style.borderTop = '1px solid #e5e7eb';
        addNewDiv.style.backgroundColor = '#f3f4f6';
        addNewDiv.style.fontWeight = '600';
        addNewDiv.style.cursor = 'pointer';
        addNewDiv.textContent = `+ Tambah ${label} Baru`;
        addNewDiv.addEventListener('click', () => addNew());
        dropdown.appendChild(addNewDiv);

        dropdown.classList.add('show');
    };

    const select = (value) => {
        input.value = value.name || value.label || value;
        dropdown.classList.remove('show');
        if (validateFn && typeof validateFn === 'function') {
            validateFn(value);
        }
    };

    const addNew = () => {
        dropdown.classList.remove('show');
        input.focus();
        if (input.value.trim() === '') {
            input.placeholder = `Ketik ${label.toLowerCase()} baru...`;
        }
    };

    // Event listeners
    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimeout);

        if (query.length === 0) {
            dropdown.classList.remove('show');
            return;
        }

        // Preload if not already done
        if (!isPreloaded) {
            loadAll();
            return;
        }

        // Use preloaded data with instant fuzzy search (50ms debounce only for rendering)
        debounceTimeout = setTimeout(() => {
            display(allItems, query);
        }, 50);
    });

    input.addEventListener('focus', function () {
        if (this.value.trim().length > 0 && allItems.length > 0) {
            display(allItems, this.value.trim());
        } else if (!isPreloaded) {
            loadAll();
        }
    });

    input.addEventListener('blur', () => {
        setTimeout(() => dropdown.classList.remove('show'), 150);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Preload data on initialization (without showing dropdown)
    preloadData();

    return { toggle, select, addNew };
}

export default createAutocomplete;