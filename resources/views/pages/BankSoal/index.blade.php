@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Soal</h1>
    <div>
        <a href="{{ route('banksoal.import.create') }}" class="btn btn-sm btn-info shadow-sm">
            <i class="fas fa-upload fa-sm text-white-50"></i> Import Soal
        </a>
        <a href="{{ route('banksoal.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Soal
        </a>
    </div>
</div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Tabel Soal -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr class="text-center">
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
                                    <tr class="text-center">
                                        {{-- PERBAIKAN DI SINI: Menggunakan firstItem() untuk penomoran yang benar --}}
                                        <td>{{ $soalList->firstItem() + $no }}</td>
                                        <td>{{ Str::limit(strip_tags($soal->soal), 50) }}</td>
                                        <td>
                                            {{ $soal->kelas->kelas ?? '-' }}
                                            -
                                            {{ $soal->kelas->jurusan->nama ?? '-' }}
                                        </td>
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
                                            @php
                                                // Ambil profil guru dari user yang sedang login
                                                $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
                                            @endphp
                                            
                                            @if ($guru && $soal->id_guru == $guru->id)
                                                <a href="{{ route('banksoal.edit', $soal->id) }}" class="btn btn-sm btn-warning">
                                                    Edit <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('banksoal.delete', $soal->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus soal ini?')">
                                                        Hapus <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-secondary text-light">Dibuat oleh: {{ $soal->guru->name ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada soal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Menampilkan tombol paginasi --}}
                        <div class="d-flex justify-content-center">
                            {{ $soalList->links() }}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection