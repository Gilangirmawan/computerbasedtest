@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Soal</h3>
    {{-- Pesan error validasi --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <strong>Gagal menyimpan:</strong>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <form action="{{ route('banksoal.tambah') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">Mata Pelajaran</label>
        <select name="id_mapel" class="form-control" required>
        <option value="">-- Pilih Mapel --</option>
        @foreach ($mapelList as $mapel)
            <option value="{{ $mapel->id }}" {{ old('id_mapel') == $mapel->id ? 'selected' : '' }}>
            {{ $mapel->nama }}
            </option>
        @endforeach
        </select>
        @error('id_mapel') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Kelas</label>
        {{-- gunakan kelas_id jika tabel soal sudah memakai FK kelas_id --}}
        <select name="kelas_id" class="form-control" required>
        <option value="">-- Pilih Kelas --</option>
        @foreach ($kelasList as $k)
            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
            {{ $k->kelas }}{{ $k->jurusan ? ' - '.$k->jurusan->nama : '' }}
            </option>
        @endforeach
        </select>
        @error('kelas_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- field lain: soal, opsi_a..e, jawaban, file --}}
    <div class="mb-3">
        <label class="form-label">Soal</label>
        <textarea name="soal" class="form-control" required>{{ old('soal') }}</textarea>
        @error('soal') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    @foreach(['a','b','c','d','e'] as $opt)
        <div class="mb-3">
        <label class="form-label">Opsi {{ strtoupper($opt) }}</label>
        <input type="text" name="opsi_{{ $opt }}" value="{{ old('opsi_'.$opt) }}" class="form-control" @if($opt !== 'e') required @endif>
        @error('opsi_'.$opt) <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    @endforeach

    <div class="mb-3">
        <label class="form-label">Jawaban Benar</label>
        <select name="jawaban" class="form-control" required>
        <option value="">-- Pilih --</option>
        @foreach(['A','B','C','D','E'] as $j)
            <option value="{{ $j }}" {{ old('jawaban') == $j ? 'selected' : '' }}>{{ $j }}</option>
        @endforeach
        </select>
        @error('jawaban') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">File (opsional)</label>
        <input type="file" name="file" class="form-control">
        @error('file') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
