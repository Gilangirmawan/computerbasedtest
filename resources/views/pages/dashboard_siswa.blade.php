@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Siswa</h1>
    </div>

    <div class="row">

        <!-- Card 1: Profil Siswa -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Profil Siswa</div>
                            @if ($profilSiswa)
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profilSiswa->nama }}</div>
                                <hr class="mt-2 mb-2">
                                <div class="text-gray-600 small">
                                    <strong>NIS:</strong> {{ $profilSiswa->nis }} <br>
                                    {{-- ====================================================== --}}
                                    {{-- PERBAIKAN DI SINI: Mengubah ->nama menjadi ->nama_kelas --}}
                                    {{-- ====================================================== --}}
                                    <strong>Kelas:</strong> {{ $profilSiswa->kelas->kelas ?? 'N/A' }} <br>
                                    <strong>Jurusan:</strong> {{ $profilSiswa->jurusan->nama ?? 'N/A' }}
                                </div>
                            @else
                                <div class="text-danger">Profil siswa tidak ditemukan.</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Daftar Ujian Tersedia (Kode tidak berubah) -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ujian Tersedia ({{ $ujianTersedia->count() }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($ujianTersedia->isEmpty())
                                    <p>Saat ini belum ada ujian yang tersedia untuk Anda.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($ujianTersedia->take(3) as $ujian)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    {{ $ujian->nama_ujian }} - ({{ $ujian->mapel->nama }})
                                                    <br>
                                                    @if(isset($ujianSelesaiIds) && in_array($ujian->id, $ujianSelesaiIds))
                                                        <span class="badge bg-success">Selesai Dikerjakan</span>
                                                    @else
                                                        <span class="badge bg-secondary">Belum Dikerjakan</span>
                                                    @endif
                                                </div>
                                                <span class="badge bg-primary rounded-pill text-white">{{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('d M Y') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            @if($ujianTersedia->count() > 0)
                                <a href="{{ route('ikutujian.daftar') }}" class="btn btn-success btn-sm mt-3">
                                    Lihat Semua Ujian <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
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