@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mt-5">
        <h2>Selamat Datang di Sistem CBT</h2>
        <p class="lead">Silakan klik tombol di bawah untuk melihat daftar ujian yang tersedia.</p>
        <a href="{{ route('ikutujian.daftar') }}" class="btn btn-lg btn-primary mt-3">
            Lihat Daftar Ujian
        </a>
    </div>
</div>
@endsection