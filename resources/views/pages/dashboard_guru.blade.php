@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        {{-- Menggunakan data dari $profilGuru untuk sapaan --}}
        <h1 class="h3 mb-0 text-gray-800">Dashboard - Selamat Datang, {{ $profilGuru->nama ?? Auth::user()->name }}!</h1>
    </div>

    <div class="row">

        <!-- Card 1: Profil Guru -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Profil Guru</div>
                            
                            {{-- ====================================================== --}}
                            {{-- PERBAIKAN DI SINI: Memastikan pemanggilan nama benar --}}
                            {{-- ====================================================== --}}
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $profilGuru->nama ?? Auth::user()->name }}
                            </div>
                            
                            @if ($profilGuru)
                                <hr class="mt-2 mb-2">
                                <div class="text-gray-600 small">
                                    <strong>NIP:</strong> {{ $profilGuru->nip ?? 'N/A' }} <br>
                                    <strong>Username:</strong> {{ Auth::user()->username }}
                                </div>
                            @else
                                <div class="text-danger mt-2">Profil detail guru tidak ditemukan.</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Data Bank Soal -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bank Soal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $jumlahSoal }} Soal Dibuat
                            </div>
                            <a href="{{ route('banksoal.index') }}" class="btn btn-info btn-sm mt-3">
                                Kelola Bank Soal <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Ujian Dibuat -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ujian Dibuat (Terbaru)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($ujianDibuat->isEmpty())
                                    <p class="mb-0 small">Anda belum membuat ujian.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($ujianDibuat as $ujian)
                                            <li class="list-group-item px-0 py-1">
                                                <small>{{ $ujian->nama_ujian }} - ({{ $ujian->mapel->nama }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                             <a href="{{ route('ujian.index') }}" class="btn btn-success btn-sm mt-3">
                                Kelola Ujian <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection