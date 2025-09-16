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

{{-- Catatan / Petunjuk --}}
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Petunjuk Pengerjaan</h5>
        <ul>
            <li>Pastikan kelas yang dipilih sudah sesuai.</li>
            <li>Pastikan koneksi internet stabil.</li>
            <li>Hapus soal setelah ujian sudah pada mata pelajaran tertentu sudah diselesaikan.</li>
        </ul>
    </div>

    {{-- Notifikasi --}}
    @if (session('success') && !session('swal_success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
                                            {{-- Karena controller sudah memfilter, kita bisa langsung tampilkan tombol --}}
                                            <a href="{{ route('banksoal.edit', $soal->id) }}" class="btn btn-sm btn-warning">
                                                Edit <i class="fas fa-pen"></i>
                                            </a>
                                            <form id="delete-form-{{ $soal->id }}" action="{{ route('banksoal.delete', $soal->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-button" data-form-id="{{ $soal->id }}">
                                                    Hapus <i class="fas fa-trash"></i>
                                                </button>
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

                        {{-- Menampilkan tombol paginasi --}}
                        <div class="d-flex justify-content-center">
                            {{ $soalList->links() }}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. Logika untuk notifikasi SUKSES
        @if (session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('swal_success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        // 2. Logika untuk konfirmasi HAPUS
        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                var formId = this.getAttribute('data-form-id');
                var form = document.getElementById('delete-form-' + formId);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Soal yang akan dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

    });
</script>
@endsection