<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\SiswaStatusController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\IkutUjianController;
use App\Http\Controllers\DashboardController;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Mapel;

//Auth Routes
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'registerView'])->name('registerView');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware('role:admin,guru,siswa')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::middleware('role:admin')->group(function(){
    // Routes for guru
    // Route::resource('guru', GuruController::class);
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/create', [GuruController::class, 'create'])->name('guru.create');
    Route::post('/guru/create', [GuruController::class, 'tambah'])->name('guru.tambah');
    Route::get('/guru/edit/{id}', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/delete/{id}', [GuruController::class, 'delete'])->name('guru.delete');
    Route::get('/guru/import', [GuruController::class, 'importCreate'])->name('guru.import.create');
    Route::post('/guru/import', [GuruController::class, 'importStore'])->name('guru.import.store');

    // Routes for Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'tambah'])->name('siswa.tambah');
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'delete'])->name('siswa.delete');

    // Routes for Siswa Status
    Route::put('/siswa/{id}/approve', [SiswaStatusController::class, 'approve'])->name('siswa.approve');
    Route::put('/siswa/{id}/reject', [SiswaStatusController::class, 'reject'])->name('siswa.reject');

    //Routes for Mapel
    // Route::resource('mapel', MapelController::class);
    Route::get('/mapel', [MapelController::class, 'index'])->name('mapel.index');
    Route::get('/mapel/create', [MapelController::class, 'create'])->name('mapel.create');
    Route::post('/mapel/create', [MapelController::class, 'tambah'])->name('mapel.tambah');
    Route::get('/mapel/edit/{id}', [MapelController::class, 'edit'])->name('mapel.edit');
    Route::put('/mapel/{id}', [MapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/delete/{id}', [MapelController::class, 'delete'])->name('mapel.delete');

    //Route Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::get('/jurusan/create', [JurusanController::class, 'create'])->name('jurusan.create');
    Route::post('/jurusan/create', [JurusanController::class, 'tambah'])->name('jurusan.tambah');
    Route::get('/jurusan/edit/{id}', [JurusanController::class, 'edit'])->name('jurusan.edit');
    Route::put('/jurusan/{id}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/delete/{id}', [JurusanController::class, 'delete'])->name('jurusan.delete');

    //Route Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas/create', [KelasController::class, 'tambah'])->name('kelas.tambah');
    Route::get('/kelas/edit/{id}', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/delete/{id}', [KelasController::class, 'delete'])->name('kelas.delete');
});


//route fitur guru
Route::middleware('role:guru')->group(function(){
    // Route::resource('banksoal', BankSoalController::class);
    Route::get('/banksoal', [BankSoalController::class, 'index'])->name('banksoal.index');
    Route::get('/banksoal/create', [BankSoalController::class, 'create'])->name('banksoal.create');
    Route::post('/banksoal/create', [BankSoalController::class, 'tambah'])->name('banksoal.tambah');
    Route::get('/banksoal/edit/{id}', [BankSoalController::class, 'edit'])->name('banksoal.edit');
    Route::put('/banksoal/{id}', [BankSoalController::class, 'update'])->name('banksoal.update');
    Route::delete('/banksoal/delete/{id}', [BankSoalController::class, 'delete'])->name('banksoal.delete');
    Route::get('/banksoal/import', [BankSoalController::class, 'importCreate'])->name('banksoal.import.create');
    Route::post('/banksoal/import', [BankSoalController::class, 'importStore'])->name('banksoal.import.store');

    // Route Ujian
    Route::resource('ujian', UjianController::class);
    Route::get('/ujian', [UjianController::class, 'index'])->name('ujian.index');
    Route::get('/ujian/create', [UjianController::class, 'create'])->name('ujian.create');
    Route::post('/ujian/create', [UjianController::class, 'tambah'])->name('ujian.tambah');
    Route::get('/ujian/edit/{id}', [UjianController::class, 'edit'])->name('ujian.edit');
    Route::put('/ujian/{id}', [UjianController::class, 'update'])->name('ujian.update');
    Route::delete('/ujian/delete/{id}', [UjianController::class, 'delete'])->name('ujian.delete');

    // 🚨 Ganti path detail jadi /ujian/{ujian}/detail supaya tidak bentrok dengan pola /ujian/{id} milik resource
    Route::get('/ujian/{ujian}/detail', [UjianController::class, 'detail'])->name('ujian.detail');
    Route::get('/ujian/{ujian}/export', [UjianController::class, 'export'])->name('ujian.export');
    Route::get('/hasil-ujian/{ikutUjian}/lihat', [UjianController::class, 'lihatHasil'])->name('ujian.lihatHasil');
    Route::delete('/hasil-ujian/{ikutUjian}/batalkan', [UjianController::class, 'batalkanHasil'])->name('ujian.batalkanHasil');
});

// routes siswa
Route::middleware('role:siswa')->group(function () {
    Route::get('/ikut_ujian', [UjianController::class, 'index'])->name('ikutujian.index');
    Route::get('/ikut_ujian', [IkutUjianController::class, 'daftar'])->name('ikutujian.daftar');
    Route::post('/ikut_ujian/cek-token', [IkutUjianController::class, 'cekToken'])->name('ikutujian.cekToken');
    Route::get('/ikut_ujian/{id}/mulai', [IkutUjianController::class, 'mulai'])->name('ikutujian.mulai');
    Route::post('/ikut_ujian/{id}/selesai', [IkutUjianController::class, 'selesaiUjian'])->name('ikutujian.selesai');

    // route simpan jawaban
    Route::post('/jawaban/simpan', [IkutUjianController::class, 'simpanJawaban'])->name('jawaban.simpan');
});