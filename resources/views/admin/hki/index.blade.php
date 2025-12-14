@extends('admin.layouts.main')

@section('title', 'Data HKI')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Luaran HKI</h1>
    </div>

    <div class="container-fluid">

        {{-- Filter form removed per UI simplification: filters and the unused searchbox were removed. --}}

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data HKI</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="hkiTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Pendaftaran</th>
                                <th>Tgl Permohonan</th>
                                <th>Judul Ciptaan</th>
                                <th>Pemegang Hak Cipta</th>
                                <th>Jenis Ciptaan</th>
                                <th>Anggota Pencipta</th>
                                <th>Judul Pengabdian Terkait</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hki as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->no_pendaftaran }}</td>
                                    <td>{{ optional($row->tgl_permohonan)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $row->judul_ciptaan }}
                                        <div><small
                                                class="text-muted">{{ $row->luaran->jenisLuaran->nama_jenis_luaran ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $row->pemegang_hak_cipta }}</td>
                                    <td>{{ $row->jenis_ciptaan }}</td>
                                    <td class="td-list">
                                        @if (!empty($row->dosen) && $row->dosen->isNotEmpty())
                                            @php $angg = $row->dosen; @endphp
                                            <ol class="mb-0 pl-3">
                                                @foreach ($angg->take(2) as $d)
                                                    <li>{{ $d->nama }}</li>
                                                @endforeach
                                            </ol>
                                            @if ($angg->count() > 2)
                                                <a href="#" data-toggle="modal"
                                                    data-target="#anggotaModal{{ $loop->index }}">+{{ $angg->count() - 2 }}
                                                    lainnya</a>

                                                <!-- Modal for anggota pencipta -->
                                                <div class="modal fade" id="anggotaModal{{ $loop->index }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="anggotaModalLabel{{ $loop->index }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="anggotaModalLabel{{ $loop->index }}">Anggota
                                                                    Pencipta</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ol class="mb-0 pl-3">
                                                                    @foreach ($angg as $d)
                                                                        <li>{{ $d->nama }}</li>
                                                                    @endforeach
                                                                </ol>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($row->luaran->pengabdian))
                                            <a
                                                href="{{ route('admin.pengabdian.show', $row->luaran->pengabdian->id_pengabdian) }}">
                                                {{ $row->luaran->pengabdian->judul_pengabdian }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="aksi-column text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.hki.show', $row->id_detail_hki ?? $row->id) }}"
                                                class="btn btn-sm btn-info" title="Detail HKI"><i
                                                    class="fas fa-eye"></i></a>
                                            @if ($row->dokumen)
                                                <a href="{{ Storage::url($row->dokumen->path_file) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary" title="Unduh Dokumen"><i
                                                        class="fas fa-download"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.bootstrap4.min.css" rel="stylesheet">

    <style>
        /* Align with Pengabdian index styles */
        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        .table-responsive thead {
            background-color: #f8f9fc;
        }

        /* Zebra striping to match Pengabdian index */
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

        .td-list ul {
            margin-bottom: 0;
            padding-left: 0;
            list-style-type: none;
        }

        .td-list ul li {
            font-size: 0.85rem;
            padding-bottom: 2px;
        }

        /* Make ordered lists match Pengabdian list styling (font size and spacing) */
        .td-list ol {
            margin-bottom: 0;
            padding-left: 1.2rem;
            /* keep numbers visible and aligned */
        }

        .td-list ol li {
            font-size: 0.85rem;
            padding-bottom: 2px;
        }

        .badge-hki {
            background-color: #1cc88a;
            color: #fff;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/fixedHeader.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable mainly to enable FixedHeader and client-side search/sort features
            var table = $('#hkiTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "ordering": true,
                "searching": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    },
                },
                // Allow the DataTables length selector to be visible (show 10 entri selector)
                "lengthChange": true,
                "fixedHeader": true,
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                "order": [
                    [1, 'asc']
                ]
            });

            table.on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
            // Popovers replaced with modals for mobile-friendly behavior (no popover JS needed)
        });
    </script>
@endpush
