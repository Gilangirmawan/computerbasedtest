@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ganti Password</h1>
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
            @if (session('swal_success'))
                {{-- Notifikasi sukses akan ditangani oleh skrip di bawah --}}
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                {{-- ========================================================= --}}
                {{-- PERBAIKAN PADA SEMUA INPUT PASSWORD --}}
                {{-- ========================================================= --}}
                <div class="mb-3">
                    <label for="current_password" class="form-label">Password Saat Ini *</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        <div class="input-group-append">
                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="input-group-append">
                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                     @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru *</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        <div class="input-group-append">
                             <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                {{-- ========================================================= --}}

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
{{-- SweetAlert2 CDN jika belum ada di layout utama --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- ========================================================= --}}
{{-- SCRIPT BARU UNTUK FITUR LIHAT PASSWORD --}}
{{-- ========================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk notifikasi sukses
        @if (session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('swal_success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        // Logika untuk toggle lihat password
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                // Cari input password yang berada satu level di atasnya lalu cari input di dalamnya
                const passwordInput = this.closest('.input-group').querySelector('input');
                // Cari ikon mata di dalam span ini
                const eyeIcon = this.querySelector('i');
                
                // Ganti tipe input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ganti ikon mata
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        });
    });
</script>
@endpush