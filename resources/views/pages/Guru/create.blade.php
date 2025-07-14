@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Guru</h1>
    </div>

    {{-- Form Create --}}
    <div class="row">
        <div class="col">
            <div class="card shadow p-4">
                <form action="{{ route('guru.tambah') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" name="nip" id="nip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    {{-- <div class="form-group">
                        <label for="password">Password Guru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div> --}}
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection