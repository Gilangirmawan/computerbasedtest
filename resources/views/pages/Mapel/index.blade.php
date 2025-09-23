@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Mata Pelajaran</h1>
        <a href="{{ route('mapel.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Mapel
        </a>
    </div>

    {{-- Menampilkan notifikasi sukses dengan SweetAlert --}}
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
                            <th style="width: 50px;">No</th>
                            <th>Nama Mata Pelajaran</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapel as $index => $item)
                            <tr>
                                <td class="text-center">{{ $mapel->firstItem() + $index }}</td>
                                <td>{{ $item->nama }}</td>
                                <td class="text-center">
                                    <a href="{{ route('mapel.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-pen"> Edit</i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('mapel.delete', $item->id) }}" method="POST" class="d-inline">
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
                                <td colspan="3" class="text-center">Data Mata Pelajaran Tidak Tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Menampilkan tombol paginasi --}}
            <div class="d-flex justify-content-center">
                {{ $mapel->links() }}
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
                    title: 'Anda yakin ingin menghapus data ini?',
                    text: "Tindakan ini tidak dapat diurungkan!",
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