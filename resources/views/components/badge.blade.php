@props([
    'type' => 'default',
    'size' => 'normal'
])
@php
    // Menggunakan BadgeHelper sebagai single source of truth
    $badgeData = \App\Helpers\BadgeHelper::getBadgeData($type, $size);
    $class = $badgeData['class'];
    $sizeClass = $badgeData['sizeClass'];
    $defaultLabel = $badgeData['label'];
@endphp

<span {{ $attributes->merge(['class' => "badge {$class} {$sizeClass} rounded-lg font-semibold inline-block"]) }}>
    {{ $slot->isEmpty() ? $defaultLabel : $slot }}
</span>