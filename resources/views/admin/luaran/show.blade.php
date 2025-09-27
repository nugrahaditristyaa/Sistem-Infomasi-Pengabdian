@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Detail Luaran</h4>
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Pengabdian</dt>
                    <dd class="col-sm-9">{{ $luaran->pengabdian->judul_pengabdian ?? '-' }}</dd>
                    <dt class="col-sm-3">Kategori SPMI</dt>
                    <dd class="col-sm-9">{{ $luaran->kategoriSpmi->kode_spmi ?? '-' }}</dd>
                    <dt class="col-sm-3">Jenis Luaran</dt>
                    <dd class="col-sm-9">{{ $luaran->jenisLuaran->nama_jenis_luaran ?? '-' }}</dd>
                    <dt class="col-sm-3">Judul</dt>
                    <dd class="col-sm-9">{{ $luaran->judul }}</dd>
                    <dt class="col-sm-3">Tahun</dt>
                    <dd class="col-sm-9">{{ $luaran->tahun }}</dd>
                </dl>
                <a href="{{ route('admin.luaran.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
