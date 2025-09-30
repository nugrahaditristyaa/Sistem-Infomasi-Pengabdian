@extends('admin.layouts.main')

@section('title', 'Edit Data Mahasiswa')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Mahasiswa</h1>
        <a href="{{ route('admin.mahasiswa.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mahasiswa.update', $mahasiswa->nim) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nim" class="form-label font-weight-bold">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim"
                                value="{{ $mahasiswa->nim }}" readonly style="background-color: #f8f9fa;">
                            <small class="form-text text-muted">NIM tidak dapat diubah</small>
                        </div>

                        <div class="form-group">
                            <label for="nama" class="form-label font-weight-bold">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama', $mahasiswa->nama) }}"
                                placeholder="Masukkan nama lengkap mahasiswa" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="prodi" class="form-label font-weight-bold">Program Studi <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('prodi') is-invalid @enderror" id="prodi" name="prodi"
                                required>
                                <option value="">-- Pilih Program Studi --</option>
                                <option value="Informatika"
                                    {{ old('prodi', $mahasiswa->prodi) == 'Informatika' ? 'selected' : '' }}>Informatika
                                </option>
                                <option value="Sistem Informasi"
                                    {{ old('prodi', $mahasiswa->prodi) == 'Sistem Informasi' ? 'selected' : '' }}>Sistem
                                    Informasi</option>
                                <option value="Teknik Komputer"
                                    {{ old('prodi', $mahasiswa->prodi) == 'Teknik Komputer' ? 'selected' : '' }}>Teknik
                                    Komputer</option>
                                <option value="Manajemen Informatika"
                                    {{ old('prodi', $mahasiswa->prodi) == 'Manajemen Informatika' ? 'selected' : '' }}>
                                    Manajemen Informatika</option>
                                <option value="Teknologi Informasi"
                                    {{ old('prodi', $mahasiswa->prodi) == 'Teknologi Informasi' ? 'selected' : '' }}>
                                    Teknologi Informasi</option>
                            </select>
                            @error('prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Perbarui Data
                            </button>
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Informasi Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>NIM:</strong></td>
                            <td>{{ $mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama Saat Ini:</strong></td>
                            <td>{{ $mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi:</strong></td>
                            <td>{{ $mahasiswa->prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terdaftar:</strong></td>
                            <td>{{ $mahasiswa->created_at ? $mahasiswa->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if ($mahasiswa->updated_at && $mahasiswa->updated_at != $mahasiswa->created_at)
                            <tr>
                                <td><strong>Terakhir Diubah:</strong></td>
                                <td>{{ $mahasiswa->updated_at ? $mahasiswa->updated_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Peringatan</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mt-2 mb-0">
                            <li>NIM tidak dapat diubah setelah data dibuat</li>
                            <li>Pastikan nama sesuai identitas resmi</li>
                            <li>Perubahan program studi memerlukan perhatian khusus</li>
                        </ul>
                    </div>
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

        .table-borderless td {
            padding: 0.5rem 0;
            border: none;
        }

        .table-borderless td:first-child {
            width: 40%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Form validation
        $('form').on('submit', function(e) {
            const nama = $('#nama').val();
            const prodi = $('#prodi').val();

            if (!nama || !prodi) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
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
