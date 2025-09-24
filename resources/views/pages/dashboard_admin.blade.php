@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Administrator</h1>
    </div>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Administrator</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahAdmin }} Akun</div>
                            @if(Auth::user()->is_superadmin)
                                <a href="{{ route('administrator.index') }}" class="btn btn-danger btn-sm mt-3">
                                    Kelola <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahGuru }} Guru</div>
                            <a href="{{ route('guru.index') }}" class="btn btn-warning btn-sm mt-3 text-white">
                                Kelola <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahSiswa }} Siswa</div>
                             <a href="{{ route('siswa.index') }}" class="btn btn-info btn-sm mt-3">
                                Kelola <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection