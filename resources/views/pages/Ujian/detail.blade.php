@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Ujian</h1>
        <a href="{{ route('ujian.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Daftar Ujian
        </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Ujian</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Nama Ujian:</strong>
                            <span>{{ $ujian->nama_ujian }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Mata Pelajaran:</strong>
                            <span>{{ $ujian->mapel->nama ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Kelas:</strong>
                            <span>{{ $ujian->kelas->kelas ?? 'N/A' }} - {{ $ujian->kelas->jurusan->nama ?? '' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Jumlah Soal:</strong>
                            <span>{{ $ujian->jumlah_soal }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Durasi:</strong>
                            <span>{{ $ujian->waktu }} Menit</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Token:</strong>
                            <code>{{ $ujian->token }}</code>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Waktu Mulai:</strong>
                            <span>{{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('d M Y, H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Ujian Siswa ({{ $ujian->peserta->count() }} Peserta)</h6>
                </div>
                <a href="{{ route('ujian.export', $ujian->id) }}" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel fa-sm"></i> Export Excel
                </a>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas-Jurusan</th>
                                    <th>Waktu Selesai</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ujian->peserta as $index => $peserta)
                                    <tr class="text-center">
                                        <td class="text-center">{{ $pesertaList->firstItem() + $index }}</td>
                                        <td>{{ $peserta->profil_siswa->nama ?? 'Siswa Telah Dihapus' }}</td>
                                        <td>
                                            @if ($peserta->profil_siswa && $peserta->profil_siswa->kelas)
                                                {{ $peserta->profil_siswa->kelas->kelas }} - {{ $peserta->profil_siswa->kelas->jurusan->nama ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($peserta->tgl_selesai)->format('d M Y, H:i') }}</td>
                                        <td class="text-center">
                                            <span class="badge 
                                                @if($peserta->nilai >= 80) bg-success 
                                                @elseif($peserta->nilai >= 60) bg-warning 
                                                @else bg-danger @endif">
                                                {{ number_format($peserta->nilai, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('ujian.lihatHasil', $peserta->id) }}" class="btn btn-sm btn-info" title="Lihat Detail Jawaban">
                                                <i class="fas fa-eye"> Lihat Hasil Ujian</i>
                                            </a>
                                            <form id="cancel-form-{{ $peserta->id }}" action="{{ route('ujian.batalkanHasil', $peserta->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger cancel-button" data-form-id="{{ $peserta->id }}" title="Batalkan Ujian Siswa">
                                                    <i class="fas fa-times"> Batalkan Ujian</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada siswa yang mengerjakan ujian ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-auto d-flex justify-content-center">
                        {{ $pesertaList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.cancel-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var formId = this.getAttribute('data-form-id');
            var form = document.getElementById('cancel-form-' + formId);

            Swal.fire({
                title: 'Anda yakin ingin membatalkan hasil ujian siswa ini?',
                text: "Tindakan ini akan menghapus semua jawaban dan nilai siswa. Siswa dapat mengerjakan ulang ujian ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection