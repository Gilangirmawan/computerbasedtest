@extends('layouts.app')

@section('content')
<!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Jurusan</h1>
        <a href="/jurusan/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Jurusan
        </a>
    </div>

    {{-- Tabel --}}
    <div class="row">
        <div class="col">
            <div class="card shadow p-4">
                <div class="card">
                    <table class="table table-responsive table-bordered table-hovered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Jurusan</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         @if (count($jurusan) == 0)
                            <tbody>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <p class="text-center">Data Jurusan Tidak Tersedia</p>
                                    </td>
                                </tr>
                            </tbody>
                        @else
                            <tbody>
                                @foreach ($jurusan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kode_jurusan }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            <div class="d-flex">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('jurusan.edit', $item->kode_jurusan) }}" class="btn btn-sm btn-warning mr-2">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                {{-- Tombol Hapus dengan Form DELETE --}}
                                                <form action="{{ route('jurusan.delete', $item->kode_jurusan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus Jurusan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection