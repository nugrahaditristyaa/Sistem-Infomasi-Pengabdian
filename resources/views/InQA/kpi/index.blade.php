@extends('inqa.layouts.main')

@section('title', 'Data KPI - InQA Dashboard')

@push('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 2px;
        }

        .table th {
            background-color: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #5a5c69;
        }

        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .kode-badge {
            background-color: #4e73df;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .target-value {
            font-weight: 600;
            color: #1cc88a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-primary"></i>Data KPI
            </h1>
            <small class="text-muted">Kelola Key Performance Indicator (KPI) untuk monitoring kinerja</small>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif



        <!-- Main Content Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Daftar KPI
                </h6>
                <span class="badge badge-primary badge-pill">
                    {{ $kpis->count() }} Data
                </span>
            </div>

            <div class="card-body">
                @if ($kpis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="8%" class="text-center">No</th>
                                    <th width="15%">Kode KPI</th>
                                    <th width="40%">Nama Indikator</th>
                                    <th width="12%" class="text-center">Target</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kpis as $index => $kpi)
                                    <tr>
                                        <td class="text-center">
                                            <span class="text-muted">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <span class="kode-badge">{{ $kpi->kode }}</span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $kpi->nama_indikator }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="target-value">{{ number_format($kpi->target, 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $kpi->satuan }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('inqa.kpi.edit', $kpi->id_kpi) }}"
                                                class="btn btn-sm btn-warning btn-action" title="Edit KPI">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-chart-bar fa-4x text-gray-300"></i>
                        </div>
                        <h5 class="text-gray-600 mb-3">Belum Ada Data KPI</h5>
                        <p class="text-gray-500 mb-4">
                            Data KPI (Key Performance Indicator) belum tersedia.<br>
                            Hubungi administrator untuk menambahkan data KPI.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Catatan:</strong> InQA hanya dapat mengedit KPI yang sudah ada, tidak dapat menambah
                            atau menghapus KPI.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#dataTable').DataTable({
                "pageLength": 10,
                "order": [
                    [1, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 5]
                    }, // Disable sorting for No and Actions columns
                    {
                        "searchable": false,
                        "targets": [0, 5]
                    } // Disable search for No and Actions columns
                ],
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data yang ditampilkan",
                    "paginate": {
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "emptyTable": "Tidak ada data KPI yang tersedia"
                }
            });

            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Initialize tooltips
            $('[title]').tooltip();
        });
    </script>
@endpush
