@extends('dekan.layouts.main')

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
            <a href="{{ route($routeBase . '.dosen.rekap.export', array_filter(['year' => request('year'), 'prodi' => request('prodi')])) }}"
                class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-file-excel mr-1"></i> Ekspor CSV
            </a>
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

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter mr-2"></i>Filter Data
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route($routeBase . '.dosen.rekap') }}" class="form-inline">
                    <!-- Year Filter -->
                    <div class="form-group mr-3 mb-2">
                        <label for="yearFilter" class="mr-2">Tahun:</label>
                        <select name="year" id="yearFilter" class="form-control form-control-sm"
                            onchange="this.form.submit()">
                            <option value="all" {{ $filterYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($userRole === 'Dekan')
                        <!-- Prodi Filter (Only for Dekan) -->
                        <div class="form-group mr-3 mb-2">
                            <label for="prodiFilter" class="mr-2">Program Studi:</label>
                            <select name="prodi" id="prodiFilter" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                <option value="all" {{ $filterProdi == 'all' ? 'selected' : '' }}>Semua Prodi</option>
                                @foreach ($prodiOptions as $prodi)
                                    <option value="{{ $prodi }}" {{ $filterProdi == $prodi ? 'selected' : '' }}>
                                        {{ $prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Reset Button -->
                    <div class="form-group mb-2">
                        <a href="{{ route($routeBase . '.dosen.rekap') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

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
                                    @if ($userRole === 'Dekan')
                                        <th>Program Studi</th>
                                    @endif
                                    <th class="text-center">Jumlah Kegiatan ▼</th>
                                    <th>Judul Terlibat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dosenData as $index => $dosen)
                                    @php
                                        $judulTerlibat = $dosen->pengabdian->pluck('judul')->unique()->implode('; ');
                                    @endphp
                                    <tr
                                        onclick="onRowClick('{{ $dosen->nik }}', '{{ $dosen->nama }}', {{ (int) ($dosen->jumlah_pengabdian > 0) }})">
                                        <td class="no-column">{{ $dosenData->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-primary">{{ $dosen->nama }}</span>
                                                @if ($userRole !== 'Dekan')
                                                    <small class="text-muted">{{ $dosen->prodi }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        @if ($userRole === 'Dekan')
                                            <td>
                                                <span
                                                    class="badge badge-prodi {{ strtolower(str_replace(' ', '-', $dosen->prodi)) }}">
                                                    {{ $dosen->prodi }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            <span class="badge badge-count">{{ $dosen->jumlah_pengabdian }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $judulTerlibat ?: '-' }}</span>
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
            // Determine column configuration based on user role
            var isDekan = '{{ $userRole }}' === 'Dekan';

            // Configure DataTable columns
            var nonSortableColumns = isDekan ? [0, 4] : [0, 3]; // No and Judul columns
            var centerAlignColumns = isDekan ? [0, 3] : [0, 2]; // No and Jumlah Kegiatan columns
            var sortColumn = isDekan ? 3 : 2; // Jumlah Kegiatan column index

            // Initialize DataTable with custom settings
            $('#dosenTable').DataTable({
                paging: false, // We use Laravel pagination
                searching: true,
                ordering: true,
                info: false,
                fixedHeader: true,
                language: {
                    search: 'Cari:',
                    searchPlaceholder: 'Nama dosen...',
                    zeroRecords: 'Tidak ada data yang sesuai',
                    emptyTable: 'Tidak ada data tersedia'
                },
                columnDefs: [{
                        orderable: false,
                        targets: nonSortableColumns
                    },
                    {
                        className: 'text-center',
                        targets: centerAlignColumns
                    }
                ],
                order: [
                    [sortColumn, 'desc']
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
                url: '{{ route($routeBase . '.dosen.detail', ':nik') }}'.replace(':nik', nik),
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

        // Row click handler to open modal when data exists
        function onRowClick(nik, nama, hasData) {
            if (hasData) {
                showDosenDetail(nik, nama);
            }
        }
    </script>
@endpush
