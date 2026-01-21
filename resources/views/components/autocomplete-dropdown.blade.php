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