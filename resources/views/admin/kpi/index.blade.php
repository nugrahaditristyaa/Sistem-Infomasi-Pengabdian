@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>KPI</h4>
            <a href="{{ route('admin.kpi.create') }}" class="btn btn-primary">Tambah</a>
        </div>
        <div class="mb-3">
            <a href="{{ route('admin.kpi.monitoring') }}" class="btn btn-outline-secondary">Monitoring KPI</a>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Indikator</th>
                            <th>Target</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kpi as $row)
                            <tr>
                                <td>{{ $row->kode }}</td>
                                <td>{{ $row->nama_indikator }}</td>
                                <td>{{ $row->target }}</td>
                                <td>{{ $row->satuan }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info"
                                        href="{{ route('admin.kpi.show', $row->id_kpi) }}">Detail</a>
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ route('admin.kpi.edit', $row->id_kpi) }}">Ubah</a>
                                    <form action="{{ route('admin.kpi.destroy', $row->id_kpi) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Hapus data?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $kpi->links() }}
            </div>
        </div>
    </div>
@endsection
