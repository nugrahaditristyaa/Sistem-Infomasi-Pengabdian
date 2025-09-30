@extends('admin.layouts.main')

@section('title', 'Detail Mahasiswa - ' . $mahasiswa->nama)

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Mahasiswa</h1>
        <div>
            <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->nim) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Data
            </a>
            <a href="{{ route('admin.mahasiswa.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-graduate"></i> Profil Mahasiswa
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; font-size: 3rem; color: white;">
                            {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="font-weight-bold text-gray-800">{{ $mahasiswa->nama }}</h5>
                    <p class="text-muted mb-1">NIM: {{ $mahasiswa->nim }}</p>
                    <span class="badge badge-info badge-pill px-3 py-2">{{ $mahasiswa->prodi ?? '-' }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-line"></i> Statistik Kegiatan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="h4 mb-0 font-weight-bold text-primary">
                                {{ $mahasiswa->pengabdian->count() }}
                            </div>
                            <div class="text-xs font-weight-bold text-uppercase tracking-wide text-muted">
                                Total Pengabdian
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-success">
                                {{ $mahasiswa->pengabdian->where('status', 'selesai')->count() }}
                            </div>
                            <div class="text-xs text-success">Selesai</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                {{ $mahasiswa->pengabdian->where('status', 'berlangsung')->count() }}
                            </div>
                            <div class="text-xs text-warning">Berlangsung</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informasi Dasar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">NIM:</td>
                                    <td>{{ $mahasiswa->nim }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Nama Lengkap:</td>
                                    <td>{{ $mahasiswa->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Program Studi:</td>
                                    <td>
                                        <span class="badge badge-info">{{ $mahasiswa->prodi ?? '-' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">Terdaftar:</td>
                                    <td>{{ $mahasiswa->created_at ? $mahasiswa->created_at->format('d F Y, H:i') : '-' }}</td>
                                </tr>
                                @if($mahasiswa->updated_at && $mahasiswa->updated_at != $mahasiswa->created_at)
                                <tr>
                                    <td class="font-weight-bold">Terakhir Diubah:</td>
                                    <td>{{ $mahasiswa->updated_at ? $mahasiswa->updated_at->format('d F Y, H:i') : '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        <span class="badge badge-success">Aktif</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengabdian History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Riwayat Kegiatan Pengabdian
                    </h6>
                    <span class="badge badge-primary badge-pill">{{ $mahasiswa->pengabdian->count() }} kegiatan</span>
                </div>
                <div class="card-body">
                    @if($mahasiswa->pengabdian->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Pengabdian</th>
                                        <th>Tahun</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswa->pengabdian as $pengabdian)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="text-sm font-weight-bold">{{ $pengabdian->judul }}</div>
                                                <div class="text-xs text-muted">{{ $pengabdian->sumber_dana }}</div>
                                            </td>
                                            <td>{{ $pengabdian->tahun }}</td>
                                            <td>
                                                @switch($pengabdian->status)
                                                    @case('selesai')
                                                        <span class="badge badge-success">Selesai</span>
                                                        @break
                                                    @case('berlangsung')
                                                        <span class="badge badge-warning">Berlangsung</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $pengabdian->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.pengabdian.show', $pengabdian->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada kegiatan pengabdian</h5>
                            <p class="text-muted">Mahasiswa ini belum terlibat dalam kegiatan pengabdian apapun.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->nim) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Data
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Hapus Data
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" action="{{ route('admin.mahasiswa.destroy', $mahasiswa->nim) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('styles')
    <style>
        .profile-avatar .bg-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .table-borderless td {
            padding: 0.75rem 0;
            border: none;
            vertical-align: top;
        }
        
        .badge-pill {
            font-size: 0.875rem;
        }
        
        .tracking-wide {
            letter-spacing: 0.05em;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .btn-outline-primary:hover {
            background-color: #4e73df;
            border-color: #4e73df;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus data mahasiswa "${{{ $mahasiswa->nama }}}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush