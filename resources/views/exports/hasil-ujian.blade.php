<table>
    <thead>
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 14px;">
                Hasil Ujian: {{ $ujian->nama_ujian }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 12px;">
                Mata Pelajaran: {{ $ujian->mapel->nama ?? 'N/A' }} | Kelas: {{ $ujian->kelas->kelas ?? 'N/A' }} - {{ $ujian->kelas->jurusan->nama ?? '' }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Nama Siswa</th>
            <th style="font-weight: bold; border: 1px solid #000;">Kelas</th>
            <th style="font-weight: bold; border: 1px solid #000;">Waktu Selesai</th>
            <th style="font-weight: bold; border: 1px solid #000;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ujian->peserta as $index => $peserta)
            <tr>
                <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000;">{{ $peserta->profil_siswa->nama ?? 'Siswa Telah Dihapus' }}</td>
                <td style="border: 1px solid #000;">
                    @if ($peserta->profil_siswa && $peserta->profil_siswa->kelas)
                        {{ $peserta->profil_siswa->kelas->kelas }} - {{ $peserta->profil_siswa->kelas->jurusan->nama ?? '' }}
                    @else
                        N/A
                    @endif
                </td>
                <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($peserta->tgl_selesai)->format('d-m-Y H:i:s') }}</td>
                <td style="border: 1px solid #000;">{{ number_format($peserta->nilai, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>