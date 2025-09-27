@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Ubah KPI</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.kpi.update', $kpi->id_kpi) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" class="form-control" value="{{ $kpi->kode }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Indikator</label>
                        <input type="text" class="form-control" name="nama_indikator" value="{{ $kpi->nama_indikator }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <input type="number" class="form-control" name="target" min="0" value="{{ $kpi->target }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" class="form-control" name="satuan" value="{{ $kpi->satuan }}" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.kpi.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
