@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconClass' => 'w-5 h-5',
    'loading' => false,
    'loadingId' => null
])

@php
    $baseClasses = 'flex items-center gap-2 justify-center font-semibold border-none cursor-pointer transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-[#0C4777] hover:bg-[#F59E0B] text-white px-5 py-2.5 rounded-lg',
        'cancel' => 'bg-gray-400 hover:bg-gray-500 text-white px-5 py-2.5 rounded-lg',
        'export' => 'bg-[#059669] hover:bg-[#047857] text-white px-5 py-2.5 rounded-lg',
        'export-pdf' => 'bg-[#DC2626] hover:bg-[#B91C1C] text-white px-5 py-2.5 rounded-lg',
        'success' => 'bg-[#10B981] hover:bg-[#059669] text-white px-4 py-2 rounded-md text-sm',
        'danger' => 'bg-[#EF4444] hover:bg-[#DC2626] text-white px-4 py-2 rounded-md text-sm',
        'view' => 'bg-[#F59E0B] hover:bg-[#D97706] text-white px-4 py-2 rounded-md text-sm no-underline inline-block',
    ];
    
    // Default icons untuk setiap variant
    $defaultIcons = [
        'primary' => 'heroicon-o-plus',
        'cancel' => 'heroicon-o-x-mark',
        'export' => 'heroicon-o-arrow-down-tray',
        'export-pdf' => 'heroicon-o-document-text',
        'success' => 'heroicon-o-check-circle',
        'danger' => 'heroicon-o-x-circle',
        'view' => 'heroicon-c-eye',
    ];
    
    // Gunakan icon dari prop, atau fallback ke default icon
    $displayIcon = $icon ?? ($defaultIcons[$variant] ?? null);
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
    
    // Merge additional classes from attributes
    if (isset($attributes['class'])) {
        $classes .= ' ' . $attributes['class'];
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($displayIcon)
            @svg($displayIcon, $iconClass)
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($displayIcon && !$loading)
            @svg($displayIcon, $iconClass)
        @endif
        
        @if($loading && $loadingId)
            <span id="{{ $loadingId }}_text">{{ $slot }}</span>
            <svg id="{{ $loadingId }}_spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
