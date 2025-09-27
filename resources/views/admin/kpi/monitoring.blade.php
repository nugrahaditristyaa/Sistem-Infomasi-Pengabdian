@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Monitoring KPI</h4>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.kpi.monitoring.store') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">KPI</label>
                            <select name="id_kpi" class="form-select">
                                @foreach ($kpi as $k)
                                    <option value="{{ $k->id_kpi }}">{{ $k->kode }} - {{ $k->nama_indikator }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pengabdian</label>
                            <select name="id_pengabdian" class="form-select">
                                @foreach ($pengabdian as $p)
                                    <option value="{{ $p->id_pengabdian }}">{{ $p->judul_pengabdian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tahun</label>
                            <input type="number" class="form-control" name="tahun" min="2020" max="2030"
                                value="{{ date('Y') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Nilai Capaian</label>
                            <input type="number" class="form-control" name="nilai_capai" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" name="status">
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-primary" type="submit">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Kode KPI</th>
                            <th>Pengabdian</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monitoring as $m)
                            <tr>
                                <td>{{ $m->tahun }}</td>
                                <td>{{ $m->kpi->kode }}</td>
                                <td>{{ $m->pengabdian->judul_pengabdian ?? '-' }}</td>
                                <td>{{ $m->nilai_capai }}</td>
                                <td>{{ $m->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $monitoring->links() }}
            </div>
        </div>
    </div>
@endsection
