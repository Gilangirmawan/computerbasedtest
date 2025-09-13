@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Ujian</h1>
  <a href="{{ route('ujian.create') }}" class="btn btn-sm btn-primary shadow-sm">
    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Ujian
  </a>
</div>

<div class="card shadow">
  <div class="card-body">
    @if(session('success') && !session('swal_success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>No</th>
            <th>Nama Ujian</th>
            <th>Mapel</th>
            <th>Paket</th>
            <th>Kelas - Jurusan</th>
            <th>Mulai</th>
            <th>Durasi (menit)</th>
            <th>Token</th>
            <th>Jenis</th>
            <th style="width: 140px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ujianList as $i => $u)
            <tr class="text-center">
              <td>{{ $i+1 }}</td>
              <td>{{ $u->nama_ujian }}</td>
              <td>{{ $u->mapel->nama ?? '-' }}</td>
              <td>{{ $u->soal_count }}</td>
              <td>
                {{ $u->kelas->kelas ?? '-' }}
                @if(optional($u->kelas)->jurusan)
                  - {{ $u->kelas->jurusan->nama }} ({{ $u->kelas->jurusan_id }})
                @elseif($u->kode_jurusan)
                  - {{ $u->kode_jurusan }}
                @endif
              </td>
              <td>{{ optional($u->waktu_mulai)->format('d/m/Y H:i') ?? '-' }}</td>
              <td>{{ $u->waktu }}</td>
              <td><code>{{ $u->token }}</code></td>
              <td class="text-capitalize">{{ $u->jenis }}</td>
              <td>
                @php
                    // Ambil profil guru dari user yang sedang login
                    $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
                @endphp
            
                @if ($guru && $u->id_guru == $guru->id)
                    {{-- Jika guru yang login adalah pembuat ujian, tampilkan tombol --}}
                    <a href="{{ route('ujian.detail', $u->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>Lihat Detail Ujian
                    </a>
                    <a href="{{ route('ujian.edit', $u->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                    <form id="delete-form-{{ $u->id }}" action="{{ route('ujian.delete', $u->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        {{-- Tombol ini sekarang memicu SweetAlert, bukan submit langsung --}}
                        <button type="button" class="btn btn-sm btn-danger delete-button" data-form-id="{{ $u->id }}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                @else
                    {{-- Jika bukan, tampilkan nama pembuatnya --}}
                    <span class="badge bg-secondary text-light">Dibuat oleh: {{ $u->guru->name ?? 'N/A' }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="10" class="text-center text-muted">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
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
                    title: 'Anda yakin ingin menghapus ujian ini?',
                    text: "Semua data terkait ujian ini akan dihapus permanen!",
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