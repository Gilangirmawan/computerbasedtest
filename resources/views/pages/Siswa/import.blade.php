@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Data Siswa</h1>
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Data Siswa
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
                    <li>Pastikan semua kolom di template terisi dengan benar.</li>
                </ul>
                <a href="{{ asset('template/template-siswa.xlsx') }}" class="btn btn-success btn-sm mt-2" download>
                    <i class="fas fa-download"></i> Unduh Template Excel
                </a>
            </div>

            <form action="{{ route('siswa.import.store') }}" method="POST" enctype="multipart/form-data">
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