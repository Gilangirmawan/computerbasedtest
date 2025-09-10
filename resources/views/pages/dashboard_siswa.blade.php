@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        {{-- Menggunakan data dari $profilSiswa untuk sapaan --}}
        <h1 class="h3 mb-0 text-gray-800">Dashboard - Selamat Datang, {{ $profilSiswa->nama ?? Auth::user()->name }}!</h1>
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

        <!-- Card 2: Daftar Ujian Tersedia -->
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

    {{-- BLOK BARU UNTUK MENAMPILKAN TABEL NILAI --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat dan Nilai Ujian</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th>Nama Ujian</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Jumlah Benar</th>
                                    <th>Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatUjian as $riwayat)
                                    <tr class="text-center">
                                        <td>{{ $riwayat->ujian->nama_ujian ?? 'Ujian Telah Dihapus' }}</td>
                                        <td>{{ $riwayat->ujian->mapel->nama ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($riwayat->tgl_selesai)->format('d M Y, H:i') }}</td>
                                        <td class="text-center">{{ $riwayat->jml_benar }}</td>
                                        <td class="text-center">
                                            <span class="badge 
                                                @if($riwayat->nilai >= 80) bg-success 
                                                @elseif($riwayat->nilai >= 60) bg-warning 
                                                @else bg-danger @endif">
                                                {{ number_format($riwayat->nilai, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Anda belum memiliki riwayat ujian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection