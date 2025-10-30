@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Soal dari Excel/CSV</h1>
        <a href="{{ route('banksoal.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Bank Soal
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- ========================================================= --}}
            {{-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN ERROR VALIDASI --}}
            {{-- ========================================================= --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Menampilkan notifikasi error dari dalam file Excel --}}
            @if (session('import_errors'))
                <div class="alert alert-danger">
                    <strong>Terjadi beberapa kesalahan saat impor:</strong>
                    <ul>
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info">
                <strong>Petunjuk:</strong><br>
                <ul>
                    <li>Unduh template yang disediakan untuk memastikan format file sudah benar.</li>
                    <li>File yang diunggah bisa berformat <strong>.xlsx</strong> atau <strong>.csv</strong>.</li>
                    <li>Pastikan nama header kolom adalah: <strong>soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban</strong>.</li>
                </ul>
                <a href="{{ asset('template/template-soal.xlsx') }}" class="btn btn-success btn-sm mt-2" download>
                    <i class="fas fa-download"></i> Unduh Template Excel
                </a>
            </div>

            <form action="{{ route('banksoal.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_mapel">Pilih Mata Pelajaran *</label>
                            <select name="id_mapel" class="form-control" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach ($mapelList as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelas_id">Pilih Kelas *</label>
                            <select name="kelas_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ $kelas->kelas }} - {{ $kelas->jurusan->nama ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="file">Pilih File (.xlsx atau .csv) *</label>
                            <input type="file" name="file" class="form-control-file" required accept=".xlsx,.csv">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-upload"></i> Mulai Proses Import
                </button>
            </form>
        </div>
    </div>
@endsection