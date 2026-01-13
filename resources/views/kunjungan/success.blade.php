@extends('layouts.guest')
@section('title', 'Konfirmasi Berhasil')
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            @if($type === 'terima')
                <!-- Success Icon - Terima -->
                <div class="mb-6">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-green-700 mb-4">
                    Kunjungan Diterima
                </h1>
                
                <p class="text-gray-700 text-lg mb-8">
                    {{ $message }}
                </p>
                
                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded mb-8 text-left">
                    <h3 class="font-bold text-green-800 mb-3">Informasi Tamu:</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                        <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                        <p><strong>Instansi:</strong> {{ $kunjungan->tamu->instansi_tamu ?? '-' }}</p>
                        <p><strong>Keperluan:</strong> {{ $kunjungan->tujuan_kunjungan }}</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600">
                    Tamu sedang menunggu di area resepsionis. Silakan jemput tamu Anda.
                </p>
                
            @else
                <!-- Success Icon - Tolak -->
                <div class="mb-6">
                    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-red-700 mb-4">
                    Kunjungan Ditolak
                </h1>
                
                <p class="text-gray-700 text-lg mb-8">
                    {{ $message }}
                </p>
                
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded mb-8 text-left">
                    <h3 class="font-bold text-red-800 mb-3">Informasi Tamu:</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                        <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                        <p><strong>Keperluan:</strong> {{ $kunjungan->tujuan_kunjungan }}</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600">
                    Tamu akan diberitahu bahwa Anda tidak dapat menerima kunjungan saat ini.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
