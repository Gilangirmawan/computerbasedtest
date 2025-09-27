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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Siswa</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('siswa.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="jurusan_id">Filter Berdasarkan Jurusan</label>
                            <select name="jurusan_id" id="jurusan_id" class="form-control">
                                <option value="">Semua Jurusan</option>
                                @foreach ($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->kode_jurusan }}" {{ request('jurusan_id') == $jurusan->kode_jurusan ? 'selected' : '' }}>
                                        {{ $jurusan->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Filter Berdasarkan Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary w-100 mt-2">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
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
                {{ $siswa->appends(request()->query())->links() }}
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