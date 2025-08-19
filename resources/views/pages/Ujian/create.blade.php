@extends('layouts.app')

@section('content')
<style>
  /* sentuhan kecil biar rapi */
  .section-title {
    font-weight: 600;
    font-size: .95rem;
    color: #6b7280; /* gray-500 */
    letter-spacing: .2px;
  }
  .input-muted {
    background: #f8fafc; /* subtle gray */
  }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Tambah Ujian</h1>
  <a href="{{ route('ujian.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
  </a>
</div>

<div class="card shadow">
  <div class="card-body">
    <form action="{{ route('ujian.tambah') }}" method="POST" novalidate>
      @csrf

      {{-- Bagian 1: Informasi Ujian --}}
      <div class="mb-3 section-title">Informasi Ujian</div>
      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Nama Ujian</label>
          <input type="text"
                 name="nama_ujian"
                 class="form-control @error('nama_ujian') is-invalid @enderror"
                 value="{{ old('nama_ujian') }}"
                 {{-- placeholder="Contoh: UTS Bahasa Indonesia" --}}
                 required>
          @error('nama_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-lg-6">
          <label class="form-label">Mata Pelajaran</label>
          <select name="id_mapel"
                  class="form-select form-control @error('id_mapel') is-invalid @enderror"
                  required>
            <option value="">— Pilih Mapel —</option>
            @foreach($mapelList as $m)
              <option value="{{ $m->id }}" {{ old('id_mapel') == $m->id ? 'selected' : '' }}>
                {{ $m->nama }}
              </option>
            @endforeach
          </select>
          @error('id_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-lg-6">
          <label class="form-label">Kelas</label>
          <select id="kelas_id"
                  name="kelas_id"
                  class="form-select form-control @error('kelas_id') is-invalid @enderror"
                  required>
            <option value="">— Pilih Kelas —</option>
            @foreach($kelasList as $k)
              <option
                value="{{ $k->id }}"
                data-kode-jurusan="{{ $k->jurusan_id }}"
                data-jurusan-nama="{{ optional($k->jurusan)->nama }}"
                {{ old('kelas_id') == $k->id ? 'selected' : '' }}
              >
                {{ $k->kelas }} — {{ optional($k->jurusan)->nama ?? 'Jurusan?' }} ({{ $k->jurusan_id }})
              </option>
            @endforeach
          </select>
          @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-lg-6">
          <label class="form-label d-flex align-items-center">
            Jurusan <small class="text-muted ml-2">(otomatis dari pilihan kelas)</small>
          </label>
          <div class="input-group">
            <input type="text"
                   id="jurusan_nama_show"
                   class="form-control input-muted"
                   value="{{ old('jurusan_nama_show') }}"
                   {{-- placeholder="Akan terisi otomatis" --}}
                   readonly>
            <span class="input-group-text">Kode</span>
            <input type="text"
                   id="kode_jurusan_show"
                   class="form-control input-muted"
                   value="{{ old('kode_jurusan') }}"
                   readonly>
          </div>
          <input type="hidden" name="kode_jurusan" id="kode_jurusan" value="{{ old('kode_jurusan') }}">
          @error('kode_jurusan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <hr class="my-4">

      {{-- Bagian 2: Pengaturan Soal --}}
      <div class="mb-3 section-title">Pengaturan Soal</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Jumlah Soal</label>
          <input type="number"
                 min="1"
                 name="jumlah_soal"
                 class="form-control @error('jumlah_soal') is-invalid @enderror"
                 value="{{ old('jumlah_soal') }}"
                 {{-- placeholder="cth: 40" --}}
                 required>
          @error('jumlah_soal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Durasi (menit)</label>
          <input type="number"
                 min="1"
                 name="waktu"
                 class="form-control @error('waktu') is-invalid @enderror"
                 value="{{ old('waktu') }}"
                 {{-- placeholder="cth: 90" --}}
                 required>
          @error('waktu') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Jenis Soal</label>
          <select name="jenis"
                  class="form-select form-control @error('jenis') is-invalid @enderror"
                  required>
            <option value="">— Pilih Jenis —</option>
            <option value="acak" {{ old('jenis')=='acak' ? 'selected' : '' }}>Acak</option>
            <option value="set"   {{ old('jenis')=='set'   ? 'selected' : '' }}>Set</option>
          </select>
          @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <hr class="my-4">

      {{-- Bagian 3: Waktu Pelaksanaan --}}
      <div class="mb-3 section-title">Waktu Pelaksanaan</div>
      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Waktu Mulai</label>
          <input type="datetime-local"
                 name="waktu_mulai"
                 class="form-control @error('waktu_mulai') is-invalid @enderror"
                 value="{{ old('waktu_mulai') }}"
                 required>
          @error('waktu_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-lg-6">
          <label class="form-label">Terlambat (opsional)</label>
          <input type="datetime-local"
                 name="terlambat"
                 class="form-control @error('terlambat') is-invalid @enderror"
                 value="{{ old('terlambat') }}">
          @error('terlambat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <hr class="my-4">

      {{-- Bagian 4: Keamanan --}}
      <div class="mb-3 section-title">Keamanan</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Token</label>
          <input type="text"
                 name="token"
                 maxlength="5"
                 class="form-control @error('token') is-invalid @enderror"
                 value="{{ old('token') }}"
                 {{-- placeholder="Contoh: ELSWN" --}}
                 required>
          @error('token') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <small class="text-muted">Maksimal 5 karakter. Huruf/angka diperbolehkan.</small>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary">
          <i class="fas fa-save mr-1"></i> Simpan
        </button>
        <a href="{{ route('ujian.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

{{-- Auto-set jurusan berdasarkan kelas --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const kelas = document.getElementById('kelas_id');
    const inKode = document.getElementById('kode_jurusan');
    const showKode = document.getElementById('kode_jurusan_show');
    const showNama = document.getElementById('jurusan_nama_show');

    function syncJurusan() {
      const opt = kelas.options[kelas.selectedIndex];
      if (!opt) return;
      const kode = opt.getAttribute('data-kode-jurusan') || '';
      const nama = opt.getAttribute('data-jurusan-nama') || '';
      inKode.value = kode;
      showKode.value = kode;
      showNama.value = nama || (kode ? '—' : '');
    }

    kelas.addEventListener('change', syncJurusan);
    // set nilai awal (untuk old value)
    syncJurusan();
  });
</script>
@endsection
