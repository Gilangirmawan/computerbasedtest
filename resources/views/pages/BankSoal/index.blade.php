@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Soal</h1>
        <a href="{{ route('banksoal.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Soal
        </a>
    </div>

    <!-- Tabel Soal -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Soal</th>
                                    <th>Kelas</th>
                                    <th>Mapel</th>
                                    <th>Jawaban</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($soalList as $no => $soal)
                                    <tr>
                                        <td>{{ $no + 1 }}</td>
                                        <td>{{ Str::limit(strip_tags($soal->soal), 50) }}</td>
                                        <td>{{ $soal->kelas }}</td>
                                        <td>{{ $soal->mapel->nama ?? '-' }}</td>
                                        <td>{{ $soal->jawaban }}</td>
                                        <td>
                                            @if($soal->file)
                                                <a href="{{ asset('storage/' . $soal->file) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('banksoal.edit', $soal->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('banksoal.delete', $soal->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus soal ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada soal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
