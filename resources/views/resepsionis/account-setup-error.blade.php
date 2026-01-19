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

        .bg-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
        }

        .circle-1 {
            width: 275px;
            height: 275px;
            background: linear-gradient(180deg, rgba(255, 227, 102, 0.00) 0%, rgba(255, 227, 102, 0.70) 100%);
            -webkit-mask: conic-gradient(from 90deg, transparent 0deg 45deg, black 45deg 360deg);
            mask: conic-gradient(from 90deg, transparent 0deg 45deg, black 45deg 360deg);
            border-radius: 50%;
            bottom: -40px;
            left: -80px;
        }

        .circle-2 {
            width: 450px;
            height: 450px;
            background: linear-gradient(180deg, rgba(247, 178, 24, 0.00) 0%, rgba(247, 178, 24, 0.70) 100%);
            top: 2px;
            right: 100px;
        }
        
        .donut {
            position: absolute;
            border-radius: 50%;
            -webkit-mask: radial-gradient(transparent 0, transparent 110px, black 110px);
            mask: radial-gradient(transparent 0, transparent 110px, black 110px);
        }

        .donut-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(-50deg, rgba(255, 227, 102, 0.70) 0%, rgba(95, 129, 161, 0.70) 52.4%, rgba(71, 185, 174, 0.70) 100%);
            top: -5%;
            left: 15%;
        }

        .donut-2 {
            width: 275px;
            height: 275px;
            background: linear-gradient(75deg, rgba(247, 178, 24, 0.70) 0%, rgba(145, 104, 14, 0.70) 100%);
            bottom: -15%;
            right: -5%;
        }

        .donut-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(-45deg, rgba(255, 227, 102, 0.38) 0%, rgba(95, 129, 161, 0.38) 52.4%, rgba(71, 185, 174, 0.38) 100%);
            -webkit-mask: radial-gradient(transparent 0, transparent 60px, black 60px); 
            mask: radial-gradient(transparent 0, transparent 60px, black 60px); 
            bottom: 1%;
            right: 15%;
        }

        .dots-pattern {
            position: absolute;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 40px;
            opacity: 0.2;
        }

        .dots-pattern-top {
            top: 20%;
            left: 2.5%;
        }

        .dots-pattern-bottom {
            bottom: 5%;
            left: 15%;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }

        .arrows {
            position: absolute;
            right: 5%;
            top: 40%;
            opacity: 0.15;
        }

        .arrow {
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-right: 40px solid white;
            margin: 10px;
        }
    </style>
@endpush
@section('content')
    <div class="bg-pattern">
        <div class="donut donut-3"></div>
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="donut donut-1"></div>
        <div class="donut donut-2"></div>

        <div class="dots-pattern dots-pattern-top">
            @for ($i = 0; $i < 40; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <div class="dots-pattern dots-pattern-bottom">
            @for ($i = 0; $i < 16; $i++)
                <div class="dot"></div>
            @endfor
        </div>

        <div class="arrows inline-flex">
            <div class="arrow"></div>
            <div class="arrow"></div>
            <div class="arrow"></div>
            <div class="arrow"></div>
        </div>
    </div>

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

            <a href="{{ route('resepsionis.login') }}"
                class="inline-flex items-center justify-center bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                @svg('heroicon-o-arrow-left', 'w-5 h-5 mr-2')
                Kembali ke Login
            </a>
        </div>
    </div>
@endsection
