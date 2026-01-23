<div class="date-range-filter">
    <div class="date-input-group">
        <label class="date-input-label">Dari Tanggal:</label>
        <input type="date" id="{{ $startInputId ?? 'dateFilterStart' }}" class="date-input" value="{{ $startDate ?? '' }}">
    </div>
    <div class="date-input-group">
        <label class="date-input-label">Sampai Tanggal:</label>
        <input type="date" id="{{ $endInputId ?? 'dateFilterEnd' }}" class="date-input" value="{{ $endDate ?? '' }}">
    </div>
    <div class="date-filter-actions">
        <button class="date-filter-btn date-filter-btn-apply" onclick="{{ $applyFunction ?? 'applyDateFilter()' }}">
            Terapkan
        </button>
        <button class="date-filter-btn date-filter-btn-clear" onclick="{{ $clearFunction ?? 'clearDateFilter()' }}">
            Hapus
        </button>
    </div>
</div>

@push('styles')
<style>
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
        margin-bottom: 4px;
    }

    .date-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
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
        padding: 8px 16px;
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
        background: #084E8F;
    }

    .date-filter-btn-clear {
        background: #EF4444;
        color: white;
    }

    .date-filter-btn-clear:hover {
        background: #DC2626;
    }
</style>
@endpush
