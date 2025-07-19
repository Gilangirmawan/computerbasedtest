@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Soal</h3>
    <form action="{{ route('banksoal.tambah') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="id_mapel" class="form-label">Mata Pelajaran</label>
            <select name="id_mapel" id="id_mapel" class="form-select" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapelList as $mapel)
                    <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Pilih Kelas</label>
            <select name="kelas" id="kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->kelas }}">
                        {{ $kelas->kelas }} - {{ $kelas->jurusan->nama ?? 'Jurusan tidak tersedia' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="soal" class="form-label">Soal</label>
            <textarea name="soal" id="soal" class="form-control" rows="3" required></textarea>
        </div>

        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
        <div class="mb-3">
            <label for="opsi_{{ $opt }}" class="form-label">Opsi {{ strtoupper($opt) }}</label>
            <input type="text" name="opsi_{{ $opt }}" id="opsi_{{ $opt }}" class="form-control" required>
        </div>
        @endforeach

        <div class="mb-3">
            <label for="jawaban" class="form-label">Jawaban Benar</label>
            <select name="jawaban" id="jawaban" class="form-select" required>
                <option value="">-- Pilih Jawaban --</option>
                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">File (Opsional)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.docx">
        </div>

        <div class="mb-3">
            <label for="tipe_file" class="form-label">Tipe File</label>
            <input type="text" name="tipe_file" id="tipe_file" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Soal</button>
    </form>
</div>
@endsection
