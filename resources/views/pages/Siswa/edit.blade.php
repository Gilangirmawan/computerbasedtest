@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Siswa</h1>
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
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

            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS *</label>
                            <input type="text" name="nis" id="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $siswa->nis) }}" required>
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $siswa->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3">
                            <label for="foto" class="form-label">Ganti Foto (Opsional)</label>
                            <input type="file" name="foto" id="foto" class="form-control-file @error('foto') is-invalid @enderror">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($siswa->foto)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" width="100" class="img-thumbnail">
                                    <small class="d-block mt-1">Foto saat ini</small>
                                </div>
                            @endif
                        </div> --}}
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kelas_id_select" class="form-label">Kelas - Jurusan *</label>
                            <select name="kelas_id" id="kelas_id_select" class="form-control @error('kelas_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas dan Jurusan --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" 
                                            data-jurusan-id="{{ $kelas->jurusan->kode_jurusan ?? '' }}"
                                            {{ old('kelas_id', $siswa->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->kelas }} - {{ $kelas->jurusan->nama ?? 'Umum' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="jurusan_id" id="jurusan_id_hidden" value="{{ old('jurusan_id', $siswa->jurusan_id) }}">
                        @error('jurusan_id')
                            <div class="text-danger small mt-n2 mb-3">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <label for="username" class="form-label">Username Akun *</label>
                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $siswa->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status">Status Akun *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="submitted" {{ old('status', $siswa->user->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ old('status', $siswa->user->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $siswa->user->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Script untuk mengisi jurusan_id secara otomatis --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kelasSelect = document.getElementById('kelas_id_select');
        const jurusanHiddenInput = document.getElementById('jurusan_id_hidden');

        function updateJurusanId() {
            const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const jurusanId = selectedOption.getAttribute('data-jurusan-id');
                jurusanHiddenInput.value = jurusanId;
            }
        }
        
        kelasSelect.addEventListener('change', updateJurusanId);
        updateJurusanId();
    });
</script>
@endpush