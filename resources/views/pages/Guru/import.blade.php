@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Data Guru</h1>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Data Guru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
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
                    <li>Unduh template untuk memastikan format file sudah benar.</li>
                    <li>File yang diunggah bisa berformat <strong>.xlsx</strong> atau <strong>.csv</strong>.</li>
                    <li>Pastikan nama header kolom adalah: <strong>nip, nama</strong>.</li>
                    <li>Sistem akan secara otomatis membuat akun login untuk setiap guru dengan <strong>username</strong> dan <strong>password</strong> default yang sama dengan <strong>NIP</strong>.</li>
                </ul>
                <a href="{{ asset('template/template-guru.xlsx') }}" class="btn btn-success btn-sm mt-2" download>
                    <i class="fas fa-download"></i> Unduh Template Excel
                </a>
            </div>

            <form action="{{ route('guru.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">Pilih File (.xlsx atau .csv) *</label>
                    <input type="file" name="file" class="form-control-file" required accept=".xlsx,.csv">
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-upload"></i> Mulai Proses Import
                </button>
            </form>
        </div>
    </div>
@endsection