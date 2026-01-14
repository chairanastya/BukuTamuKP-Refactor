@extends('layouts.guest')
@section('title', 'Notulensi Berhasil Disimpan')
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <!-- Success Icon -->
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                Notulensi Berhasil Disimpan!
            </h1>

            <p class="text-gray-600 mb-6">
                {{ $message }}
            </p>

            <!-- Info Card -->
            <div class="bg-blue-50 border-l-4 border-[#084E8F] p-6 rounded-lg mb-8 text-left">
                <h3 class="font-bold text-[#084E8F] mb-3">Informasi Tamu:</h3>
                <div class="space-y-2 text-gray-700">
                    <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                    <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                    <p><strong>Instansi:</strong> {{ $kunjungan->tamu->instansi_tamu ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-8">
                <p class="text-green-800 text-sm">
                    <strong>Notifikasi:</strong> Email berisi link untuk melihat notulensi telah dikirim ke tamu.
                </p>
            </div>

            <a href="{{ url('/') }}" class="inline-block bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Clear saved images from localStorage when reaching success page
    window.addEventListener('DOMContentLoaded', function() {
        // Get token from URL or pass it from controller
        const urlParams = new URLSearchParams(window.location.search);
        const currentUrl = window.location.pathname;
        
        // Try to extract token from referrer or clear all notulensi storage
        try {
            // Clear all notulensi_images entries from localStorage
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('notulensi_images_')) {
                    localStorage.removeItem(key);
                    console.log('Cleared:', key);
                }
            });
            
            // Clear session storage flag
            Object.keys(sessionStorage).forEach(key => {
                if (key.startsWith('form_submitted_')) {
                    sessionStorage.removeItem(key);
                }
            });
        } catch (e) {
            console.error('Error clearing storage:', e);
        }
    });
</script>
@endpush

@endsection
