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

@section('content')
    @include('partials.kunjungan-form')
@endsection

@include('partials.kunjungan-form-scripts')
