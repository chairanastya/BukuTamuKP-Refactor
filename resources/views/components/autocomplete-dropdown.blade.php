@props([
    'id' => 'autocomplete_' . uniqid(),
    'placeholder' => 'Cari...',
    'searchRoute' => null,
    'onSelect' => null,
    'minChars' => 2,
    'debounce' => 300,
    'itemNameKey' => 'nama',
    'itemDetailKeys' => ['detail'],
    'resultTransform' => null,
])

<style>
    /* Autocomplete Dropdown */
    .autocomplete-dropdown {
        position: absolute;
        margin-top: 10px;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #084E8F;
        border-radius: 8px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 50;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .autocomplete-dropdown.show {
        display: block;
    }

    .autocomplete-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .autocomplete-item:hover {
        background-color: #F9FCFF;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-name {
        color: #1e40af;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .autocomplete-detail {
        color: #6b7280;
        font-size: 14px;
    }
</style>

<div class="autocomplete-container" {{ $attributes }}>
    <input 
        type="text" 
        id="{{ $id }}_input"
        class="autocomplete-input"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
    />
    <div id="{{ $id }}_dropdown" class="autocomplete-dropdown"></div>
</div>

<script>
(function() {
    const inputId = '{{ $id }}';
    const input = document.getElementById(`${inputId}_input`);
    const dropdown = document.getElementById(`${inputId}_dropdown`);
    const searchRoute = '{{ $searchRoute }}';
    const minChars = {{ $minChars }};
    const debounceDelay = {{ $debounce }};
    const itemNameKey = '{{ $itemNameKey }}';
    const itemDetailKeys = @json($itemDetailKeys);
    const onSelectCallback = '{{ $onSelect }}';
    const resultTransformFn = '{{ $resultTransform }}';
    
    let debounceTimeout;

    if (!input || !dropdown) {
        console.error(`Autocomplete elements not found for ID: ${inputId}`);
        return;
    }

    // Input event listener
    input.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimeout);

        if (query.length < minChars) {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            return;
        }

        debounceTimeout = setTimeout(() => {
            searchKaryawan(query);
        }, debounceDelay);
    });

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Search function
    function searchKaryawan(query) {
        if (!searchRoute) {
            console.error('Search route not provided');
            return;
        }

        const url = `${searchRoute}${searchRoute.includes('?') ? '&' : '?'}q=${encodeURIComponent(query)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Apply transform if provided
                let items = data;
                if (resultTransformFn && typeof window[resultTransformFn] === 'function') {
                    items = window[resultTransformFn](data);
                }
                displayAutocomplete(items);
            })
            .catch(error => {
                console.error('Error searching:', error);
                dropdown.innerHTML = '<div class="autocomplete-item">Error loading results</div>';
                dropdown.classList.add('show');
            });
    }

    // Display results
    function displayAutocomplete(items) {
        if (!items || items.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
            dropdown.classList.add('show');
            return;
        }

        const html = items.map((item, index) => {
            const name = item[itemNameKey] || 'Unknown';
            const details = itemDetailKeys
                .map(key => item[key])
                .filter(val => val)
                .join(' - ');

            // Escape HTML to prevent XSS
            const escapedName = escapeHtml(name);
            const escapedDetails = escapeHtml(details);
            
            // Create data attributes for the item
            const dataAttrs = Object.keys(item)
                .map(key => `data-${key}="${escapeHtml(String(item[key]))}"`).join(' ');

            return `
                <div class="autocomplete-item" 
                     data-index="${index}"
                     ${dataAttrs}
                     onclick="window.autocomplete_${inputId}_select(${index}, ${escapeHtml(JSON.stringify(item))})">
                    <div class="autocomplete-name">${escapedName}</div>
                    ${details ? `<div class="autocomplete-detail">${escapedDetails}</div>` : ''}
                </div>`;
        }).join('');

        dropdown.innerHTML = html;
        dropdown.classList.add('show');
    }

    // Selection handler
    window[`autocomplete_${inputId}_select`] = function(index, item) {
        dropdown.classList.remove('show');
        input.value = '';
        
        // Call the onSelect callback if provided
        if (onSelectCallback && typeof window[onSelectCallback] === 'function') {
            window[onSelectCallback](item, inputId);
        }
        
        // Dispatch custom event
        const event = new CustomEvent('autocomplete:select', {
            detail: { item, inputId }
        });
        input.dispatchEvent(event);
    };

    // Utility function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
})();
</script>
