@extends('layouts.guest')
@section('title', 'Konfirmasi Tindakan')
@section('header', 'Buku Tamu Digital')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8">
            @if($action === 'terima')
                <!-- Konfirmasi Terima -->
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        Konfirmasi Terima Kunjungan
                    </h1>
                    <p class="text-gray-600">
                        Apakah Anda yakin akan menerima kunjungan ini?
                    </p>
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg mb-6">
                    <h3 class="font-bold text-green-800 mb-3">Informasi Tamu:</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                        <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                        <p><strong>Instansi:</strong> {{ $kunjungan->tamu->instansi_tamu ?? '-' }}</p>
                        <p><strong>Keperluan:</strong> {{ $kunjungan->tujuan_kunjungan }}</p>
                        <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d F Y') }}, {{ $kunjungan->jam_mulai }} WIB</p>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6">
                    <p class="text-blue-800 text-sm">
                        <strong>Perhatian:</strong> Dengan menerima kunjungan ini, Anda akan diminta untuk menjemput tamu di area resepsionis. Tamu akan mendapat notifikasi via email.
                    </p>
                </div>

                <div class="flex gap-4">
                    <form action="{{ route('kunjungan.process', $kunjungan->token_approval) }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="action" value="terima">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                            Ya, Terima Kunjungan
                        </button>
                    </form>
                    <a href="{{ url('/') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-200 text-center">
                        Batal
                    </a>
                </div>

            @else
                <!-- Konfirmasi Tolak -->
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        Konfirmasi Tolak Kunjungan
                    </h1>
                    <p class="text-gray-600">
                        Apakah Anda yakin akan menolak kunjungan ini?
                    </p>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg mb-6">
                    <h3 class="font-bold text-red-800 mb-3">Informasi Tamu:</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Nama:</strong> {{ $kunjungan->tamu->nama_tamu }}</p>
                        <p><strong>Email:</strong> {{ $kunjungan->tamu->email_tamu }}</p>
                        <p><strong>Instansi:</strong> {{ $kunjungan->tamu->instansi_tamu ?? '-' }}</p>
                        <p><strong>Keperluan:</strong> {{ $kunjungan->tujuan_kunjungan }}</p>
                        <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d F Y') }}, {{ $kunjungan->jam_mulai }} WIB</p>
                    </div>
                </div>

                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded mb-6">
                    <p class="text-orange-800 text-sm">
                        <strong>Perhatian:</strong> Dengan menolak kunjungan ini, tamu akan mendapat notifikasi via email bahwa Anda tidak dapat menerima kunjungan saat ini. Tamu akan diminta untuk menjadwalkan ulang.
                    </p>
                </div>

                <div class="flex gap-4">
                    <form action="{{ route('kunjungan.process', $kunjungan->token_approval) }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="action" value="tolak">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                            Ya, Tolak Kunjungan
                        </button>
                    </form>
                    <a href="{{ url('/') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-200 text-center">
                        Batal
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
