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

        .table-responsive thead {
            background-color: #f8f9fc;
        }

        .table-hover tbody tr:hover {
            background-color: #f2f2f2 !important;
        }

        /* Zebra striping stronger for readability */
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
        }

        .no-column {
            width: 56px;
            text-align: center;
            white-space: nowrap;
            padding-left: 6px;
            padding-right: 6px;
        }

        .aksi-column {
            text-align: center;
            width: 120px;
        }

        /* Card padding more roomy */
        .card .card-body {
            padding: 1.5rem;
        }

        /* Badge styling */
        .badge-prodi {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
        }

        .badge-prodi.informatika {
            background-color: #4e73df;
            color: white;
        }

        .badge-prodi.sistem-informasi {
            background-color: #1cc88a;
            color: white;
        }

        .badge-count {
            background-color: #36b9cc;
            color: white;
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
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

        /* Pagination uses global/bootstrap styles to match pengabdian index */

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

        /* Align DataTables search label and input inline */
        #dtSearchContainer .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }

        #dtSearchContainer .dataTables_filter input[type="search"] {
            width: 30%;
            max-width: 380px;
        }
    </style>
@endpush

@section('content')
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
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>Data Rekap Pengabdian Dosen
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Controls row: Match Admin Layout (Filter + Search on Left) -->
                    <div class="row align-items-center mb-3">
                        <div class="col-sm-12 col-md-6 d-flex align-items-center" id="dtSearchContainerWrapper">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-2" data-toggle="modal"
                                data-target="#dosenFilterModal">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <div id="dtSearchContainer" style="flex:1"></div>
                        </div>
                        <div class="col-sm-12 col-md-6 d-flex justify-content-md-end mt-2 mt-md-0">
                            {{-- Reserved for future right-aligned controls --}}
                        </div>
                    </div>

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
                                        <th class="text-center">Jumlah Kegiatan</th>
                                        <th>Judul Terlibat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dosenData as $index => $dosen)
                                        <tr>
                                            <td class="no-column">{{ $dosenData->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="">{{ $dosen->nama }}</span>
                                                    @if ($userRole !== 'Dekan')
                                                        <small class="text-muted">{{ $dosen->prodi }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            @if ($userRole === 'Dekan')
                                                <td>
                                                    {{ $dosen->prodi }}
                                                </td>
                                            @endif
                                            <td class="text-center">
                                                {{ $dosen->jumlah_pengabdian }}
                                            </td>
                                            <td>
                                                @if ($dosen->pengabdian->count() > 0)
                                                    <ul class="mb-0 small pl-3">
                                                        @foreach ($dosen->pengabdian->unique('judul_pengabdian') as $p)
                                                            <li style="font-size:0.85rem; line-height:1.35;">
                                                                {{ $p->judul_pengabdian }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted font-italic">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- DataTables Pagination is enabled in JS, remove manual links -->
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
    @endsection

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

    <!-- Filter Modal -->
    <div class="modal fade" id="dosenFilterModal" tabindex="-1" role="dialog" aria-labelledby="dosenFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dosenFilterModalLabel">
                        <i class="fas fa-filter mr-2"></i>Filter Rekap Dosen
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="GET" action="{{ route($routeBase . '.dosen.rekap') }}">
                    <div class="modal-body">
                        <!-- Year Filter -->
                        <div class="form-group">
                            <label for="yearFilterModal">Tahun</label>
                            <select name="year" id="yearFilterModal" class="form-control">
                                <option value="all" {{ $filterYear == 'all' ? 'selected' : '' }}>Semua Tahun
                                </option>
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($userRole === 'Dekan')
                            <!-- Prodi Filter (Only for Dekan) -->
                            <div class="form-group">
                                <label for="prodiFilterModal">Program Studi</label>
                                <select name="prodi" id="prodiFilterModal" class="form-control">
                                    <option value="all" {{ $filterProdi == 'all' ? 'selected' : '' }}>Semua Prodi
                                    </option>
                                    @foreach ($prodiOptions as $prodi)
                                        <option value="{{ $prodi }}"
                                            {{ $filterProdi == $prodi ? 'selected' : '' }}>
                                            {{ $prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route($routeBase . '.dosen.rekap') }}" class="btn btn-secondary">
                            Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>

        <script>
            $(document).ready(function() {
                // Determine column configuration based on user role
                var isDekan = '{{ $userRole }}' === 'Dekan';

                // Configure DataTable columns
                // Disable sorting on No, Nama Dosen and Judul; also Program Studi when visible
                var nonSortableColumns = isDekan ? [0, 1, 2, 4] : [0, 1, 3];
                var centerAlignColumns = isDekan ? [0, 3] : [0, 2]; // No and Jumlah Kegiatan columns
                var sortColumn = isDekan ? 3 : 2; // Jumlah Kegiatan column index

                // Initialize DataTable with custom settings
                var dt = $('#dosenTable').DataTable({
                    paging: true,
                    pageLength: 10,
                    dom: 'frtip',
                    searching: true,
                    ordering: true,
                    info: true, // Show "Showing 1 to 10 of X" info
                    fixedHeader: true,
                    language: {
                        search: 'Cari:',
                        searchPlaceholder: 'Nama dosen...',
                        zeroRecords: 'Tidak ada data yang sesuai',
                        emptyTable: 'Tidak ada data tersedia',
                        paginate: {
                            first: 'Pertama',
                            last: 'Terakhir',
                            next: 'selanjutnya',
                            previous: 'sebelumnya'
                        }
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

                // Move DataTables search into our custom container and style it
                var $dtFilter = $('#dosenTable_filter');
                $dtFilter.appendTo('#dtSearchContainer');
                $dtFilter.addClass('mb-0 w-100');
                var $input = $dtFilter.find('input');
                $input.addClass('form-control form-control-sm');
                // Make search input expand nicely on small screens
                $input.attr('placeholder', 'Nama dosen...');
                $input.css({
                    maxWidth: '100%'
                });
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
