<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SI Pengabdian - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.css') }}" rel="stylesheet">
</head>

<body>

    <div class="container h-100">

        <div class="row justify-content-center align-items-center" style="height: 100vh;">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h1 text-gray-900 mb-4">Login</h1>
                                    </div>

                                    {{-- Alert untuk error validasi --}}
                                    @if ($errors->any() || session('error') || session('login_error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            @if (session('login_error'))
                                                <strong>Login Gagal!</strong><br>
                                                {{ session('login_error') }}
                                            @elseif (session('error'))
                                                <strong>Error!</strong><br>
                                                {{ session('error') }}
                                            @else
                                                <strong>Validasi Gagal!</strong><br>
                                                <ul class="mb-0 mt-2">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Notifikasi sukses menggunakan SweetAlert --}}
                                    @if (session('success'))
                                        <script>
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: '{{ session('success') }}',
                                                showConfirmButton: false,
                                                timer: 2000
                                            });
                                        </script>
                                    @endif

                                    <form class="user" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text"
                                                class="form-control form-control-user @error('username') is-invalid @enderror"
                                                id="username" name="username" value="{{ old('username') }}"
                                                placeholder="Masukkan username..." autofocus required>
                                            @error('username')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <div id="username-feedback" class="invalid-feedback d-none">
                                                <i class="fas fa-exclamation-circle mr-1"></i><span
                                                    id="username-message"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password"
                                                class="form-control form-control-user @error('password') is-invalid @enderror"
                                                id="password" name="password" placeholder="Masukkan password..."
                                                required>
                                            @error('password')
                                                <div class="invalid-feedback d-block">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <div id="password-feedback" class="invalid-feedback d-none">
                                                <i class="fas fa-exclamation-circle mr-1"></i><span
                                                    id="password-message"></span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>

                                        <div class="security-info">
                                            <i class="fas fa-shield-alt mr-2"></i>
                                            <strong>Keamanan:</strong> Maksimal 5 percobaan login dalam 1 menit.
                                            Akun akan dikunci sementara jika melebihi batas.
                                        </div>

                                        <hr>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Validasi real-time untuk username
            $('#username').on('input blur', function() {
                const username = $(this).val().trim();
                const $feedback = $('#username-feedback');
                const $message = $('#username-message');

                if (username.length === 0) {
                    $(this).removeClass('is-valid is-invalid');
                    $feedback.addClass('d-none');
                } else if (username.length < 3) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $message.text('Username minimal 3 karakter');
                    $feedback.removeClass('d-none').addClass('d-block');
                } else if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $message.text(
                        'Username hanya boleh mengandung huruf, angka, titik, underscore, dan dash');
                    $feedback.removeClass('d-none').addClass('d-block');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $feedback.addClass('d-none');
                }
            });

            // Validasi real-time untuk password
            $('#password').on('input blur', function() {
                const password = $(this).val();
                const $feedback = $('#password-feedback');
                const $message = $('#password-message');

                if (password.length === 0) {
                    $(this).removeClass('is-valid is-invalid');
                    $feedback.addClass('d-none');
                } else if (password.length < 6) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $message.text('Password minimal 6 karakter');
                    $feedback.removeClass('d-none').addClass('d-block');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $feedback.addClass('d-none');
                }
            });

            // Validasi form sebelum submit
            $('form.user').on('submit', function(e) {
                let isValid = true;
                const username = $('#username').val().trim();
                const password = $('#password').val();

                // Reset validasi
                $('.form-control').removeClass('is-invalid is-valid');
                $('.invalid-feedback').addClass('d-none');

                // Validasi username
                if (username.length === 0) {
                    $('#username').addClass('is-invalid');
                    $('#username-message').text('Username wajib diisi');
                    $('#username-feedback').removeClass('d-none');
                    isValid = false;
                } else if (username.length < 3) {
                    $('#username').addClass('is-invalid');
                    $('#username-message').text('Username minimal 3 karakter');
                    $('#username-feedback').removeClass('d-none');
                    isValid = false;
                } else if (!/^[a-zA-Z0-9._-]+$/.test(username)) {
                    $('#username').addClass('is-invalid');
                    $('#username-message').text('Username tidak valid');
                    $('#username-feedback').removeClass('d-none');
                    isValid = false;
                }

                // Validasi password
                if (password.length === 0) {
                    $('#password').addClass('is-invalid');
                    $('#password-message').text('Password wajib diisi');
                    $('#password-feedback').removeClass('d-none');
                    isValid = false;
                } else if (password.length < 6) {
                    $('#password').addClass('is-invalid');
                    $('#password-message').text('Password minimal 6 karakter');
                    $('#password-feedback').removeClass('d-none');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    // Fokus ke field pertama yang error
                    $('.is-invalid').first().focus();

                    // Tampilkan pesan error umum
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali input Anda',
                        showConfirmButton: true
                    });
                }
            });

            // Auto-hide alert setelah 5 detik
            if ($('.alert').length > 0) {
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            }

            // Animasi loading saat submit
            $('form.user').on('submit', function() {
                if (!$(this).hasClass('was-validated') || $(this)[0].checkValidity()) {
                    const $btn = $(this).find('button[type="submit"]');
                    $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
                    $btn.prop('disabled', true);
                }
            });
        });
    </script>

    <style>
        /* Custom styles untuk validasi */
        .form-control.is-valid {
            border-color: #1cc88a;
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25);
        }

        .form-control.is-invalid {
            border-color: #e74a3b;
            box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.25);
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .alert {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .alert-danger {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
            color: white;
            border-left: 4px solid #a93226;
        }

        .alert-danger .fas {
            animation: bounce 0.6s ease-in-out;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        /* Security indicator */
        .security-info {
            background: rgba(78, 115, 223, 0.1);
            border: 1px solid rgba(78, 115, 223, 0.2);
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #5a5c69;
        }

        .security-info .fas {
            color: #4e73df;
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Loading animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .col-lg-6 {
                padding: 1rem !important;
            }

            .h1 {
                font-size: 1.75rem !important;
            }
        }
    </style>

</body>

</html>
