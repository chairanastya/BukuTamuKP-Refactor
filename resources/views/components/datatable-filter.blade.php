@props([
    'activeCount' => 0,
    'buttonText' => 'Filter',
    'alignment' => 'right' // 'right' or 'left'
])

<div class="filter-container" x-data="{ isOpen: false }" @click.away="isOpen = false">
    <button 
        type="button"
        class="filter-btn" 
        :class="{ 'active': {{ $activeCount }} > 0 }"
        @click="isOpen = !isOpen"
    >
        @svg('akar-settings-horizontal', 'w-5 h-5')
        <span>{{ $buttonText }}</span>
        @if($activeCount > 0)
            <span class="active-filter-badge">{{ $activeCount }}</span>
        @endif
    </button>

    <div 
        class="filter-main-dropdown {{ $alignment === 'left' ? 'left-0' : 'right-0' }}"
        :class="{ 'show': isOpen }"
        @click.stop
    >
        {{ $slot }}
    </div>
</div>

<style>
.filter-container {
    display: flex;
    gap: 8px;
    align-items: center;
    position: relative;
}

.filter-btn {
    background: white;
    border: 1px solid #d1d5db;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.filter-btn:hover {
    background: #f3f4f6;
    border-color: #47B9AE;
}

.filter-btn.active {
    background: #0C4777;
    color: white;
    border-color: #0C4777;
}

.filter-main-dropdown {
    position: absolute;
    top: 100%;
    margin-top: 4px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    min-width: 180px;
    z-index: 100;
    display: none;
}

.filter-main-dropdown.show {
    display: block;
}

.filter-category-item {
    padding: 12px 16px;
    cursor: pointer;
    transition: background 0.15s;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 500;
}

.filter-category-item:hover {
    background: #f3f4f6;
}

.filter-category-item:first-child {
    border-radius: 8px 8px 0 0;
}

.filter-category-item:last-child {
    border-radius: 0 0 8px 8px;
}

.filter-sub-dropdown {
    position: absolute;
    top: 0;
    right: 100%;
    margin-right: 4px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    max-width: 300px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 101;
    display: none;
}

.filter-sub-dropdown.show {
    display: block;
}

.filter-dropdown-item {
    padding: 10px 16px;
    cursor: pointer;
    transition: background 0.15s;
    font-size: 14px;
}

.filter-dropdown-item:hover {
    background: #f3f4f6;
}

.filter-dropdown-item.active {
    background: #DBEAFE;
    color: #1E40AF;
    font-weight: 600;
}

.karyawan-item {
    padding: 12px 16px;
    cursor: pointer;
    transition: background 0.15s;
    border-bottom: 1px solid #f3f4f6;
}

.karyawan-item:last-of-type {
    border-bottom: none;
}

.karyawan-item:hover {
    background: #f3f4f6;
}

.karyawan-item.active {
    background: #DBEAFE;
}

.karyawan-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 2px;
}

.karyawan-detail {
    font-size: 12px;
    color: #6B7280;
}

.filter-clear {
    padding: 10px 16px;
    border-top: 1px solid #e5e7eb;
    cursor: pointer;
    color: #EF4444;
    font-weight: 600;
    font-size: 14px;
    text-align: center;
}

.filter-clear:hover {
    background: #FEE2E2;
}

.active-filter-badge {
    background: #0C4777;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 4px;
}

/* Date range filter specific styles */
.date-range-filter {
    padding: 16px;
    min-width: 280px;
}

.date-input-group {
    margin-bottom: 12px;
}

.date-input-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.date-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.date-input:focus {
    outline: none;
    border-color: #47B9AE;
    box-shadow: 0 0 0 3px rgba(71, 185, 174, 0.1);
}

.date-filter-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.date-filter-btn {
    flex: 1;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.date-filter-btn-apply {
    background: #0C4777;
    color: white;
}

.date-filter-btn-apply:hover {
    background: #F59E0B;
}

.date-filter-btn-clear {
    background: #F3F4F6;
    color: #6B7280;
}

.date-filter-btn-clear:hover {
    background: #E5E7EB;
}
</style>
