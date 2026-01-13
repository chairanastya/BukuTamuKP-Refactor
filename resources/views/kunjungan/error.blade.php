@extends('layouts.guest')
@section('title', 'Error Konfirmasi')
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <!-- Error Icon -->
            <div class="mb-6">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-orange-700 mb-4">
                Oops! Ada Masalah
            </h1>
            
            <p class="text-gray-700 text-lg mb-8">
                {{ $message }}
            </p>
            
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
        </div>
    </div>
</div>
@endsection
