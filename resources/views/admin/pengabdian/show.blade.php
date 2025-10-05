@extends('admin.layouts.main')

@section('title', 'Detail Pengabdian Masyarakat')

@push('styles')
    {{-- CSS Kustom untuk menyempurnakan tampilan halaman detail --}}
    <style>
        .detail-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #4e73df;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .main-title {
            font-weight: 600;
            color: #3a3b45;
            line-height: 1.4;
        }

        .list-group-item {
            padding-left: 0;
            padding-right: 0;
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .sub-section-hki {
            background-color: #f8f9fc;
            padding: 1rem;
            border-radius: 0.35rem;
            margin-top: 1rem;
            border: 1px solid #e3e6f0;
        }
    </style>
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengabdian</h1>
        <a href="{{ route('admin.pengabdian.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        {{-- Kolom Kiri untuk Informasi Utama --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4 class="main-title mb-4">{{ $pengabdian->judul_pengabdian }}</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Tanggal Pengabdian</small>
                            <span
                                class="font-weight-bold">{{ \Carbon\Carbon::parse($pengabdian->tanggal_pengabdian)->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Nama Mitra</small>
                            <span
                                class="font-weight-bold">{{ optional($pengabdian->mitra->first())->nama_mitra ?? '-' }}</span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <small class="text-muted d-block">Lokasi Kegiatan</small>
                            <span
                                class="font-weight-bold">{{ optional($pengabdian->mitra->first())->lokasi_mitra ?? '-' }}</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Luaran Kegiatan --}}
                    <h5 class="detail-section-title">Luaran Kegiatan</h5>

                    <div class="mb-3">
                        <small class="text-muted d-block">Total Luaran Direncanakan</small>
                        <span class="font-weight-bold text-primary">{{ $pengabdian->jumlah_luaran_direncanakan }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Luaran Wajib</small>
                        <span
                            class="font-weight-bold">{{ $pengabdian->luaranWajib->nama_luaran ?? 'Tidak ada data' }}</span>
                    </div>

                    @if ($pengabdian->luaran->isNotEmpty())
                        <div>
                            <small class="text-muted d-block">Luaran Tambahan</small>
                            <ul class="list-group list-group-flush mt-1">
                                @foreach ($pengabdian->luaran as $luaran)
                                    <li class="list-group-item">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        <span
                                            class="font-weight-bold">{{ $luaran->jenisLuaran->nama_jenis_luaran ?? '-' }}</span>

                                        @if ($luaran->jenisLuaran->nama_jenis_luaran === 'HKI' && $luaran->detailHki)
                                            <div class="sub-section-hki">
                                                <h6 class="font-weight-bold text-primary mb-3"><i
                                                        class="fas fa-copyright mr-2"></i>Detail HKI</h6>
                                                <div class="row">
                                                    <div class="col-md-6 mb-2"><small class="text-muted d-block">No.
                                                            Pendaftaran</small><strong>{{ $luaran->detailHki->no_pendaftaran }}</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-2"><small class="text-muted d-block">Tgl.
                                                            Permohonan</small><strong>{{ \Carbon\Carbon::parse($luaran->detailHki->tgl_permohonan)->isoFormat('D MMM YYYY') }}</strong>
                                                    </div>
                                                    <div class="col-12 mb-2"><small class="text-muted d-block">Judul
                                                            Ciptaan</small><strong>{{ $luaran->detailHki->judul_ciptaan }}</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-2"><small class="text-muted d-block">Pemegang
                                                            Hak
                                                            Cipta</small><strong>{{ $luaran->detailHki->pemegang_hak_cipta }}</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-2"><small class="text-muted d-block">Jenis
                                                            Ciptaan</small><strong>{{ $luaran->detailHki->jenis_ciptaan }}</strong>
                                                    </div>
                                                </div>

                                                {{-- PERUBAHAN DI SINI: Menampilkan anggota pencipta HKI --}}
                                                @if ($luaran->detailHki->dosen->isNotEmpty())
                                                    <hr class="my-3">
                                                    <div>
                                                        <small class="text-muted d-block mb-1">Anggota Pencipta
                                                            (Dosen)
                                                        </small>
                                                        @foreach ($luaran->detailHki->dosen as $dosenHki)
                                                            <span
                                                                class="badge badge-info mr-1">{{ $dosenHki->nama }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if ($luaran->detailHki->dokumen)
                                                    <hr class="my-3">
                                                    <div>
                                                        <small class="text-muted d-block">Dokumen HKI Terlampir</small>
                                                        <a href="{{ Storage::url($luaran->detailHki->dokumen->path_file) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                            <i class="fas fa-download fa-sm mr-1"></i>
                                                            {{ $luaran->detailHki->dokumen->nama_file }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan untuk Tim, Dana, dan Dokumen --}}
        <div class="col-lg-4">
            <!-- Tim Pengabdian -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users mr-2"></i>Tim Pengabdian</h6>
                </div>
                <div class="card-body">
                    <h6 class="detail-section-title">Dosen</h6>
                    <ul class="list-group list-group-flush">
                        {{-- Tampilkan Ketua terlebih dahulu --}}
                        @foreach ($pengabdian->dosen->where('pivot.status_anggota', 'ketua') as $dosen)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $dosen->nama }}</span>
                                <span class="badge badge-primary">Ketua</span>
                            </li>
                        @endforeach
                        {{-- Kemudian tampilkan Anggota --}}
                        @foreach ($pengabdian->dosen->where('pivot.status_anggota', 'anggota') as $dosen)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $dosen->nama }}</span>
                                <span class="badge badge-secondary">Anggota</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($pengabdian->mahasiswa->isNotEmpty())
                        <h6 class="detail-section-title">Mahasiswa</h6>
                        <ul class="list-group list-group-flush">
                            @foreach ($pengabdian->mahasiswa as $mahasiswa)
                                <li class="list-group-item">{{ $mahasiswa->nama }} ({{ $mahasiswa->nim }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Sumber Dana -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-money-bill-wave mr-2"></i>Sumber Dana
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php $totalDana = 0; @endphp
                        @forelse ($pengabdian->sumberDana as $dana)
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <span>
                                        {{ $dana->nama_sumber }}
                                        @if (strtolower($dana->jenis) == 'internal')
                                            <span class="badge badge-success ml-1">Internal</span>
                                        @elseif(strtolower($dana->jenis) == 'eksternal')
                                            <span class="badge badge-danger ml-1">Eksternal</span>
                                        @else
                                            <span class="badge badge-secondary ml-1">{{ $dana->jenis }}</span>
                                        @endif
                                    </span>
                                    <span>Rp {{ number_format($dana->jumlah_dana, 0, ',', '.') }}</span>
                                </div>
                            </li>
                            @php $totalDana += $dana->jumlah_dana; @endphp
                        @empty
                            <li class="list-group-item">Tidak ada data sumber dana.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between font-weight-bold">
                    <span>Total Biaya</span>
                    <span>Rp {{ number_format($totalDana, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Dokumen Pendukung -->
            @php
                $dokumenPendukung = $pengabdian->dokumen->whereNull('id_detail_hki');
            @endphp
            @if ($dokumenPendukung->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-paperclip mr-2"></i>Dokumen Pendukung
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($dokumenPendukung as $dokumen)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $dokumen->jenisDokumen->nama_jenis_dokumen ?? 'Dokumen' }}</span>
                                    <a href="{{ Storage::url($dokumen->path_file) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download fa-sm"></i> Unduh
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
