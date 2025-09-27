@extends('admin.layouts.main')

@section('title', 'Detail HKI')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail HKI: {{ $hki->judul_ciptaan }}</h1>
        <a href="{{ route('admin.hki.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar HKI
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pendaftaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Judul Ciptaan:</strong><br>{{ $hki->judul_ciptaan }}</p>
                    <p><strong>Jenis Ciptaan:</strong><br>{{ $hki->jenis_ciptaan }}</p>
                    <p><strong>Nomor Pendaftaran:</strong><br>{{ $hki->no_pendaftaran }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Permohonan:</strong><br>{{ \Carbon\Carbon::parse($hki->tgl_permohonan)->isoFormat('D MMMM YYYY') }}</p>
                    <p><strong>Pemegang Hak Cipta:</strong><br>{{ $hki->pemegang_hak_cipta }}</p>
                    @if($hki->dokumen)
                        <p><strong>Dokumen Terlampir:</strong><br>
                            <a href="{{ Storage::url($hki->dokumen->path_file) }}" target="_blank">
                                <i class="fas fa-file-alt mr-1"></i> {{ $hki->dokumen->nama_file }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pencipta & Kegiatan Terkait</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Anggota Pencipta:</strong></p>
                    @if($hki->dosen->isNotEmpty())
                        <ul>
                            @foreach($hki->dosen as $pencipta)
                                <li>{{ $pencipta->nama }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada data pencipta.</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($hki->luaran && $hki->luaran->pengabdian)
                        @php $pengabdian = $hki->luaran->pengabdian; @endphp
                        <p><strong>Judul Pengabdian Terkait:</strong><br>
                            <a href="{{ route('admin.pengabdian.show', $pengabdian->id_pengabdian) }}">{{ $pengabdian->judul_pengabdian }}</a>
                        </p>
                        <p><strong>Ketua:</strong><br>{{ $pengabdian->ketua->nama ?? '-' }}</p>
                    @else
                        <p class="text-muted">Tidak terhubung dengan kegiatan pengabdian.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection