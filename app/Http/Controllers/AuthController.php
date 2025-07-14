<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
     public function login()
    {
        return view('pages.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|alpha_dash',
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $userStatus = Auth::user()->status;
            // dd(Auth::user());
            if($userStatus == 'submitted') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda belum disetujui. Silakan tunggu konfirmasi dari admin.',
                ]);
            }else if ($userStatus == 'rejected') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut.',
                ]);
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'Terjadi Kesalahan, Periksa Kembali username dan password Anda.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/');
    }

    public function registerView()
    {
        $kelasList = \App\Models\Kelas::all();
        $jurusanList = \App\Models\Jurusan::all();

        return view('pages.auth.register', compact('kelasList', 'jurusanList'));
    }

    public function register(Request $request)
    {
        // Validasi input untuk siswa
        $request->validate([
            'name' => ['required', 'string'],
            'nis' => ['required', 'string', 'unique:siswa'],
            'username' => ['required', 'alpha_dash'], 
            'password' => ['required'],
            'kelas_id' => 'required|exists:kelas,id',
            'jurusan_id' => 'required|exists:jurusan,kode_jurusan',
        ]);

    // Simpan data user siswa
    $user = new User();
    $user->name = $request->input('name');
    $user->username = $request->input('username'); 
    $user->password = Hash::make($request->input('password'));
    $user->status = 'submitted'; // default status
    $user->role_id = 3; // 3 = siswa

    $user->save();

    $siswa = new Siswa();
    $siswa->user_id = $user->id;
    $siswa->nis = $request->input('nis');
    $siswa->nama = $request->input('name');
    $siswa->username = $request->input('username');
    $siswa->password = Hash::make($request->input('password'));
    $siswa->kelas_id = $request->input('kelas_id'); // ✅ ini benar
    $siswa->jurusan_id = $request->input('jurusan_id'); // ✅ ini benar
    $siswa->save();

    return redirect()->route('login')->with('success', 'Pendaftaran berhasil, silakan login.');
    }
}
