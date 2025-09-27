<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Computer Based Test - Register Siswa</title>

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url("{{ asset('template/img/smk-musasi.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .card.transparent-card {
            background-color: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: none !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .transparent-card .text-gray-900 {
            color: #fff !important;
        }
        
        .form-group {
            position: relative;
        }
        .form-control-icon {
            position: absolute;
            top: 50%;
            right: 1.2rem;
            transform: translateY(-50%);
            color: #a9a9a9;
        }
        .form-control-user.has-icon {
            padding-right: 3.5rem !important;
        }
        .toggle-password {
            cursor: pointer;
        }

        .form-control-user.custom-select {
            height: 3.1rem;
            padding-left: 0.75rem;
            border-radius: 10rem;
        }

        /* Warna default (placeholder) untuk dropdown */
        select.form-control-user {
            color: #a9a9a9;
        }
        
        /* Warna teks setelah opsi dipilih (diaktifkan oleh JavaScript) */
        select.form-control-user.is-selected {
            color: #6e707e !important; /* Diberi !important untuk memaksa perubahan */
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card o-hidden border-0 my-5 transparent-card">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Registrasi Akun</h1>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="user" action="{{ route('register') }}" method="post">
                                        @csrf
                                        @method('POST')
                                        
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control form-control-user"
                                                id="inputName" placeholder="Nama Lengkap" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="nis" class="form-control form-control-user"
                                                id="inputNis" placeholder="Masukkan NIS..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="username" class="form-control form-control-user has-icon"
                                                id="inputUsername" placeholder="Masukkan Username..." required>
                                            <i class="fas fa-user form-control-icon"></i>
                                        </div>
                                        <div class="form-group">
                                            <select name="kelas_id" class="form-control form-control-user custom-select" required>
                                                <option value="" disabled selected>Pilih Kelas</option>
                                                @foreach($kelasList as $k)
                                                    <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select name="jurusan_id" class="form-control form-control-user custom-select" required>
                                                <option value="" disabled selected>Pilih Jurusan</option>
                                                @foreach($jurusanList as $j)
                                                    <option value="{{ $j->kode_jurusan }}">{{ $j->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user has-icon"
                                                id="inputPassword" placeholder="Masukkan Password" required>
                                            <span class="form-control-icon toggle-password">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Simpan
                                        </button>
                                        <hr style="border-color: rgba(255,255,255,0.3);">
                                    </form>
                                    <hr style="border-color: rgba(255,255,255,0.3);">
                                    <div class="text-center">
                                        <a class="small" href="/" style="color: white;">Sudah Punya Akun? Login!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Kode untuk toggle password (tetap sama)
            const togglePassword = document.querySelector('.toggle-password');
            if (togglePassword) {
                const passwordInput = document.querySelector('#inputPassword');
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const eyeIcon = this.querySelector('i');
                    eyeIcon.classList.toggle('fa-eye');
                    eyeIcon.classList.toggle('fa-eye-slash');
                });
            }

            // KODE BARU UNTUK MEMPERBAIKI WARNA DROPDOWN
            const customSelects = document.querySelectorAll('.custom-select');
            customSelects.forEach(function(select) {
                select.addEventListener('change', function() {
                    if (this.value !== "") {
                        this.classList.add('is-selected');
                    } else {
                        this.classList.remove('is-selected');
                    }
                });
            });
        });
    </script>
</body>
</html>