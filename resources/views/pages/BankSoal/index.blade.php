@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Soal</h1>
        <a href="/siswa/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data
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
                                <th>Soal</th>
                                <th>Kelas-Mapel</th>
                                <th>Kunci Jawaban</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        @if (count($banksoal) == 0)
                            <tbody>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <p class="text-center">Data Soal Tidak Tersedia</p>
                                    </td>
                                </tr>
                            </tbody>
                        @else
                            <tbody>
                                @foreach ($banksoal as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->soal }}</td>
                                        <td>{{ $item->kelas->mapel }}</td>
                                       <td>
                                            @foreach($item->opsi_jawaban as $opsi)
                                                <div>{{ $opsi->teks }} @if($opsi->is_benar) ✅ @endif</div>
                                            @endforeach
                                        </td>
                                        <td>{{ $item->mapel->nama ?? '-' }}</td>
                                        <td>{{ $item->kelas->kelas ?? '-' }}</td>
                                        <td>{{ $item->aksi }}</td>
                                        <td>
                                            <div class="d-flex">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-sm btn-warning mr-2">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                {{-- Tombol Hapus dengan Form DELETE --}}
                                                <form action="{{ route('siswa.delete', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
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
