@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Data Mata Pelajaran</h2>

    <form action="{{ route('mapel.tambah') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nama" class="form-label">Mata Pelajaran</label>
            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
