@props([
    'nama',
    'departemen' => '',
    'jabatan' => '',
    'active' => false,
])
@php
    $details = [];
    if (!empty($departemen))
        $details[] = $departemen;
    if (!empty($jabatan))
        $details[] = $jabatan;
    $detailText = !empty($details) ? implode(' - ', $details) : '';

    $classes = 'karyawan-item';
    if ($active)
        $classes .= ' active';
@endphp

<div class="{{ $classes }}" {{ $attributes }}>
    <div class="karyawan-name">{{ $nama }}</div>
    @if($detailText)
        <div class="karyawan-detail">{{ $detailText }}</div>
    @endif
</div>

<style>
    .karyawan-item {
        padding: 10px 16px;
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
</style>
