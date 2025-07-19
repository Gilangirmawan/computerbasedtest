@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Tambah Data Kelas</h2>

    <form action="{{ route('kelas.tambah') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" name="kelas" id="kelas" class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas') }}">
            @error('kelas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="jurusan_id">Pilih Jurusan</label>
            <select name="jurusan_id" name="jurusan_id" id="jurusan_id" class="form-control" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach($jurusanList as $jurusan)
                    <option value="{{ $jurusan->kode_jurusan }}">{{ $jurusan->nama }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
