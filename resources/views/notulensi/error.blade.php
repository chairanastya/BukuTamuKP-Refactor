@extends('layouts.guest')
@section('title', 'Error')
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <!-- Error Icon -->
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                Oops! Terjadi Kesalahan
            </h1>

            <p class="text-gray-600 mb-8">
                {{ $message }}
            </p>

            <a href="{{ route('resepsionis.dashboard') }}" class="inline-block bg-[#084E8F] hover:bg-[#F7B218] text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
