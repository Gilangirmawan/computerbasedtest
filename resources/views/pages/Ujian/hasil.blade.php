@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Hasil Ujian Siswa</h1>
        <a href="{{ route('ujian.detail', $ikutUjian->id_ujian) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Detail Ujian
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pengerjaan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nama Siswa:</strong> {{ $ikutUjian->profil_siswa->nama ?? 'N/A' }} <br>
                    <strong>Kelas:</strong> {{ $ikutUjian->profil_siswa->kelas->kelas ?? 'N/A' }} - {{ $ikutUjian->profil_siswa->kelas->jurusan->nama ?? '' }}
                </div>
                <div class="col-md-6 text-md-right">
                    <strong>Ujian:</strong> {{ $ikutUjian->ujian->nama_ujian ?? 'N/A' }} <br>
                    <strong>Jadwal Ujian:</strong> {{ \Carbon\Carbon::parse($ikutUjian->ujian->waktu_mulai)->format('d M Y, H:i') }} <br>
                    <strong>Durasi:</strong> {{ $ikutUjian->ujian->waktu }} Menit <br>
                    <strong>Nilai Akhir:</strong> 
                    <span class="badge @if($ikutUjian->nilai >= 80) bg-success @elseif($ikutUjian->nilai >= 60) bg-warning @else bg-danger @endif">
                        {{ number_format($ikutUjian->nilai, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mt-4 mb-3">Rincian Jawaban</h5>
    {{-- ====================================================== --}}
    {{-- PERBAIKAN DI SINI: Looping menggunakan '$daftarJawaban' --}}
    {{-- ====================================================== --}}
    @forelse($daftarJawaban as $index => $jawaban)
        <div class="card shadow mb-3">
            <div class="card-body">
                @if ($jawaban->soal)
                    <p><b>{{ $index + 1 }}. {!! $jawaban->soal->soal !!}</b></p>
                    
                    <ul class="list-group list-group-flush">
                        @foreach(['a','b','c','d','e'] as $opsi)
                            @php
                                $field = 'opsi_'.$opsi;
                                $is_jawaban_siswa = (strtolower($jawaban->jawaban) == $opsi);
                                $is_kunci_jawaban = (strtolower($jawaban->soal->jawaban) == $opsi);
                            @endphp
                            @if(!empty($jawaban->soal->$field))
                                <li class="list-group-item 
                                    @if($is_jawaban_siswa && $is_kunci_jawaban) table-success @endif 
                                    @if($is_jawaban_siswa && !$is_kunci_jawaban) table-danger @endif
                                    @if(!$is_jawaban_siswa && $is_kunci_jawaban) table-info @endif
                                ">
                                    {{ strtoupper($opsi) }}. {!! $jawaban->soal->$field !!}

                                    @if($is_jawaban_siswa) <span class="badge bg-warning float-right">Jawaban Siswa</span> @endif
                                    @if($is_kunci_jawaban) <span class="badge bg-success float-right ml-2">Kunci Jawaban</span> @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-danger"><b>Soal untuk jawaban ini telah dihapus dari bank soal.</b></p>
                @endif
            </div>
        </div>
    @empty
        <div class="card shadow">
            <div class="card-body text-center">
                <p>Tidak ditemukan data rincian jawaban untuk ujian ini.</p>
            </div>
        </div>
    @endforelse
    <div class="d-flex justify-content-center">
        {{ $daftarJawaban->links() }}
    </div>
@endsection