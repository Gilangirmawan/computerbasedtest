<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\IkutUjian;
use App\Models\Jawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <-- PERUBAHAN 1: Tambahkan baris ini

class IkutUjianController extends Controller
{
    // Halaman awal
    public function index()
    {
        return view('pages.IkutUjian.index');
    }

    /**
     * Ambil ID siswa yang sedang login.
     *
     * @return int|null
     */
    protected function getSiswaId()
    {
        $id = Auth::id(); // mengembalikan null atau integer
        if (! $id) abort(401, 'Silakan login sebagai siswa.');
        return (int) $id;
    }

    // Daftar ujian tersedia
    public function daftar()
    {
        $siswa = Auth::user();

        $profilSiswa = $siswa->siswa;

        if (!$profilSiswa) {
            $ujian = collect(); 
        } else {
            // =================================================================
            // PERBAIKAN FINAL DI SINI
            // Mencari di kolom 'kode_jurusan' (milik tabel ujian)
            // Menggunakan nilai dari '$profilSiswa->jurusan_id' (milik tabel siswa)
            // =================================================================
            $ujian = Ujian::with('mapel')
                        ->where('kelas_id', $profilSiswa->kelas_id)
                        ->where('kode_jurusan', $profilSiswa->jurusan_id) 
                        ->get();
            // =================================================================
        }

        return view('pages.IkutUjian.daftar', compact('ujian'));
    }

    // Validasi token
    public function cekToken(Request $request)
    {
        $request->validate([
            'id_ujian' => 'required|integer',
            'token'    => 'required|string',
        ]);

        $ujian = Ujian::findOrFail($request->id_ujian);

        if ($request->token !== $ujian->token) {
            // GANTI baris withErrors menjadi with('error', ...)
            return back()->with('error', 'Token yang Anda masukkan salah, silakan coba lagi!');
        }

        return redirect()->route('ikutujian.mulai', $ujian->id);
    }

    // Mulai ujian
    public function mulai($id)
    {
        $ujian = Ujian::with('mapel')->findOrFail($id);
        
        // =================================================================
        // MENGAKTIFKAN KEMBALI BLOK VALIDASI WAKTU UJIAN
        // =================================================================
        $now = Carbon::now();
        $waktuMulai = Carbon::parse($ujian->waktu_mulai);
        // Batas akhir adalah waktu selesai + toleransi keterlambatan dari database
        $batasAkhir = Carbon::parse($ujian->waktu_selesai)->addMinutes($ujian->terlambat ?? 0);

        // Cek jika ujian belum dimulai
        if ($now->isBefore($waktuMulai)) {
            return redirect()->route('ikutujian.daftar')
                ->with('error', 'Gagal memulai! Ujian "' . $ujian->nama_ujian . '" belum dimulai.');
        }

        // Cek jika waktu untuk memulai ujian sudah terlewat
        if ($now->isAfter($batasAkhir)) {
            return redirect()->route('ikutujian.daftar')
                ->with('error', 'Gagal memulai! Waktu untuk Ujian "' . $ujian->nama_ujian . '" telah berakhir.');
        }
        // =================================================================
        // AKHIR DARI BLOK VALIDASI
        // =================================================================

        $soalIds = DB::table('paket_soal')
            ->where('id_ujian', $ujian->id)
            ->pluck('id_soal');

        $soal = Soal::whereIn('id', $soalIds)->get();

        return view('pages.IkutUjian.mulai', compact('ujian', 'soal'));
    }

    // Simpan jawaban (autosave)
    public function simpanJawaban(Request $request)
    {
        $request->validate([
            'id_ujian' => 'required|integer',
            'id_soal'  => 'required|integer',
            'jawaban'  => 'nullable|string|max:5',
        ]);

        $idSiswa = $this->getSiswaId();

        Jawaban::updateOrCreate(
            [
                'id_ujian' => $request->id_ujian,
                'id_siswa' => $idSiswa,
                'id_soal'  => $request->id_soal,
            ],
            [
                'jawaban'  => $request->jawaban,
            ]
        );

        return response()->json(['success' => true]);
    }

    // Selesai ujian → simpan semua jawaban & nilai
   public function selesaiUjian(Request $request, $idUjian)
    {
        //  dd(Auth::user());
        // =================================================================
        // SOLUSI UTAMA: Periksa otentikasi pengguna di awal.
        // Ini adalah cara paling aman untuk memastikan kita memiliki data siswa.
        // =================================================================
        $user = Auth::user();

        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('swal_error', 'Sesi Anda telah berakhir. Silakan login kembali untuk melihat hasil ujian.');
        }

        // Jika user ada, kita aman untuk mendapatkan ID-nya.
        $idSiswa = $user->id;
        // =================================================================
        // AKHIR DARI SOLUSI
        // =================================================================


        // Simpan jawaban
        $answers = $request->input('jawaban', []);
        foreach ($answers as $idSoal => $pilihan) {
            Jawaban::updateOrCreate(
                [
                    'id_ujian' => $idUjian,
                    'id_siswa' => $idSiswa,
                    'id_soal'  => $idSoal,
                ],
                [
                    'jawaban'  => $pilihan,
                ]
            );
        }

        // Ambil jawaban siswa
        $jawabanSiswa = Jawaban::where('id_ujian', $idUjian)
            ->where('id_siswa', $idSiswa)
            ->get();

        // Hitung nilai
        $benar = 0;
            foreach ($jawabanSiswa as $jwb) {
                $soal = Soal::find($jwb->id_soal);
                
                // Periksa apakah jawaban siswa ada dan soalnya ditemukan
                if ($soal && $jwb->jawaban !== null) {
                    // Bersihkan kunci jawaban dan jawaban siswa sebelum membandingkan
                    $kunciJawabanBersih = trim(strtolower($soal->jawaban));
                    $jawabanSiswaBersih = trim(strtolower($jwb->jawaban));

                    if ($kunciJawabanBersih === $jawabanSiswaBersih) {
                        $benar++;
                        $jwb->is_benar = true;
                    } else {
                        $jwb->is_benar = false;
                    }
                } else {
                    // Jika siswa tidak menjawab atau soal tidak ada, anggap salah
                    $jwb->is_benar = false;
                }
                $jwb->save();
            }

        $jumlahSoal = $jawabanSiswa->count();
        // Hindari pembagian dengan nol jika tidak ada jawaban sama sekali
        $nilai = ($jumlahSoal > 0) ? ($benar / $jumlahSoal) * 100 : 0;

        // Simpan ringkasan ke ikut_ujian
        IkutUjian::updateOrCreate(
            [
                'id_ujian' => $idUjian,
                'id_siswa' => $idSiswa,
            ],
            [
                'jml_benar'   => $benar,
                'nilai'       => $nilai,
                'tgl_selesai' => now(),
                'status'      => 'selesai',
            ]
        );

        return redirect()->route('ikutujian.daftar')
            ->with('success', 'Ujian selesai. Nilai Anda: ' . number_format($nilai, 2));
    }
}