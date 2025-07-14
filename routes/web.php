<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaStatusController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Mapel;

//Auth Routes
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'registerView'])->name('registerView');
Route::post('/register', [AuthController::class, 'register'])->name('register');


Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

// Routes for Guru
Route::resource('guru', GuruController::class);
Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
Route::get('/guru/create', [GuruController::class, 'create'])->name('guru.create');
Route::post('/guru/create', [GuruController::class, 'tambah'])->name('guru.tambah');
Route::get('/guru/edit/{id}', [GuruController::class, 'edit'])->name('guru.edit');
Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
Route::delete('/guru/delete/{id}', [GuruController::class, 'delete'])->name('guru.delete');

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