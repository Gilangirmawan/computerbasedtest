@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Soal</h3>
    <form action="{{ route('banksoal.update', $soal->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="id_mapel" class="form-label">Mata Pelajaran</label>
            <select name="id_mapel" class="form-control" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)
                    <option value="{{ $m->id }}" {{ $soal->id_mapel == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <select name="kelas" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->kelas }}" {{ $soal->kelas == $k->kelas ? 'selected' : '' }}>{{ $k->kelas }} - {{ $k->jurusan->nama ?? '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Soal</label>
            <textarea name="soal" class="form-control" required>{{ old('soal', $soal->soal) }}</textarea>
        </div>

        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
        <div class="mb-3">
            <label class="form-label">Opsi {{ strtoupper($opt) }}</label>
            <input type="text" name="opsi_{{ $opt }}" value="{{ old('opsi_'.$opt, $soal->{'opsi_'.$opt}) }}" class="form-control" required>
        </div>
        @endforeach

        <div class="mb-3">
            <label for="jawaban" class="form-label">Jawaban Benar</label>
            <select name="jawaban" class="form-control" required>
                <option value="">-- Pilih Jawaban --</option>
                @foreach(['A','B','C','D','E'] as $j)
                    <option value="{{ $j }}" {{ $soal->jawaban == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">File (jika ingin mengganti)</label>
            <input type="file" name="file" class="form-control">
            @if($soal->file)
                <small>File saat ini: <a href="{{ asset('storage/' . $soal->file) }}" target="_blank">Lihat File</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('banksoal.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
