@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Guru</h1>
    </div>

    {{-- Form Edit And Update --}}
    <div class="row">
        <div class="col">
            <div class="card shadow p-4">
                <form action="{{ route('guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                            <input type="text" name="nip" id="nip" class="form-control" @error('nip') is-invalid @enderror value="{{ old('nip', $guru->nip) }}">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" @error('nama') is-invalid @enderror value="{{ old('nama', $guru->nama) }}">
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