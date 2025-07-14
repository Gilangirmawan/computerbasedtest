@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Data Jurusan</h2>

    <form action="{{ route('jurusan.tambah') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
            <input type="text" name="kode_jurusan" id="kode_jurusan" class="form-control @error('kode_jurusan') is-invalid @enderror" value="{{ old('kode_jurusan') }}">
            @error('kode_jurusan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="nis" class="form-label">Nama Jurusan</label>
            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('jurusan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
