export function createAutocomplete(config) {
    const { input, dropdown, searchRoute, validateFn, label } = config;
    let allItems = [];

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

        items.forEach(item => {
            const li = document.createElement('li');
            li.textContent = item.name || item.label || item;
            li.addEventListener('click', () => select(item));
            dropdown.appendChild(li);
        });

        dropdown.classList.add('show');
    };

    const select = (value) => {
        input.value = value.name || value.label || value;
        dropdown.classList.remove('show');
        if (validateFn) {
            validateFn(value);
        }
    };

    const addNew = () => {
        // Placeholder for adding new item functionality
        console.log('Add new item functionality not implemented yet');
    };

    input.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length > 0) {
            search(query);
        } else {
            display(allItems, query);
        }
    });

    input.addEventListener('focus', toggle);
    input.addEventListener('blur', () => {
        setTimeout(() => dropdown.classList.remove('show'), 150);
    });

    return { toggle, select, addNew };
}