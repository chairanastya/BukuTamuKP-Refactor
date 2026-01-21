@props([
    'nama' => '',
    'departemen' => '',
    'jabatan' => '',
    'onclick' => '',
])

@php
    $detail = trim(($departemen ? $departemen : '') . ($departemen && $jabatan ? ' - ' : '') . ($jabatan ? $jabatan : ''));
@endphp

<div 
    class="karyawan-card {{ $attributes->get('class') }}" 
    @if($onclick) onclick="{{ $onclick }}" @endif
    {{ $attributes->except(['class']) }}
>
    <div class="karyawan-card-info">
        <div class="karyawan-card-name">{{ $nama }}</div>
        @if($detail)
            <div class="karyawan-card-detail">{{ $detail }}</div>
        @endif
    </div>
</div>

<style>
.karyawan-card {
    display: flex;
    align-items: center;
    padding: 0 12px;
    background-color: white;
    border: 2px solid #084E8F;
    border-radius: 8px;
    width: 100%;
    height: 50px;
    box-sizing: border-box;
    cursor: pointer;
    transition: all 0.2s;
    max-width: 100%;
}

.karyawan-card:hover {
    background-color: #f0f9ff;
    border-color: #0C4777;
}

.karyawan-card-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 4px 0;
    gap: 2px;
    min-width: 0;
    overflow: hidden;
}

.karyawan-card-name {
    color: #084E8F;
    font-weight: 600;
    font-size: 15px;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.karyawan-card-detail {
    color: #6b7280;
    font-size: 13px;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
