@extends('layouts.guest')
@section('title', 'Link Tidak Valid - Buku Tamu Digital')
@push('styles')
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(#0C4777 17.8%, #47B9AE 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
    </style>
@endpush
@section('content')
    <x-auth-background />

    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md text-center">
            <div class="mb-6">
                @svg('heroicon-o-x-circle', 'w-20 h-20 text-red-500 mx-auto')
            </div>
            
            <h1 class="text-2xl font-extrabold text-gray-900 mb-4">
                Link Tidak Valid
            </h1>
            
            <p class="text-gray-600 mb-8">
                {{ $message }}
            </p>

            <x-button 
                :href="route('resepsionis.login')" 
                variant="primary" 
                icon="heroicon-o-arrow-left"
                class="py-3 px-6 shadow-lg hover:shadow-xl"
            >
                Kembali ke Login
            </x-button>
        </div>
    </div>
@endsection
