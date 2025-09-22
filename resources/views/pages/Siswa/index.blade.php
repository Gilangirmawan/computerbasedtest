@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align--items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Siswa</h1>
        <div>
            <a href="{{ route('siswa.import.create') }}" class="btn btn-sm btn-info shadow-sm">
                <i class="fas fa-upload fa-sm text-white-50"></i> Import Data
            </a>
            <a href="{{ route('siswa.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data
            </a>
        </div>
    </div>

    {{-- Notifikasi akan ditangani oleh skrip di bawah --}}
    @if (session('swal_success'))
    @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelas - Jurusan</th>
                            <th>Status Akun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $index => $item)
                            <tr class="text-cnter">
                                <td class="text-center">{{ $siswa->firstItem() + $index }}</td>
                                <td>{{ $item->nis }}</td>
                                <td>{{ $item->nama }}</td>
                                <td class="text-center">{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $item->kelas->kelas ?? '-' }} - {{ $item->jurusan->nama ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($item->user)
                                        @if(strtolower($item->user->status) == 'approved')
                                            <span class="badge badge-pill badge-success">{{ ucfirst($item->user->status) }}</span>
                                        @elseif(strtolower($item->user->status) == 'submitted')
                                            <span class="badge badge-pill badge-warning">{{ ucfirst($item->user->status) }}</span>
                                        @elseif(strtolower($item->user->status) == 'rejected')
                                            <span class="badge badge-pill badge-danger">{{ ucfirst($item->user->status) }}</span>
                                        @else
                                            <span class="badge badge-pill badge-secondary">{{ ucfirst($item->user->status) }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-pill badge-dark">Belum Ada Akun</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-pen"> Edit</i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('siswa.delete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-button" data-form-id="{{ $item->id }}" title="Hapus">
                                            <i class="fas fa-trash"> Hapus</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Data Siswa Tidak Tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Menampilkan tombol paginasi --}}
            <div class="d-flex justify-content-center">
                {{ $siswa->links() }}
            </div>
        </div>
    </div>

{{-- Script untuk notifikasi SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Logika untuk notifikasi SUKSES
        @if (session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('swal_success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        // Logika untuk konfirmasi HAPUS
        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                var formId = this.getAttribute('data-form-id');
                var form = document.getElementById('delete-form-' + formId);

                Swal.fire({
                    title: 'Anda yakin ingin menghapus data siswa ini?',
                    text: "Akun login siswa yang terhubung juga akan dihapus!",
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