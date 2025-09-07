<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ujian;
use App\Models\IkutUjian;

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
                // Gunakan ID dari user yang login ($user->id) untuk mencari di tabel IkutUjian,
                // karena kolom 'id_siswa' di sana menyimpan user_id.
                // =================================================================
                $ujianSelesaiIds = IkutUjian::where('id_siswa', $user->id)
                                            ->pluck('id_ujian')
                                            ->toArray();
            }

            return view('pages.dashboard_siswa', compact('profilSiswa', 'ujianTersedia', 'ujianSelesaiIds'));

        } elseif ($user->role_id == 2) { // 2 = guru
            return view('pages.dashboard'); 
        } elseif ($user->role_id == 1) { // 1 = admin
            return view('pages.dashboard');
        }

        // Fallback jika pengguna tidak memiliki peran atau peran tidak cocok
        return view('pages.dashboard');
    }
}