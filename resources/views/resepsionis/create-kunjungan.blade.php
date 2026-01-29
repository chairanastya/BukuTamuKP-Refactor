@extends('layouts.app')
@section('title', 'Buat Kunjungan Baru - Buku Tamu Digital')

@section('header')
    Buku Tamu Digital
@endsection

@section('header-action')
    <x-user-dropdown :userName="Auth::user()->nama_resepsionis" :logoutRoute="route('resepsionis.logout')" />
@endsection

@section('sidebar')
    @include('partials.resepsionis-sidebar')
@endsection

@include('partials.kunjungan-form-styles')

@push('styles')
    <style>
        body {
            overflow-x: hidden;
        }

        .container {
            margin-left: 96px;
            padding: 110px 1rem 80px 1rem;
            width: calc(100% - 96px);
            max-width: 100%;
            box-sizing: border-box;
        }

        .form-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 160px 2rem 80px 2rem;
                width: 100%;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    @include('partials.kunjungan-form')
@endsection

@include('partials.kunjungan-form-scripts')