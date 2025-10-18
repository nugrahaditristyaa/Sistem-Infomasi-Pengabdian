@extends('inqa.layouts.main')

@section('title', 'Rekap Pengabdian Dosen')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.bootstrap4.min.css" rel="stylesheet">
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
            cursor: pointer;
        }

        /* Zebra striping for better readability */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #fbfcfd;
        }

        .table thead th {
            font-weight: 600;
            color: #4e73df;
            border-bottom-width: 2px;
            background-color: #f8f9fc;
        }

        .badge-count {
            background-color: #4e73df;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-prodi {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-prodi.informatika {
            background-color: #1cc88a;
            color: white;
        }

        .badge-prodi.sistem-informasi {
            background-color: #36b9cc;
            color: white;
        }

        .no-column {
            width: 60px;
            text-align: center;
        }

        .aksi-column {
            text-align: center;
            width: 120px;
        }

        /* Card styling */
        .modern-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .modern-card .card-body {
            padding: 1.5rem;
        }



        /* Modal styling */
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%) !important;
            border-bottom: none;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .pengabdian-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .pengabdian-item:hover {
            border-color: #4e73df;
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.1);
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
        }

        .status-ketua {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .status-anggota {
            background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }



        /* Pagination styling */
        .pagination .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border: none;
            color: #4e73df;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            border: none;
        }

        .detail-btn {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .detail-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Rekap Pengabdian Dosen</h1>
                <p class="mb-0 text-muted">Daftar dosen dan aktivitas pengabdian mereka</p>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Data Table Card -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users mr-2"></i>Data Rekap Pengabdian Dosen
                    <span class="badge badge-secondary ml-2">{{ $dosenData->total() }} dosen</span>
                </h6>
            </div>
            <div class="card-body">
                @if ($dosenData->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dosenTable">
                            <thead>
                                <tr>
                                    <th class="no-column">No</th>
                                    <th>Nama Dosen</th>
                                    <th>NIK</th>
                                    <th>Program Studi</th>
                                    <th>Bidang Keahlian</th>
                                    <th class="text-center">Jumlah Kegiatan</th>
                                    <th class="aksi-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dosenData as $index => $dosen)
                                    <tr>
                                        <td class="no-column">{{ $dosenData->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-primary">{{ $dosen->nama }}</span>
                                                <small class="text-muted">
                                                    <i class="fas fa-id-card mr-1"></i>NIDN: {{ $dosen->nidn ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $dosen->nik }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-prodi {{ strtolower(str_replace(' ', '-', $dosen->prodi)) }}">
                                                {{ $dosen->prodi }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $dosen->bidang_keahlian ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-count">{{ $dosen->jumlah_pengabdian }}</span>
                                        </td>
                                        <td class="aksi-column">
                                            @if ($dosen->jumlah_pengabdian > 0)
                                                <button class="btn detail-btn btn-sm"
                                                    onclick="showDosenDetail('{{ $dosen->nik }}', '{{ $dosen->nama }}')">
                                                    <i class="fas fa-eye mr-1"></i>Detail
                                                </button>
                                            @else
                                                <span class="text-muted small">Tidak ada data</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Menampilkan {{ $dosenData->firstItem() }} sampai {{ $dosenData->lastItem() }}
                            dari {{ $dosenData->total() }} dosen
                        </div>
                        {{ $dosenData->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data dosen</h5>
                        <p class="text-muted">Belum ada data dosen yang tersedia dengan filter yang dipilih.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="detailModalLabel">
                        <i class="fas fa-user-circle mr-2"></i>Detail Pengabdian Dosen
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom settings
            $('#dosenTable').DataTable({
                "paging": false, // We use Laravel pagination
                "searching": true,
                "ordering": true,
                "info": false,
                "fixedHeader": true,
                "language": {
                    "search": "Cari:",
                    "searchPlaceholder": "Nama dosen, NIK, atau prodi...",
                    "zeroRecords": "Tidak ada data yang sesuai",
                    "emptyTable": "Tidak ada data tersedia"
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 6]
                    }, // No and Action columns not sortable
                    {
                        "className": "text-center",
                        "targets": [0, 5, 6]
                    }
                ],
                "order": [
                    [5, "desc"]
                ] // Sort by jumlah kegiatan descending
            });

            // Enhanced search functionality
            $('#dosenTable_filter input').addClass('form-control form-control-sm');
            $('#dosenTable_filter').addClass('mb-3');
        });

        // Show dosen detail function
        function showDosenDetail(nik, nama) {
            // Show modal with loading state
            $('#detailModal').modal('show');
            $('#detailModalLabel').html('<i class="fas fa-user-circle mr-2"></i>Detail Pengabdian: ' + nama);

            // Make AJAX request
            $.ajax({
                url: '{{ route('inqa.dosen.detail', ':nik') }}'.replace(':nik', nik),
                method: 'GET',
                success: function(response) {
                    let html = '';

                    // Dosen info header
                    html += '<div class="row mb-4">';
                    html += '<div class="col-md-6">';
                    html +=
                        '<h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Informasi Dosen</h6>';
                    html += '<table class="table table-sm table-borderless">';
                    html += '<tr><td class="font-weight-bold">Nama:</td><td>' + response.dosen.nama +
                        '</td></tr>';
                    html += '<tr><td class="font-weight-bold">NIK:</td><td>' + response.dosen.nik +
                        '</td></tr>';
                    html += '<tr><td class="font-weight-bold">NIDN:</td><td>' + (response.dosen.nidn || 'N/A') +
                        '</td></tr>';
                    html += '<tr><td class="font-weight-bold">Program Studi:</td><td>' + response.dosen.prodi +
                        '</td></tr>';
                    html += '<tr><td class="font-weight-bold">Bidang Keahlian:</td><td>' + (response.dosen
                        .bidang_keahlian || 'N/A') + '</td></tr>';
                    html += '</table>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<div class="text-center p-3 bg-primary text-white rounded">';
                    html += '<h4>' + response.pengabdian.length + '</h4>';
                    html += '<small>Total Kegiatan Pengabdian</small>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    // Pengabdian list
                    if (response.pengabdian.length > 0) {
                        html +=
                            '<h6 class="text-primary mb-3"><i class="fas fa-list mr-2"></i>Daftar Kegiatan Pengabdian</h6>';

                        response.pengabdian.forEach(function(item, index) {
                            const statusClass = item.status_anggota === 'Ketua' ? 'status-ketua' :
                                'status-anggota';
                            const tanggal = new Date(item.tanggal_pengabdian).toLocaleDateString(
                                'id-ID');

                            html += '<div class="pengabdian-item">';
                            html +=
                                '<div class="d-flex justify-content-between align-items-start mb-2">';
                            html += '<h6 class="mb-1 text-primary">' + (index + 1) + '. ' + item.judul +
                                '</h6>';
                            html += '<span class="status-badge ' + statusClass + '">' + item
                                .status_anggota + '</span>';
                            html += '</div>';
                            html += '<div class="row">';
                            html += '<div class="col-sm-6">';
                            html +=
                                '<small class="text-muted"><i class="fas fa-calendar mr-1"></i>Tanggal: ' +
                                tanggal + '</small>';
                            html += '</div>';
                            html += '<div class="col-sm-6">';
                            html +=
                                '<small class="text-muted"><i class="fas fa-money-bill mr-1"></i>Sumber Dana: ' +
                                item.sumber_dana + '</small>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });
                    } else {
                        html += '<div class="text-center py-4">';
                        html += '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                        html += '<h6 class="text-muted">Tidak ada data pengabdian</h6>';
                        html +=
                            '<p class="text-muted">Dosen ini belum memiliki kegiatan pengabdian pada periode yang dipilih.</p>';
                        html += '</div>';
                    }

                    $('#modalBody').html(html);
                },
                error: function() {
                    $('#modalBody').html(
                        '<div class="text-center py-4">' +
                        '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                        '<h6 class="text-warning">Gagal memuat data</h6>' +
                        '<p class="text-muted">Terjadi kesalahan saat memuat detail pengabdian dosen.</p>' +
                        '</div>'
                    );
                }
            });
        }
    </script>
@endpush
