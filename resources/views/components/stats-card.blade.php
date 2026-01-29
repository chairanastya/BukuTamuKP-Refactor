@props([
    'title' => 'Stat Title',
    'value' => '0',
    'icon' => 'far-clock',
    'iconColor' => 'text-gray-600',
    'valueColor' => 'text-[#084E8F]',
    'bgColor' => '#E5E7EB',
    'filter' => 'all',
    'onclick' => null,
])

<style>
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s;
    }

    .stats-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .stats-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .stats-card > div:first-child {
            width: 100%;
        }

        .stats-card .text-3xl {
            font-size: 1.875rem;
        }

        .stats-card .text-sm {
            font-size: 0.75rem;
        }
    }
</style>

<div class="stats-card cursor-pointer hover:shadow-lg transition-shadow" 
     data-filter="{{ $filter }}"
     @if($onclick) onclick="{{ $onclick }}" @endif
     {{ $attributes }}>
    <div>
        <p class="text-gray-600 text-sm mb-1">{{ $title }}</p>
        <p class="stats-value text-3xl font-bold {{ $valueColor }}">{{ $value }}</p>
    </div>
    <div class="stats-icon" style="background: {{ $bgColor }};">
        @svg($icon, 'w-6 h-6 ' . $iconColor)
    </div>
</div>