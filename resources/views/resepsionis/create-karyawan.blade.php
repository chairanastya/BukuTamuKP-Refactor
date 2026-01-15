@extends('layouts.app')
@section('title', 'Buat Karyawan Baru - Buku Tamu Digital')

@section('header')
Buku Tamu Digital
@endsection

@section('header-action')
<div class="relative">
    <button onclick="toggleDropdown()" class="flex items-center gap-2">
        <span>{{ Auth::user()->nama_resepsionis }}</span>
        @svg('uiw-down', 'w-5 h-5')
    </button>
    <div id="dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <form method="POST" action="{{ route('resepsionis.logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700">
                Log Out
            </button>
        </form>
    </div>
</div>
@endsection

@section('sidebar')
    <a href="{{ route('resepsionis.dashboard') }}#beranda" class="sidebar-item">
        @svg('fluentui-home-24', 'w-8 h-8')
        <span>Beranda</span>
    </a>
    <a href="{{ route('resepsionis.dashboard') }}#riwayat" class="sidebar-item">
        @svg('gmdi-history', 'w-8 h-8')
        <span>Riwayat</span>
    </a>
    <a href="{{ route('resepsionis.dashboard') }}#karyawan" class="sidebar-item">
        @svg('gmdi-people-r', 'w-8 h-8')
        <span>Daftar Karyawan</span>
    </a>
@endsection

