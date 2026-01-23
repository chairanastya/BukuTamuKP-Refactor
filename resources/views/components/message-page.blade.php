@props([
    'type' => 'success',
    'title' => '',
    'message' => '',
    'buttonText' => 'Kembali ke Beranda',
    'buttonUrl' => null,
    'showButton' => true,
    'additionalInfo' => null,
    'kunjungan' => null,
    'pageTitle' => 'Notifikasi'
])

@php
    $buttonUrl = $buttonUrl ?? route('resepsionis.dashboard');
    
    $typeConfig = [
        'success' => [
            'bgColor' => 'bg-green-100',
            'iconColor' => 'text-green-600',
            'titleColor' => 'text-green-700',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'borderColor' => 'border-green-500'
        ],
        'error' => [
            'bgColor' => 'bg-red-100',
            'iconColor' => 'text-red-600',
            'titleColor' => 'text-red-700',
            'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'borderColor' => 'border-red-500'
        ],
        'warning' => [
            'bgColor' => 'bg-orange-100',
            'iconColor' => 'text-orange-600',
            'titleColor' => 'text-orange-700',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'borderColor' => 'border-orange-500'
        ],
        'reject' => [
            'bgColor' => 'bg-red-100',
            'iconColor' => 'text-red-600',
            'titleColor' => 'text-red-700',
            'icon' => 'M6 18L18 6M6 6l12 12',
            'borderColor' => 'border-red-500'
        ],
        'accept' => [
            'bgColor' => 'bg-green-100',
            'iconColor' => 'text-green-600',
            'titleColor' => 'text-green-700',
            'icon' => 'M5 13l4 4L19 7',
            'borderColor' => 'border-green-500'
        ]
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['success'];
@endphp

@extends('layouts.guest')
@section('title', $pageTitle)
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="w-24 h-24 {{ $config['bgColor'] }} rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-12 h-12 {{ $config['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold {{ $config['titleColor'] }} mb-4">
                {{ $title }}
            </h1>

            <p class="text-gray-700 text-lg mb-8">
                {{ $message }}
            </p>

            @if($kunjungan)
                <div class="bg-{{ $type === 'error' || $type === 'warning' ? 'orange' : ($type === 'reject' ? 'red' : ($type === 'success' ? 'blue' : 'green')) }}-50 border-l-4 {{ $config['borderColor'] }} p-6 rounded mb-8 text-left">
                    <h3 class="font-bold {{ $type === 'success' ? 'text-[#084E8F]' : $config['iconColor'] }} mb-3">Informasi Tamu:</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                        <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                        @if(isset($kunjungan->tamu->instansi_tamu))
                            <p><strong>Instansi:</strong> {{ $kunjungan->tamu->instansi_tamu ?? '-' }}</p>
                        @endif
                        @if(isset($kunjungan->tujuan_kunjungan))
                            <p><strong>Keperluan:</strong> {{ $kunjungan->tujuan_kunjungan }}</p>
                        @endif
                    </div>
                </div>

                @if($type === 'success')
                    <div>
                        <p><strong>Notifikasi:</strong> Email berisi link untuk melihat notulensi telah dikirim ke tamu.</p>
                    </div>
                @endif
            @endif

            @if($additionalInfo)
                {!! $additionalInfo !!}
            @endif

            {{ $slot }}

            @if($type === 'warning')
                <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded mb-8 text-left">
                    <h3 class="font-bold text-orange-800 mb-2">Kemungkinan Penyebab:</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>Link konfirmasi sudah kadaluarsa (berlaku 24 jam)</li>
                        <li>Kunjungan sudah dikonfirmasi sebelumnya</li>
                        <li>Link tidak valid atau sudah digunakan</li>
                    </ul>
                </div>

                <p class="text-sm text-gray-600">
                    Jika Anda merasa ini adalah kesalahan, silakan hubungi resepsionis atau admin sistem.
                </p>
            @endif

            @if($type === 'accept')
                <p class="text-sm text-gray-600">
                    Tamu sedang menunggu di area resepsionis. Silakan jemput tamu Anda.
                </p>
            @endif

            @if($type === 'reject')
                <p class="text-sm text-gray-600">
                    Tamu akan diberitahu bahwa Anda tidak dapat menerima kunjungan saat ini.
                </p>
            @endif

            @if($showButton)
                <a href="{{ $buttonUrl }}" class="inline-block bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg hover:shadow-xl mt-4">
                    {{ $buttonText }}
                </a>
            @endif
        </div>
    </div>
</div>
@endsection