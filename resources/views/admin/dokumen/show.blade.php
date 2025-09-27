@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Detail Dokumen</h4>
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Pengabdian</dt>
                    <dd class="col-sm-9">{{ $dokumen->pengabdian->judul_pengabdian ?? '-' }}</dd>
                    <dt class="col-sm-3">Jenis</dt>
                    <dd class="col-sm-9">{{ $dokumen->jenisDokumen->nama_jenis_dokumen ?? '-' }}</dd>
                    <dt class="col-sm-3">Nama File</dt>
                    <dd class="col-sm-9">{{ $dokumen->nama_file }}</dd>
                </dl>
                <a href="{{ route('admin.dokumen.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
