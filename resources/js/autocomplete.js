export function createAutocomplete(config) {
    const { input, dropdown, searchRoute, validateFn, label } = config;
    let allItems = [];
    let debounceTimeout;

    const toggle = () => {
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        } else {
            loadAll();
        }
    };

    const loadAll = () => {
        fetch(`${searchRoute}?q=`)
            .then(response => response.json())
            .then(data => {
                allItems = data;
                display(data, input.value.trim());
            })
            .catch(error => console.error(`Error loading ${label}:`, error));
    };

    const search = (query) => {
        fetch(`${searchRoute}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                allItems = data;
                display(data, query);
            })
            .catch(error => console.error(`Error searching ${label}:`, error));
    };

    const display = (items, query = '') => {
        dropdown.innerHTML = '';
        if (items.length === 0) {
            dropdown.classList.remove('show');
            return;
        }

        // Filter items if query exists
        const filteredItems = query 
            ? items.filter(item => 
                (item.name || item.label || item)
                    .toString()
                    .toLowerCase()
                    .includes(query.toLowerCase())
            )
            : items;

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

        debounceTimeout = setTimeout(() => {
            allItems.length > 0 ? display(allItems, query) : search(query);
        }, 300);
    });

    input.addEventListener('focus', function () {
        if (this.value.trim().length > 0 && allItems.length > 0) {
            display(allItems, this.value.trim());
        } else if (allItems.length === 0) {
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

    return { toggle, select, addNew };
}

export default createAutocomplete;