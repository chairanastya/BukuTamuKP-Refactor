@extends('layouts.guest')
@section('title', 'Form Tamu - Buku Tamu Digital')
@section('header')
    Buku Tamu Digital
@endsection
@section('header-action')
    <a href="{{ route('resepsionis.login') }}" class="">
        Login
    </a>
@endsection

@include('partials.kunjungan-form-styles')

@push('styles')
    <style>
        body {
            overflow-x: hidden;
        }

        .container {
            padding-top: 110px;
            padding-bottom: 80px;
            margin: 0 auto;
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
                padding-top: 120px;
            }
        }
    </style>
@endpush

@section('content')
    @include('partials.kunjungan-form')
@endsection

@include('partials.kunjungan-form-scripts')