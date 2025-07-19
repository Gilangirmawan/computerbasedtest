@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kelas</h1>
    </div>

    {{-- Form Edit And Update --}}
    <div class="row">
        <div class="col">
            <div class="card shadow p-4">
                <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kelas" class="form-label">Nama Kelas</label>
                        <input type="text" name="kelas" id="kelas" class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas', $kelas->kelas) }}">
                        @error('kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3"> 
                        <label for="jurusan_id">Jurusan</label>
                            <select name="jurusan_id" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->kode_jurusan }}">{{ $jurusan->nama }}</option>
                                @endforeach
                            </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection