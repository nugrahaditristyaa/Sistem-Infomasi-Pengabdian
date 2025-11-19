@extends('admin.layouts.main')

@section('title', 'Tambah Data Mahasiswa')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Mahasiswa</h1>
        <a href="{{ route('admin.mahasiswa.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim"
                                name="nim" value="{{ old('nim') }}" placeholder="Masukkan NIM mahasiswa" required>
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">NIM harus unik dan tidak boleh sama dengan yang sudah
                                ada</small>
                        </div>

                        <div class="form-group">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap mahasiswa"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prodi" class="form-label ">Program Studi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prodi') is-invalid @enderror" id="prodi"
                                name="prodi" value="{{ old('prodi') }}" placeholder="Masukkan program studi" required>
                            @error('prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Data
                            </button>
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .form-label {
            color: #5a5c69;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto format NIM input
        $('#nim').on('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Form validation
        $('form').on('submit', function(e) {
            const nim = $('#nim').val();
            const nama = $('#nama').val();
            const prodi = $('#prodi').val();

            if (!nim || !nama || !prodi) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Validate NIM length (typically 8-15 digits)
            if (nim.length < 8 || nim.length > 15) {
                e.preventDefault();
                Swal.fire({
                    title: 'NIM Tidak Valid',
                    text: 'NIM harus terdiri dari 8-15 digit angka',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                $('#nim').focus();
                return false;
            }
        });

        // Auto capitalize nama
        $('#nama').on('input', function() {
            const words = this.value.split(' ');
            const capitalizedWords = words.map(word => {
                if (word.length > 0) {
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                }
                return word;
            });
            this.value = capitalizedWords.join(' ');
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
