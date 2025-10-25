<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Computer Based Test - Login</title>

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* CSS untuk .bg-login-image telah dihapus */

        /* ============================================== */
        /* CSS UNTUK IKON PADA INPUT FIELD */
        /* ============================================== */
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
    </style>
</head>

<body class="bg-gradient-primary">

    @if ($errors->any())
        <script>
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "@foreach($errors->all() as $error) {{ $error }} {{ $loop->last ? '.' : ',' }} @endforeach",
                icon: "error"
            });
        </script>
    @endif

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang di Sistem CBT</h1>
                                    </div>
                                    <form class="user" action="{{ route('login.authenticate') }}" method="post">
                                        @csrf
                                        @method('POST')
                                        
                                        <div class="form-group">
                                            <input type="text" name="username" class="form-control form-control-user has-icon"
                                                id="inputUsername"
                                                placeholder="Masukan Username...">
                                            <i class="fas fa-user form-control-icon"></i>
                                        </div>

                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user has-icon"
                                                id="inputPassword" 
                                                placeholder="Masukan Password">
                                            <span class="form-control-icon toggle-password">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                    </form>
                                    <hr>
                                    {{-- <div class="text-center">
                                        <a class="small" href="/register">Buat Akun!</a>
                                    </div> --}}
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
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#inputPassword');
            
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const eyeIcon = this.querySelector('i');
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        });
    </script>

</body>

</html>