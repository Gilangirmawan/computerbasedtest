<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ujian;
use App\Models\IkutUjian;
use App\Models\Guru;
use App\Models\Soal;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard yang sesuai berdasarkan peran pengguna.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role_id == 3) { // 3 = siswa
            
            $profilSiswa = $user->siswa;
            if ($profilSiswa) {
                $profilSiswa->load(['kelas', 'jurusan']);
            }
            
            $ujianTersedia = collect();
            $ujianSelesaiIds = []; // Default array kosong
            
            if ($profilSiswa) {
                $ujianTersedia = Ujian::with('mapel')
                    ->where('kelas_id', $profilSiswa->kelas_id)
                    ->where('kode_jurusan', $profilSiswa->jurusan_id)
                    ->orderBy('waktu_mulai', 'desc')
                    ->get();
                
                // =================================================================
                // PERBAIKAN DI SINI:
                // Gunakan ID dari profil siswa ($profilSiswa->id) untuk mencari di tabel IkutUjian,
                // karena kolom 'id_siswa' di sana menyimpan ID dari tabel siswa.
                // =================================================================
                $semuaRiwayatUjian = IkutUjian::where('id_siswa', $profilSiswa->id)->get();
                $ujianSelesaiIds = IkutUjian::where('id_siswa', $profilSiswa->id)
                                            ->pluck('id_ujian')
                                            ->toArray();
            }

            return view('pages.dashboard_siswa', compact('profilSiswa', 'ujianTersedia', 'ujianSelesaiIds'));

        } elseif ($user->role_id == 2) { // 2 = guru
            
            // Card 1: Ambil profil guru yang sedang login
            $profilGuru = Guru::where('user_id', $user->id)->first();

            // Card 2: Hitung jumlah soal yang dibuat oleh guru ini
            $jumlahSoal = 0;
            if ($profilGuru) {
                $jumlahSoal = Soal::where('id_guru', $profilGuru->id)->count();
            }

            // Card 3: Ambil 3 ujian terakhir yang dibuat oleh guru ini
            $ujianDibuat = collect();
            if ($profilGuru) {
                $ujianDibuat = Ujian::where('id_guru', $profilGuru->id)
                                    ->with('mapel')
                                    ->latest() // Mengurutkan dari yang terbaru
                                    ->take(3)  // Mengambil 3 data teratas
                                    ->get();
            }

            return view('pages.dashboard_guru', compact('profilGuru', 'jumlahSoal', 'ujianDibuat')); 
        } elseif ($user->role_id == 1) { // 1 = admin
            return view('pages.dashboard');
        }

        // Fallback jika pengguna tidak memiliki peran atau peran tidak cocok
        return view('pages.dashboard');
    }
}