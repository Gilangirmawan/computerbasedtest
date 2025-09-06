{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Ujian Tersedia</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first('token') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Ujian</th>
                <th>Mata Pelajaran</th>
                <th>Token</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ujian as $i => $ujian)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $ujian->nama_ujian }}</td>
                <td>{{ $ujian->mapel->nama ?? '-' }}</td>
                <td>
                    <form method="POST" action="{{ route('ikutujian.cekToken') }}">
                        @csrf
                        <input type="hidden" name="id_ujian" value="{{ $ujian->id }}">
                        <input type="text" name="token" class="form-control" placeholder="Masukkan Token" required>
                </td>
                <td>
                        <button type="submit" class="btn btn-sm btn-primary">
                            Ikut Ujian
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada ujian tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Ujian Tersedia</h2>

    {{-- Notifikasi standar (opsional, bisa dihapus jika hanya ingin SweetAlert) --}}
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Catatan / Petunjuk --}}
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Petunjuk Pengerjaan</h5>
        <ul>
            <li>Baca petunjuk dengan seksama.</li>
            <li>Bacalah Doa Terlebih Dahulu Sebelum Memulai Ujian.</li>
            <li>Pastikan koneksi internet stabil sebelum mulai ujian.</li>
            <li>Bacalah soal dengan teliti sebelum menjawab.</li>
            <li>Setiap soal wajib dijawab sebelum waktu habis.</li>
        </ul>
    </div>

    {{-- Daftar ujian --}}
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr class="text-center">
                <th>Nama Ujian</th>
                <th>Mata Pelajaran</th>
                <th>Durasi</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Token</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ujian as $ujian)
            <tr>
                <td>{{ $ujian->nama_ujian }}</td>
                <td>{{ $ujian->mapel->nama ?? '-' }}</td>
                <td class="text-center">{{ $ujian->waktu }} menit</td>
                <td>{{ $ujian->waktu_mulai }}</td>
                <td>{{ $ujian->waktu_selesai }}</td>
                <td>
                    <form method="POST" action="{{ route('ikutujian.cekToken') }}">
                        @csrf
                        <input type="hidden" name="id_ujian" value="{{ $ujian->id }}">
                        <input type="text" name="token" class="form-control" placeholder="Masukkan Token" required>
                </td>
                <td>
                        <button type="submit" class="btn btn-sm btn-primary">
                            Ikut Ujian
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada ujian tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection