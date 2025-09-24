<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ujian;
use App\Models\IkutUjian;
use App\Models\Guru;
use App\Models\Soal;
use App\Models\User;
use App\Models\Siswa;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard yang sesuai berdasarkan peran pengguna.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        //  dd([
        //     'STATUS' => 'Mengecek data pengguna yang sedang login...',
        //     'User Lengkap' => $user,
        //     'Role ID Pengguna' => $user->role_id,
        //     'NOTE' => 'Perhatikan "Role ID Pengguna". Apakah nilainya 1? Jika tidak, maka kondisi untuk menampilkan dashboard admin tidak akan terpenuhi.'
        // ]);

        if ($user->role_id == 3) { // 3 = siswa
            
            $profilSiswa = $user->siswa;
            if ($profilSiswa) {
                $profilSiswa->load(['kelas', 'jurusan']);
            }
            
            $ujianTersedia = collect();
            $ujianSelesaiIds = []; // Default array kosong
            $riwayatUjian = collect(); // Default koleksi kosong
            if ($profilSiswa) {
                $ujianTersedia = Ujian::with('mapel')
                    ->where('kelas_id', $profilSiswa->kelas_id)
                    ->where('kode_jurusan', $profilSiswa->jurusan_id)
                    ->orderBy('waktu_mulai', 'desc')
                    ->get();
                
                $ujianSelesaiIds = IkutUjian::where('id_siswa', $profilSiswa->id)
                                            ->pluck('id_ujian')
                                            ->toArray();
                
                // Ambil semua data dari tabel ikut_ujian beserta relasi ke ujian dan mapel
                $riwayatUjian = IkutUjian::where('id_siswa', $profilSiswa->id)
                                        ->with(['ujian.mapel']) // Eager loading untuk performa
                                        ->latest('tgl_selesai') // Urutkan dari yang terbaru
                                        ->get();
            }
            // =================================================================

            // Tambahkan '$riwayatUjian' ke dalam compact()
            return view('pages.dashboard_siswa', compact('profilSiswa', 'ujianTersedia', 'ujianSelesaiIds', 'riwayatUjian'));

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
        } elseif ($user->role_id == 1) {
            // Card 1: Hitung jumlah administrator (user dengan role_id = 1)
            $jumlahAdmin = User::where('role_id', 1)->count();
            
            // Card 2: Hitung jumlah total guru
            $jumlahGuru = Guru::count();

            // Card 3: Hitung jumlah total siswa
            $jumlahSiswa = Siswa::count();
            
            return view('pages.dashboard_admin', compact('jumlahAdmin', 'jumlahGuru', 'jumlahSiswa'));
        }

        // Fallback jika pengguna tidak memiliki peran atau peran tidak cocok
        return view('pages.dashboard');
    }
}