@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Detail KPI</h4>
        <div class="card mb-3">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Kode</dt>
                    <dd class="col-sm-9">{{ $kpi->kode }}</dd>
                    <dt class="col-sm-3">Indikator</dt>
                    <dd class="col-sm-9">{{ $kpi->nama_indikator }}</dd>
                    <dt class="col-sm-3">Target</dt>
                    <dd class="col-sm-9">{{ $kpi->target }} {{ $kpi->satuan }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Monitoring</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Pengabdian</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kpi->monitoringKpi as $m)
                            <tr>
                                <td>{{ $m->tahun }}</td>
                                <td>{{ $m->pengabdian->judul_pengabdian ?? '-' }}</td>
                                <td>{{ $m->nilai_capai }}</td>
                                <td>{{ $m->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
