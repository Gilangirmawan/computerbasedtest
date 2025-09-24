@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Administrator Baru</h1>
        <a href="{{ route('administrator.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('administrator.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ========================================================= --}}
                {{-- PERBAIKAN PADA INPUT PASSWORD --}}
                {{-- ========================================================= --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="input-group-append">
                            <span class="input-group-text" style="cursor: pointer;" id="toggle-password">
                                <i class="fa fa-eye" id="eye-icon"></i>
                            </span>
                        </div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        <div class="input-group-append">
                             <span class="input-group-text" style="cursor: pointer;" id="toggle-password-confirmation">
                                <i class="fa fa-eye" id="eye-icon-confirmation"></i>
                            </span>
                        </div>
                    </div>
                </div>
                {{-- ========================================================= --}}
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_superadmin" id="is_superadmin">
                    <label class="form-check-label" for="is_superadmin">
                        Jadikan sebagai Super Admin
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
{{-- ========================================================= --}}
{{-- SCRIPT BARU UNTUK FITUR LIHAT PASSWORD --}}
{{-- ========================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk field password utama
        const togglePassword = document.querySelector('#toggle-password');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eye-icon');

        togglePassword.addEventListener('click', function () {
            // Ganti tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Ganti ikon mata
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        // Logika untuk field konfirmasi password
        const togglePasswordConfirmation = document.querySelector('#toggle-password-confirmation');
        const passwordConfirmationInput = document.querySelector('#password_confirmation');
        const eyeIconConfirmation = document.querySelector('#eye-icon-confirmation');

        togglePasswordConfirmation.addEventListener('click', function () {
            // Ganti tipe input
            const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmationInput.setAttribute('type', type);
            // Ganti ikon mata
            eyeIconConfirmation.classList.toggle('fa-eye-slash');
        });
    });
</script>
@endpush