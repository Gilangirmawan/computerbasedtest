@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Ujian</h1>
  <a href="{{ route('ujian.create') }}" class="btn btn-sm btn-primary shadow-sm">
    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Ujian
  </a>
</div>

{{-- Catatan / Petunjuk --}}
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Petunjuk Pembuatan Ujian</h5>
        <ul>
            <li>Tentukan Judul Ujian contoh: UTS (Ujian Tengah Semester Ganjil/Genap).</li>
            <li>Pastikan koneksi internet stabil sebelum membuat ujian.</li>
            <li>Pastikan kelas yang dipilih sudah.</li>
            <li>Jumlah soal yang ingin dijadikan ujian harus sesuai dengan yang ada pada bank soal.</li>
            <li>Setelah waktu ujian selesai harap periksa detail ujian untuk melihat <br>
              seluruh siswa yang mengerjakan ujian.</li>
            <li>Terdapat tombol batalkan ujian bagi siswa yang melanggar peraturan.</li>
            <li>Silahkan langsung export nilai siswa jika semuanya sudah selesai.</li>
            <li>Hapus ujian yang sudah dikerjakan oleh siswa <br> 
                agar tidak ada penumpukan (pastikan nilai sudah diexport terlebih dahulu).</li>
        </ul>
    </div>

<div class="card shadow">
  <div class="card-body">
    @if(session('success') && !session('swal_success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle">
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
              <td>{{ $ujianList->firstItem() + $i }}</td>
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
                <a href="{{ route('ujian.detail', $u->id) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> Lihat Detail
                </a>
                <a href="{{ route('ujian.edit', $u->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
                <form id="delete-form-{{ $u->id }}" action="{{ route('ujian.delete', $u->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger delete-button" data-form-id="{{ $u->id }}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="10" class="text-center text-muted">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $ujianList->links() }}
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