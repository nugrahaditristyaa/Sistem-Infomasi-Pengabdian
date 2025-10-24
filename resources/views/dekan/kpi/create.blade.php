@extends('dekan.layouts.main')

@section('title', 'Tambah KPI - InQA Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus mr-2 text-primary"></i>Tambah KPI
            </h1>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Form Tambah KPI
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dekan.kpi.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">
                                    <i class="fas fa-code mr-1 text-primary"></i>Kode KPI
                                </label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror"
                                    name="kode" value="{{ old('kode') }}" placeholder="Contoh: KPI001" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Kode unik untuk identifikasi KPI</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">
                                    <i class="fas fa-tag mr-1 text-primary"></i>Satuan
                                </label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                    name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: %, buah, orang"
                                    required>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-bullseye mr-1 text-primary"></i>Nama Indikator
                        </label>
                        <input type="text" class="form-control @error('nama_indikator') is-invalid @enderror"
                            name="nama_indikator" value="{{ old('nama_indikator') }}"
                            placeholder="Masukkan nama indikator KPI" required>
                        @error('nama_indikator')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-target mr-1 text-primary"></i>Target
                        </label>
                        <input type="number" class="form-control @error('target') is-invalid @enderror" name="target"
                            min="0" step="0.01" value="{{ old('target') }}" placeholder="Masukkan nilai target"
                            required>
                        @error('target')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dekan.kpi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save mr-1"></i>Simpan KPI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endpush

