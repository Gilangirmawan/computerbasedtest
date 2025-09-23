@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Administrator</h1>
        <a href="{{ route('administrator.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('administrator.update', $administrator->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $administrator->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $administrator->username) }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <hr>
                <p class="text-muted small">Kosongkan password jika tidak ingin mengubahnya.</p>
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <hr>
                <div class="form-check mb-3">
                    <input class="form-check-input @error('is_superadmin') is-invalid @enderror" type="checkbox" name="is_superadmin" id="is_superadmin"
                        {{ old('is_superadmin', $administrator->is_superadmin) ? 'checked' : '' }}
                        {{ Auth::user()->id === $administrator->id ? 'disabled' : '' }}
                    >
                    <label class="form-check-label" for="is_superadmin">
                        Jadikan sebagai Super Admin
                    </label>
                    @if(Auth::user()->id === $administrator->id)
                        <small class="form-text text-muted">Anda tidak dapat mengubah status Super Admin akun Anda sendiri.</small>
                    @endif
                     @error('is_superadmin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection