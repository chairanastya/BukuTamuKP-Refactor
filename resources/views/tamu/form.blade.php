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
            padding: 110px 1rem 80px 1rem;
            margin: 0 auto;
            max-width: 100%;
            box-sizing: border-box;
        }

        .form-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Mobile */
        @media (max-width: 767px) {
            .container {
                padding-top: 120px;
            }
        }

        /* iPad dan Tablet - padding lebih besar */
        @media (min-width: 768px) and (max-width: 1024px) {
            .container {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }

            .form-container {
                padding: 0 1.5rem;
            }
        }

        /* Desktop */
        @media (min-width: 1025px) {
            .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
    </style>
@endpush

@section('content')
    @include('partials.kunjungan-form')
@endsection

@include('partials.kunjungan-form-scripts')