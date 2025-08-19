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
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Ujian</th>
            <th>Mapel</th>
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
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $u->nama_ujian }}</td>
              <td>{{ $u->mapel->nama ?? '-' }}</td>
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
                <a href="{{ route('ujian.edit', $u->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('ujian.destroy', $u->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus ujian ini?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
