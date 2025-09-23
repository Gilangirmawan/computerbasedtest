@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Administrator</h1>
        @if(Auth::user()->is_superadmin)
            <a href="{{ route('administrator.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Administrator
            </a>
        @endif
    </div>

    {{-- Notifikasi akan ditangani oleh skrip di bawah --}}
    @if (session('swal_success'))
    @elseif (session('swal_error'))
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Peran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($administrators as $index => $admin)
                            <tr class="text-center">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->username }}</td>
                                <td class="text-center">
                                    @if($admin->is_superadmin)
                                        <span class="badge badge-success">Super Admin</span>
                                    @else
                                        <span class="badge badge-secondary">Admin</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(Auth::user()->id !== $admin->id)
                                        <a href="{{ route('administrator.edit', $admin->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-pen"> Edit</i>
                                        </a>
                                        <form id="delete-form-{{ $admin->id }}" action="{{ route('administrator.destroy', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger delete-button" data-form-id="{{ $admin->id }}" title="Hapus">
                                                <i class="fas fa-trash"> Hapus</i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-light">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data administrator lain.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Karena tidak ada paginasi, link dihilangkan --}}
        </div>
    </div>
@endsection

@push('scripts')
{{-- Skrip untuk notifikasi dan konfirmasi --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if (session('swal_success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('swal_success') }}' });
        @endif
        @if (session('swal_error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('swal_error') }}' });
        @endif

        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                var formId = this.getAttribute('data-form-id');
                var form = document.getElementById('delete-form-' + formId);
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data administrator ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush