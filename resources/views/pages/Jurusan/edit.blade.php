@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jurusan</h1>
    </div>

    {{-- Form Edit And Update --}}
    <div class="row">
        <div class="col">
            <div class="card shadow p-4">
                <form action="{{ route('jurusan.update', $jurusan->kode_jurusan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kode_jurusan" class="form-label">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" id="kode_jurusan" class="form-control @error('kode_jurusan') is-invalid @enderror" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}">
                        @error('kode_jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $jurusan->nama) }}">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection