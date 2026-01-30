<div class="date-range-filter" onclick="event.stopPropagation()">
    <div class="date-input-group">
        <label class="date-input-label">Dari Tanggal:</label>
        <input type="date" id="{{ $startInputId ?? 'dateFilterStart' }}" class="date-input"
            value="{{ $startDate ?? '' }}">
    </div>
    <div class="date-input-group">
        <label class="date-input-label">Sampai Tanggal:</label>
        <input type="date" id="{{ $endInputId ?? 'dateFilterEnd' }}" class="date-input" value="{{ $endDate ?? '' }}">
    </div>
    <div class="date-filter-actions">
        <button class="date-filter-btn date-filter-btn-apply" onclick="{{ $applyFunction ?? 'applyDateFilter()' }}">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Terapkan
        </button>
        <button class="date-filter-btn date-filter-btn-clear" onclick="{{ $clearFunction ?? 'clearDateFilter()' }}">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Hapus
        </button>
    </div>
</div>