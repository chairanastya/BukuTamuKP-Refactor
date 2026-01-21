@props([
    'type' => 'default',
    'size' => 'normal'
])
@php
    $classes = [
        'pending' => 'bg-[#FEF9C2] text-[#D08700]',
        'accepted' => 'bg-[#DBEAFE] text-[#193CB8]',
        'done' => 'bg-[#DCFCE7] text-[#008236]',
        'canceled' => 'bg-[#FFE2E2] text-[#C10007]',
        'resepsionis' => 'bg-[#DBEAFE] text-[#193CB8]',
        'karyawan' => 'bg-[#E5E7EB] text-[#374151]',
        'aktif' => 'bg-[#D1FAE5] text-[#059669]',
        'nonaktif' => 'bg-[#FEE2E2] text-[#DC2626]',
        'default' => 'bg-gray-200 text-gray-700',
    ];

    $labels = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'done' => 'Done',
        'canceled' => 'Canceled',
        'resepsionis' => 'Resepsionis',
        'karyawan' => 'Karyawan',
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
        'default' => 'N/A',
    ];

    $sizeClasses = [
        'small' => 'px-2 py-1 text-xs',
        'normal' => 'px-3 py-1.5 text-xs',
        'large' => 'px-4 py-2 text-sm',
    ];

    $class = $classes[$type] ?? $classes['default'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['normal'];
    $defaultLabel = $labels[$type] ?? $labels['default'];
@endphp

<span {{ $attributes->merge(['class' => "badge {$class} {$sizeClass} rounded-lg font-semibold inline-block"]) }}>
    {{ $slot->isEmpty() ? $defaultLabel : $slot }}
</span>