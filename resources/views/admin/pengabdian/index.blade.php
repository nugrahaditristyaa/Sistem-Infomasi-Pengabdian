@extends('admin.layouts.main')

@section('title', 'Data Pengabdian')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.bootstrap4.min.css" rel="stylesheet">
    {{-- Export buttons removed per request: buttons CSS/JS and initialization removed below --}}
    <!-- Responsive removed to avoid duplicate search; table remains responsive via wrapper -->
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-responsive thead {
            background-color: #f8f9fc;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
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

        .judul-pengabdian {
            max-width: 300px;
            white-space: normal;
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

        /* Ordered lists styling to match HKI and ensure numeric list for anggota/mahasiswa */
        .td-list ol {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }

        .td-list ol li {
            font-size: 0.85rem;
            padding-bottom: 2px;
        }

        .text-right-numeric {
            text-align: right !important;
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

        .btn-group .btn,
        .btn-group form {
            margin-right: 5px;
        }

        .btn-group>*:last-child {
            margin-right: 0;
        }

        .badge-hki {
            background-color: #1cc88a;
            color: #fff;
            font-weight: 600;
        }

        /* Card padding more roomy */
        .card .card-body {
            padding: 1.5rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f2f2f2 !important;
            /* Tambahkan !important di sini */
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pengabdian</h1>
        <div class="d-flex">
            <a href="{{ route('admin.pengabdian.create') }}"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data Pengabdian
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Data Rekap Pengabdian
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Controls row: place action buttons and table controls here --}}
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                            data-target="#pengabdianFilterModal">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="no-column">No</th>
                                    <th>Judul Pengabdian</th>
                                    <th>Tanggal Pengabdian</th>
                                    <th>Ketua Pengabdian</th>
                                    <th>Anggota Pengabdian</th>
                                    <th>Mahasiswa Terlibat</th>
                                    <th>Mitra</th>
                                    <th>Luaran Yang Direncanakan</th>
                                    <th>Luaran Kegiatan</th>
                                    <th class="text-right-numeric">Total Dana</th>
                                    <th class="aksi-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengabdian as $item)
                                    <tr>
                                        <td class="no-column">{{ $loop->iteration }}</td>
                                        <td class="judul-pengabdian">{{ $item->judul_pengabdian }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengabdian)->format('d/m/Y') }}</td>
                                        <td>{{ $item->ketua->nama ?? '-' }}</td>
                                        <td class="td-list">
                                            @php
                                                $anggota = $item->dosen->where('pivot.status_anggota', 'anggota');
                                            @endphp
                                            @if ($anggota->count() > 0)
                                                <ol class="mb-0">
                                                    @foreach ($anggota->take(2) as $dosen)
                                                        <li>{{ $dosen->nama }}</li>
                                                    @endforeach
                                                </ol>
                                                @if ($anggota->count() > 2)
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#anggotaModal{{ $loop->index }}">Lihat
                                                        +{{ $anggota->count() - 2 }} lainnya</a>
                                                @endif
                                                <!-- Modal for anggota -->
                                                <div class="modal fade" id="anggotaModal{{ $loop->index }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="anggotaModalLabel{{ $loop->index }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="anggotaModalLabel{{ $loop->index }}">Anggota
                                                                    Pengabdian</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ol>
                                                                    @foreach ($anggota as $dosen)
                                                                        <li>{{ $dosen->nama }}</li>
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
                                            @else
                                                <span class="text-muted font-italic">Tidak ada anggota</span>
                                            @endif
                                        </td>
                                        <td class="td-list">
                                            @if ($item->mahasiswa->count() > 0)
                                                <ol class="mb-0">
                                                    @foreach ($item->mahasiswa->take(2) as $mhs)
                                                        <li>{{ $mhs->nama }}</li>
                                                    @endforeach
                                                </ol>
                                                @if ($item->mahasiswa->count() > 2)
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#mahasiswaModal{{ $loop->index }}">Lihat
                                                        +{{ $item->mahasiswa->count() - 2 }} lainnya</a>
                                                @endif

                                                <!-- Modal for mahasiswa -->
                                                <div class="modal fade" id="mahasiswaModal{{ $loop->index }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="mahasiswaModalLabel{{ $loop->index }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="mahasiswaModalLabel{{ $loop->index }}">Mahasiswa
                                                                    Terlibat</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ol>
                                                                    @foreach ($item->mahasiswa as $mhs)
                                                                        <li>{{ $mhs->nama }}</li>
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
                                            @else
                                                <span class="text-muted font-italic">Tidak ada mahasiswa</span>
                                            @endif
                                        </td>
                                        <td class="td-list">
                                            @if ($item->mitra->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach ($item->mitra->take(2) as $mitra)
                                                        <li>{{ $mitra->nama_mitra }}</li>
                                                    @endforeach
                                                    @if ($item->mitra->count() > 2)
                                                        <li><small class="text-muted">+{{ $item->mitra->count() - 2 }}
                                                                lainnya</small></li>
                                                    @endif
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td style="text-align: center; font-weight: bold;">
                                            @if (is_array($item->jumlah_luaran_direncanakan) && count($item->jumlah_luaran_direncanakan) > 0)
                                                {{-- Lakukan perulangan di dalam array untuk menampilkan setiap nama luaran --}}
                                                <div class="d-flex flex-column align-items-start">
                                                    @foreach ($item->jumlah_luaran_direncanakan as $jenis)
                                                        {{-- Menggunakan badge untuk tampilan yang rapi --}}
                                                        <span class="badge badge-info mb-1">{{ $jenis }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">Tidak Ada</span>
                                            @endif
                                        </td>

                                        {{-- =============================================== --}}
                                        {{--   KODE UNTUK KOLOM LUARAN YANG SUDAH SESUAI     --}}
                                        {{-- =============================================== --}}
                                        <td class="td-list">
                                            @if ($item->luaran->isNotEmpty())
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($item->luaran as $luaranEntry)
                                                        @php
                                                            $jenis =
                                                                $luaranEntry->jenisLuaran->nama_jenis_luaran ?? null;
                                                            $detailHki = $luaranEntry->detailHki ?? null;
                                                        @endphp

                                                        @if ($jenis && strtoupper(trim($jenis)) === 'HKI' && $detailHki)
                                                            <a href="{{ route('admin.hki.show', $detailHki->id_detail_hki) }}"
                                                                title="{{ $detailHki->judul_ciptaan }}"
                                                                data-toggle="tooltip" class="mr-1 mb-1">
                                                                <span class="badge badge-hki">HKI</span>
                                                            </a>
                                                        @else
                                                            <span
                                                                class="badge badge-secondary mr-1 mb-1">{{ $jenis ?? '-' }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted font-italic">Tidak ada luaran</span>
                                            @endif
                                        </td>

                                        <td class="text-right-numeric">
                                            Rp {{ number_format($item->sumber_dana_sum_jumlah_dana ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="aksi-column">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.pengabdian.show', $item->id_pengabdian) }}"
                                                    class="btn btn-sm btn-info" title="Detail"><i
                                                        class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.pengabdian.edit', $item->id_pengabdian) }}"
                                                    class="btn btn-sm btn-warning" title="Edit"><i
                                                        class="fas fa-edit"></i></a>
                                                <form
                                                    action="{{ route('admin.pengabdian.destroy', $item->id_pengabdian) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Hapus"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data pengabdian.</td>
                                        {{-- Diubah ke colspan 10 --}}
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>
    <!-- Responsive JS removed to avoid duplicate search and simplify behavior -->

    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                "pageLength": 10,
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
                    }
                },
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "fixedHeader": true,
                "lengthChange": true,
                // Use default dom so length selector and search are placed like HKI table
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, -1] // Nonaktifkan order/search untuk kolom No dan Aksi
                }],
                // Default order: tanggal_pengabdian (kolom index 2) descending so newest appear first
                "order": [
                    [2, 'desc']
                ]
            });

            // Re-numbering column
            table.on('order.dt search.dt', function() {
                let i = 1;
                table.cells(null, 0, {
                    search: 'applied',
                    order: 'applied'
                }).every(function(cell) {
                    this.data(i++);
                });
            }).draw();

            // Style the built-in DataTables search input and set placeholder for consistency
            var dtFilter = $('.dataTables_filter input');
            dtFilter.addClass('form-control form-control-sm');
            dtFilter.attr('placeholder', 'Cari pengabdian, judul, ketua, mitra...');
            // Size the search input to approximately match the placeholder length
            (function() {
                var ph = dtFilter.attr('placeholder') || '';
                if (!ph) return;
                // clamp character count between 20 and 80 for reasonable widths
                var chars = Math.min(Math.max(ph.length, 20), 80);
                // use ch unit so width corresponds to character count; override bootstrap width via inline styles
                dtFilter.css({
                    'width': chars + 'ch',
                    'display': 'inline-block'
                });
            })();

            // Style length select
            $('.dataTables_length select').addClass('form-control form-control-sm');

            // Column filters
            $('#filterTahun').on('change', function() {
                var val = this.value;
                // Assuming Tanggal column (index 2) contains dd/mm/YYYY, filter by year
                table.column(2).search(val ? val : '', true, false).draw();
            });

            $('#filterKetua').on('change', function() {
                var val = this.value;
                table.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
            });

            $('#filterMitra').on('change', function() {
                var val = this.value;
                table.column(6).search(val ? '^' + val + '$' : '', true, false).draw();
            });

            // Aktifkan tooltip Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <!-- Filter Modal -->
    <div class="modal fade" id="pengabdianFilterModal" tabindex="-1" role="dialog"
        aria-labelledby="pengabdianFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pengabdianFilterModalLabel"><i class="fas fa-filter mr-2"></i>Filter
                        Pengabdian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="GET" action="{{ route('admin.pengabdian.index') }}">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="mitra">Mitra</label>
                                <select id="mitra" name="mitra" class="form-control">
                                    <option value="">Semua Mitra</option>
                                    @if (isset($mitraList) && count($mitraList) > 0)
                                        @foreach ($mitraList as $mitra)
                                            <option value="{{ $mitra->id_mitra }}"
                                                {{ request('mitra') == $mitra->id_mitra ? 'selected' : '' }}>
                                                {{ $mitra->nama_mitra }}</option>
                                        @endforeach
                                    @else
                                        <option value="mitra_a" {{ request('mitra') == 'mitra_a' ? 'selected' : '' }}>
                                            Mitra
                                            A (contoh)</option>
                                        <option value="mitra_b" {{ request('mitra') == 'mitra_b' ? 'selected' : '' }}>
                                            Mitra
                                            B (contoh)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="prodi">Program Studi</label>
                                <select id="prodi" name="prodi" class="form-control">
                                    <option value="">Semua Prodi</option>
                                    @if (isset($prodiList) && count($prodiList) > 0)
                                        @foreach ($prodiList as $prodi)
                                            <option value="{{ $prodi->kode_prodi ?? $prodi->id }}"
                                                {{ request('prodi') == ($prodi->kode_prodi ?? $prodi->id) ? 'selected' : '' }}>
                                                {{ $prodi->nama_prodi ?? $prodi->nama }}</option>
                                        @endforeach
                                    @else
                                        <option value="informatika"
                                            {{ request('prodi') == 'informatika' ? 'selected' : '' }}>Informatika</option>
                                        <option value="sistem_informasi"
                                            {{ request('prodi') == 'sistem_informasi' ? 'selected' : '' }}>Sistem Informasi
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="total_dana">Total Dana Minimal (IDR)</label>
                                <input type="number" id="total_dana" name="total_dana" class="form-control"
                                    min="0" step="1000" value="{{ request('total_dana') }}"
                                    placeholder="Masukkan nilai minimal total dana">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
