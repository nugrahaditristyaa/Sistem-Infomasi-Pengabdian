@extends('kaprodi.layouts.main')

@section('title', 'Daftar Pengabdian ' . $prodi)

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pengabdian {{ $prodi }}</h1>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengabdian</h6>

            <!-- Filter by Year -->
            <form method="GET" action="" class="form-inline">
                <label class="mr-2">Filter Tahun:</label>
                <select name="year" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                    <option value="all" {{ request('year') == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pengabdianTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Pengabdian</th>
                            <th>Ketua</th>
                            <th>Tanggal</th>
                            <th>Mitra</th>
                            <th>Sumber Dana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengabdian as $index => $item)
                            <tr>
                                <td>{{ $pengabdian->firstItem() + $index }}</td>
                                <td>{{ $item->judul_pengabdian }}</td>
                                <td>{{ $item->nama_ketua ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengabdian)->format('d M Y') }}</td>
                                <td>{{ $item->mitra }}</td>
                                <td>{{ $item->sumber_dana }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data pengabdian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $pengabdian->links() }}
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#pengabdianTable').DataTable({
                "paging": false, // We use Laravel pagination
                "searching": true,
                "ordering": true,
                "info": false,
                "language": {
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "emptyTable": "Tidak ada data tersedia"
                }
            });
        });
    </script>
@endpush
