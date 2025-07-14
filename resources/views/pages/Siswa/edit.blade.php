@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Data Siswa</h2>

    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input type="text" name="nis" id="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $siswa->nis) }}">
            @error('nis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $siswa->nama) }}">
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $siswa->username) }}">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <select name="kelas_id">
            @foreach ($kelasList as $kelas)
                <option value="{{ $kelas->id }}" {{ $siswa->kelas_id == $kelas->id ? 'selected' : '' }}>
                {{ $kelas->kelas }}
                </option>
            @endforeach
            </select>

            <select name="jurusan_id">
            @foreach ($jurusanList as $jurusan)
                <option value="{{ $jurusan->kode_jurusan }}" {{ $siswa->jurusan_id == $jurusan->kode_jurusan ? 'selected' : '' }}>
                {{ $jurusan->nama }}
                </option>
            @endforeach
            </select>
        </div>

        {{-- Foto --}}
        <div class="mb-3">
            <label for="foto" class="form-label">Foto (opsional)</label>
            <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror">
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($siswa->foto)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" width="100">
                </div>
            @endif
        </div>
        <!-- Status (dari relasi ke users.status) -->
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="submitted" {{ $siswa->user->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ $siswa->user->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $siswa->user->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
