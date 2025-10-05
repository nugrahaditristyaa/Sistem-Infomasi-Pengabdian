@extends('inQA.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Tambah KPI</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('inqa.kpi.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" class="form-control" name="kode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indikator</label>
                        <input type="text" class="form-control" name="nama_indikator" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <input type="number" class="form-control" name="target" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" class="form-control" name="satuan" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('inqa.kpi.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
