@extends('admin.layouts.main')

@section('title', 'Form Edit Pengabdian')

@push('styles')
    {{-- Dependensi CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- Style Kustom --}}
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .select2-container .select2-selection--single {
            display: flex;
            align-items: center;
            height: calc(1.5em + 0.75rem + 2px);
        }

        .is-invalid+.select2-container .select2-selection--single,
        .is-invalid+.select2-container .select2-selection--multiple {
            border-color: #e74a3b !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple {
            display: flex !important;
            align-items: center !important;
            min-height: calc(1.5em + 0.75rem + 2px);
            padding: 0 8px !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__placeholder {
            margin: 0 !important;
        }

        #mahasiswa-baru-section {
            display: none;
        }

        #dosen-baru-ketua-section {
            display: none;
        }

        #dosen-baru-anggota-section {
            display: none;
        }

        .current-file a {
            font-weight: 600;
            color: #4e73df;
        }

        #select2-ketua_nik-container {
            color: #858796;
        }

        /* Selalu sembunyikan tombol hapus di baris pertama dosen baru anggota */
        #dosen-baru-anggota-container .dosen-baru-anggota-item:first-child .btn-hapus-dosen-baru-anggota {
            display: none;
        }
    </style>
@endpush

@section('content')
    @include('admin.pengabdian.select2_config')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Edit Pengabdian Masyarakat</h1>
        <a href="{{ route('admin.pengabdian.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form id="pengabdianForm" action="{{ route('admin.pengabdian.update', $pengabdian->id_pengabdian) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Informasi Utama --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt fa-fw mr-2"></i>Informasi Utama</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="judul_pengabdian">Judul Pengabdian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul_pengabdian') is-invalid @enderror"
                        id="judul_pengabdian" name="judul_pengabdian"
                        value="{{ old('judul_pengabdian', $pengabdian->judul_pengabdian) }}" required>
                    @error('judul_pengabdian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_mitra">Nama Mitra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_mitra') is-invalid @enderror"
                                id="nama_mitra" name="nama_mitra"
                                value="{{ old('nama_mitra', optional($pengabdian->mitra->first())->nama_mitra) }}">
                            @error('nama_mitra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lokasi_kegiatan">Lokasi Kegiatan Mitra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lokasi_kegiatan') is-invalid @enderror"
                                id="lokasi_kegiatan" name="lokasi_kegiatan"
                                value="{{ old('lokasi_kegiatan', optional($pengabdian->mitra->first())->lokasi_mitra) }}">
                            @error('lokasi_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tanggal_pengabdian">Tanggal Pengabdian <span class="text-danger">*</span></label>
                    {{-- MENGUBAH TYPE DARI TEXT KE DATE, MEMASTIKAN FORMAT TANGGAL YYYY-MM-DD --}}
                    <input type="date" class="form-control @error('tanggal_pengabdian') is-invalid @enderror"
                        id="tanggal_pengabdian" name="tanggal_pengabdian"
                        value="{{ old('tanggal_pengabdian', isset($pengabdian) ? Carbon\Carbon::parse($pengabdian->tanggal_pengabdian)->format('Y-m-d') : '') }}"
                        required>
                    @error('tanggal_pengabdian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Jenis Luaran yang Direncanakan <span class="text-danger">*</span></label>
                    <small class="form-text text-muted mb-2">
                        Pilih jenis-jenis luaran yang akan dicapai sesuai proposal (termasuk luaran wajib yang sudah dipilih
                        di atas).
                    </small>
                    @php
                        $selectedDirencanakan = old(
                            'jumlah_luaran_direncanakan',
                            is_string($pengabdian->jumlah_luaran_direncanakan)
                                ? json_decode($pengabdian->jumlah_luaran_direncanakan, true) ?? []
                                : (is_array($pengabdian->jumlah_luaran_direncanakan)
                                    ? $pengabdian->jumlah_luaran_direncanakan
                                    : []),
                        );
                    @endphp
                    <div class="checkbox-group @error('jumlah_luaran_direncanakan') is-invalid @enderror">
                        @foreach ($jenisLuaran as $jl)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                    id="edit_direncanakan_{{ $jl->id_jenis_luaran }}" name="jumlah_luaran_direncanakan[]"
                                    value="{{ $jl->nama_jenis_luaran }}"
                                    {{ in_array($jl->nama_jenis_luaran, $selectedDirencanakan) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="edit_direncanakan_{{ $jl->id_jenis_luaran }}">{{ $jl->nama_jenis_luaran }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('jumlah_luaran_direncanakan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ketua Pengabdian --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-tie fa-fw mr-2"></i>Ketua Pengabdian</h6>
            </div>
            <div class="card-body">
                <small class="form-text text-info mb-3">
                    <i class="fas fa-info-circle"></i> <strong>Pilih Ketua dari Dosen yang sudah ADA atau tambah Dosen Eksternal (hanya satu cara).</strong>
                </small>
                
                <div class="form-group">
                    <label for="ketua_nik">Pilih Dosen (Ketua) <span class="text-danger" id="ketua-nik-required">*</span></label>
                    <select id="ketua_nik" name="ketua_nik" class="form-control @error('ketua_nik') is-invalid @enderror" data-placeholder="— Pilih Ketua —">
                        <option value=""></option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->nik }}"
                                {{ old('ketua_nik', $pengabdian->ketua_pengabdian) == $d->nik ? 'selected' : '' }}>
                                {{ $d->nama }} - {{ $d->nidn }}
                            </option>
                        @endforeach
                    </select>
                    @error('ketua_nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol Tambah Dosen Eksternal (Ketua) --}}
                <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-dosen-baru-ketua">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal (Ketua)
                </button>

                {{-- Section Dosen Baru Ketua --}}
                <div id="dosen-baru-ketua-section" style="display: none;">
                    <hr class="mt-4 mb-3">
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-user-plus fa-fw mr-1"></i>Tambah Dosen Eksternal (Ketua)</h6>
                    @error('dosen_baru_ketua')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group mb-md-0">
                                <label>NIK <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_baru_ketua[nik]" id="dosen_baru_ketua_nik"
                                    class="form-control @error('dosen_baru_ketua.nik') is-invalid @enderror" 
                                    placeholder="NIK Dosen" value="{{ old('dosen_baru_ketua.nik') }}">
                                @error('dosen_baru_ketua.nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-md-0">
                                <label>Nama <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_baru_ketua[nama]" id="dosen_baru_ketua_nama"
                                    class="form-control @error('dosen_baru_ketua.nama') is-invalid @enderror" 
                                    placeholder="Nama Dosen" value="{{ old('dosen_baru_ketua.nama') }}">
                                @error('dosen_baru_ketua.nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>NIDN</label>
                                <input type="text" name="dosen_baru_ketua[nidn]" id="dosen_baru_ketua_nidn"
                                    class="form-control @error('dosen_baru_ketua.nidn') is-invalid @enderror" 
                                    placeholder="NIDN" value="{{ old('dosen_baru_ketua.nidn') }}">
                                @error('dosen_baru_ketua.nidn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>Jabatan</label>
                                <input type="text" name="dosen_baru_ketua[jabatan]" id="dosen_baru_ketua_jabatan"
                                    class="form-control @error('dosen_baru_ketua.jabatan') is-invalid @enderror" 
                                    placeholder="Jabatan" value="{{ old('dosen_baru_ketua.jabatan') }}">
                                @error('dosen_baru_ketua.jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>Prodi <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_baru_ketua[prodi]" id="dosen_baru_ketua_prodi"
                                    class="form-control @error('dosen_baru_ketua.prodi') is-invalid @enderror" 
                                    placeholder="Program Studi" value="{{ old('dosen_baru_ketua.prodi') }}">
                                @error('dosen_baru_ketua.prodi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label>Bidang Keahlian</label>
                                <input type="text" name="dosen_baru_ketua[bidang_keahlian]" id="dosen_baru_ketua_bidang_keahlian"
                                    class="form-control @error('dosen_baru_ketua.bidang_keahlian') is-invalid @enderror" 
                                    placeholder="Bidang Keahlian" value="{{ old('dosen_baru_ketua.bidang_keahlian') }}">
                                @error('dosen_baru_ketua.bidang_keahlian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="dosen_baru_ketua[email]" id="dosen_baru_ketua_email"
                                    class="form-control @error('dosen_baru_ketua.email') is-invalid @enderror" 
                                    placeholder="Email" value="{{ old('dosen_baru_ketua.email') }}">
                                @error('dosen_baru_ketua.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Anggota Pengabdian --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users fa-fw mr-2"></i>Anggota Pengabdian</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="anggota">Pilih Dosen FTI (Anggota)</label>
                    @php
                        $anggota_ids = $pengabdian->dosen
                            ->where('pivot.status_anggota', 'anggota')
                            ->pluck('nik')
                            ->toArray();
                    @endphp
                    <select id="anggota" name="anggota[]"
                        class="form-control @error('anggota.*') is-invalid @enderror" multiple
                        data-placeholder="Pilih satu atau lebih...">
                        @foreach ($dosen as $d)
                            <option value="{{ $d->nik }}"
                                {{ in_array($d->nik, old('anggota', $anggota_ids)) ? 'selected' : '' }}>
                                {{ $d->nama }} - {{ $d->nidn }}
                            </option>
                        @endforeach
                    </select>
                    @error('anggota.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol Tambah Dosen Eksternal (Anggota) --}}
                <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-dosen-baru-anggota">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal (Anggota)
                </button>

                {{-- Section Dosen Baru Anggota --}}
                <div id="dosen-baru-anggota-section" style="display: none;">
                    <hr class="mt-4 mb-3">
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-user-plus fa-fw mr-1"></i>Tambah Dosen Eksternal (Anggota)</h6>
                    @error('dosen_baru_anggota')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div id="dosen-baru-anggota-container">
                        @forelse (old('dosen_baru_anggota', []) as $index => $dsn)
                            <div class="dosen-baru-anggota-item mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mb-md-0">
                                            <label>NIK</label>
                                            <input type="text" name="dosen_baru_anggota[{{ $index }}][nik]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.nik') is-invalid @enderror" 
                                                placeholder="NIK" value="{{ $dsn['nik'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-md-0">
                                            <label>Nama</label>
                                            <input type="text" name="dosen_baru_anggota[{{ $index }}][nama]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.nama') is-invalid @enderror" 
                                                placeholder="Nama" value="{{ $dsn['nama'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group mb-md-0">
                                            <label>NIDN</label>
                                            <input type="text" name="dosen_baru_anggota[{{ $index }}][nidn]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.nidn') is-invalid @enderror" 
                                                placeholder="NIDN" value="{{ $dsn['nidn'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.nidn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-md-0">
                                            <label>Prodi</label>
                                            <input type="text" name="dosen_baru_anggota[{{ $index }}][prodi]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.prodi') is-invalid @enderror" 
                                                placeholder="Prodi" value="{{ $dsn['prodi'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.prodi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-md-0">
                                            <label>Bidang Keahlian</label>
                                            <input type="text" name="dosen_baru_anggota[{{ $index }}][bidang_keahlian]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.bidang_keahlian') is-invalid @enderror" 
                                                placeholder="Bidang Keahlian" value="{{ $dsn['bidang_keahlian'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.bidang_keahlian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-md-0">
                                            <label>Email</label>
                                            <input type="email" name="dosen_baru_anggota[{{ $index }}][email]" 
                                                class="form-control @error('dosen_baru_anggota.' . $index . '.email') is-invalid @enderror" 
                                                placeholder="Email" value="{{ $dsn['email'] ?? '' }}">
                                            @error('dosen_baru_anggota.' . $index . '.email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus-dosen-baru-anggota">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-info mt-2" id="btn-tambah-dosen-baru-anggota">
                        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Anggota
                    </button>
                </div>
            </div>
        </div>

        {{-- Mahasiswa Terlibat --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate fa-fw mr-2"></i>Mahasiswa
                    yang
                    Terlibat</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="mahasiswa_ids">Pilih Mahasiswa</label>
                    @php
                        $mahasiswa_ids = $pengabdian->mahasiswa->pluck('nim')->toArray();
                    @endphp
                    <select id="mahasiswa_ids" name="mahasiswa_ids[]"
                        class="form-control @error('mahasiswa_ids.*') is-invalid @enderror" multiple
                        data-placeholder="Pilih satu atau lebih...">
                        @foreach ($mahasiswa as $m)
                            <option value="{{ $m->nim }}"
                                {{ in_array($m->nim, old('mahasiswa_ids', $mahasiswa_ids)) ? 'selected' : '' }}>
                                {{ $m->nama }} - {{ $m->nim }}
                            </option>
                        @endforeach
                    </select>
                    @error('mahasiswa_ids.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-mhs-baru">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa Baru (jika belum terdaftar)
                </button>
                <div id="mahasiswa-baru-section" style="display: none;">
                    <hr class="mt-4 mb-3">
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-plus-circle fa-fw mr-1"></i>Form
                        Mahasiswa Baru</h6>
                    @error('mahasiswa_baru')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div id="mahasiswa-baru-container">
                        @forelse (old('mahasiswa_baru', []) as $index => $mhs)
                            <div class="row mahasiswa-baru-item mb-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0"><label>NIM</label><input type="text"
                                            inputmode="numeric" pattern="\d*" maxlength="8"
                                            name="mahasiswa_baru[{{ $index }}][nim]"
                                            class="form-control nim-input" placeholder="Masukkan NIM"
                                            value="{{ $mhs['nim'] ?? '' }}"
                                            oninput="this.value = this.value.replace(/\D/g, '')"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0"><label>Nama</label><input type="text"
                                            name="mahasiswa_baru[{{ $index }}][nama]" class="form-control"
                                            placeholder="Masukkan Nama" value="{{ $mhs['nama'] ?? '' }}"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-md-0"><label>Prodi</label><input type="text"
                                            name="mahasiswa_baru[{{ $index }}][prodi]" class="form-control"
                                            placeholder="Masukkan Prodi" value="{{ $mhs['prodi'] ?? '' }}"></div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end"><button type="button"
                                        class="btn btn-danger btn-sm btn-hapus-mhs-baru"><i
                                            class="fas fa-trash"></i></button></div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-info mt-2" id="btn-tambah-mhs-baru"><i
                            class="fas fa-plus fa-sm mr-1"></i> Tambah Baris</button>
                </div>
            </div>
        </div>

        {{-- Sumber Dana --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-money-bill-wave fa-fw mr-2"></i>Sumber Dana
                </h6>
            </div>
            <div class="card-body">
                @error('sumber_dana')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <div id="sumber-dana-container">
                    @php
                        $allSumberDana = old('sumber_dana', $pengabdian->sumberDana->toArray());
                    @endphp
                    @forelse ($allSumberDana as $index => $dana)
                        <div class="row sumber-dana-item mb-3">
                            <div class="col-md-3">
                                <div class="form-group mb-0"><label>Jenis <span
                                            class="text-danger">*</span></label><select
                                        name="sumber_dana[{{ $index }}][jenis]"
                                        class="form-control jenis-sumber-dana" required>
                                        <option value="Internal"
                                            {{ ($dana['jenis'] ?? '') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="Eksternal"
                                            {{ ($dana['jenis'] ?? '') == 'Eksternal' ? 'selected' : '' }}>Eksternal
                                        </option>
                                    </select></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0"><label>Nama Sumber <span
                                            class="text-danger">*</span></label><select
                                        name="sumber_dana[{{ $index }}][nama_sumber]"
                                        class="form-control nama-sumber-dana" required
                                        data-selected-value="{{ $dana['nama_sumber'] ?? '' }}"></select></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0"><label>Jumlah Dana <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="text" name="sumber_dana[{{ $index }}][jumlah_dana]"
                                            class="form-control jumlah-dana" required
                                            value="{{ $dana['jumlah_dana'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end"><button type="button"
                                    class="btn btn-danger btn-sm btn-hapus-sumber-dana"><i
                                        class="fas fa-trash"></i></button></div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <button type="button" class="btn btn-sm btn-info" id="btn-tambah-sumber-dana"><i
                        class="fas fa-plus fa-sm mr-1"></i> Tambah Sumber Dana</button>
                <hr>
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="form-group"><label for="total_biaya" class="font-weight-bold">Total Biaya
                                Pengabdian</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div><input
                                    type="text" class="form-control" id="total_biaya" readonly placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: Luaran Kegiatan --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks fa-fw mr-2"></i>Luaran Kegiatan</h6>
            </div>
            <div class="card-body">
                {{-- Luaran Wajib removed: field deprecated. --}}

                {{-- FIELD JENIS LUARAN YANG DIRENCANAKAN --}}

                <div class="form-group">
                    <label>Hasil Luaran Kegiatan</label>
                    @php
                        $selectedLuaran = old(
                            'luaran_jenis',
                            $pengabdian->luaran->pluck('jenisLuaran.nama_jenis_luaran')->toArray(),
                        );
                    @endphp
                    <div class="checkbox-group">
                        @foreach ($jenisLuaran as $jl)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input luaran-checkbox"
                                    id="luaran_{{ $jl->id_jenis_luaran }}" name="luaran_jenis[]"
                                    value="{{ $jl->nama_jenis_luaran }}"
                                    {{ in_array($jl->nama_jenis_luaran, $selectedLuaran) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="luaran_{{ $jl->id_jenis_luaran }}">{{ $jl->nama_jenis_luaran }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $detailHki =
                        $pengabdian->luaran->firstWhere('jenisLuaran.nama_jenis_luaran', 'HKI')->detailHki ?? null;
                @endphp
                <div id="detail-hki" class="luaran-detail"
                    style="{{ in_array('HKI', $selectedLuaran) ? '' : 'display:none;' }}">
                    <hr>
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-copyright fa-fw mr-1"></i>Detail HKI
                    </h6>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Nomor Pendaftaran <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                name="luaran_data[HKI][no_pendaftaran]"
                                value="{{ old('luaran_data.HKI.no_pendaftaran', optional($detailHki)->no_pendaftaran) }}"
                                required></div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Permohonan <span class="text-danger">*</span></label>
                            {{-- MENGUBAH TYPE DARI TEXT KE DATE, MEMASTIKAN FORMAT TANGGAL YYYY-MM-DD --}}
                            <input type="date"
                                class="form-control @error('luaran_data.HKI.tanggal_permohonan') is-invalid @enderror"
                                name="luaran_data[HKI][tanggal_permohonan]"
                                value="{{ old('luaran_data.HKI.tanggal_permohonan', optional($detailHki)->tgl_permohonan ? \Carbon\Carbon::parse($detailHki->tgl_permohonan)->format('Y-m-d') : '') }}"
                                required>
                            @error('luaran_data.HKI.tanggal_permohonan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group"><label>Judul Ciptaan <span class="text-danger">*</span></label><input
                            type="text" class="form-control" name="luaran_data[HKI][judul_ciptaan]"
                            value="{{ old('luaran_data.HKI.judul_ciptaan', optional($detailHki)->judul_ciptaan) }}"
                            required></div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Pemegang Hak Cipta <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                name="luaran_data[HKI][pemegang_hak_cipta]"
                                value="{{ old('luaran_data.HKI.pemegang_hak_cipta', optional($detailHki)->pemegang_hak_cipta) }}"
                                required></div>
                        <div class="col-md-6 form-group"><label>Jenis Ciptaan <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                name="luaran_data[HKI][jenis_ciptaan]"
                                value="{{ old('luaran_data.HKI.jenis_ciptaan', optional($detailHki)->jenis_ciptaan) }}"
                                required></div>
                    </div>
                    <div class="form-group">
                        <label>Anggota Pencipta (Dosen)</label>
                        @php
                            $anggotaHkiNiks = optional($detailHki)->dosen
                                ? $detailHki->dosen->pluck('nik')->toArray()
                                : [];
                        @endphp
                        <select id="hki_anggota_dosen" name="luaran_data[HKI][anggota_dosen][]" class="form-control"
                            multiple data-placeholder="Pilih satu atau lebih...">
                            @foreach ($dosen as $d)
                                <option value="{{ $d->nik }}"
                                    {{ in_array($d->nik, old('luaran_data.HKI.anggota_dosen', $anggotaHkiNiks)) ? 'selected' : '' }}>
                                    {{ $d->nama }} - {{ $d->nidn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        {{-- PERBAIKAN 3: Menghapus 'required' dari input file --}}
                        <label>Dokumen HKI <span class="text-danger">*</span></label>
                        @php $dokumenHki = optional($detailHki)->dokumen; @endphp
                        @if ($dokumenHki)
                            <div class="current-file mb-2">File saat ini: <a
                                    href="{{ Storage::url($dokumenHki->path_file) }}"
                                    target="_blank">{{ $dokumenHki->nama_file }}</a></div>
                        @endif
                        <div class="custom-file"><input type="file" class="custom-file-input"
                                name="dokumen[hki]"><label class="custom-file-label">Pilih file baru untuk
                                mengganti...</label></div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maks: 5MB.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: Dokumen Pendukung --}}
        <div id="dokumen" class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-upload fa-fw mr-2"></i>Upload Dokumen
                    Pendukung</h6>
            </div>
            <div class="card-body">
                @php
                    $dokumenMapping = [
                        'laporan_akhir' => ['label' => 'Laporan Akhir', 'max' => '10MB'],
                        'surat_tugas' => ['label' => 'Surat Tugas Dosen', 'max' => '5MB'],
                        'surat_permohonan' => ['label' => 'Surat Permohonan', 'max' => '5MB'],
                        'ucapan_terima_kasih' => ['label' => 'Surat Ucapan Terima Kasih', 'max' => '5MB'],
                        'kerjasama' => ['label' => 'MoU/MoA/Dokumen Kerja Sama Kegiatan', 'max' => '5MB'],
                    ];
                @endphp
                @foreach ($dokumenMapping as $key => $docInfo)
                    <div class="form-group">
                        <label> {{ $docInfo['label'] }} (Opsional)</label>
                        @php
                            $currentDoc = $pengabdian->dokumen
                                ->whereNull('id_detail_hki')
                                ->first(function ($doc) use ($docInfo) {
                                    return optional($doc->jenisDokumen)->nama_jenis_dokumen === $docInfo['label'];
                                });
                        @endphp
                        @if ($currentDoc)
                            <div class="current-file mb-2">File saat ini: <a
                                    href="{{ Storage::url($currentDoc->path_file) }}"
                                    target="_blank">{{ $currentDoc->nama_file }}</a></div>
                        @endif
                        <div class="custom-file"><input type="file" class="custom-file-input"
                                name="dokumen[{{ $key }}]"><label class="custom-file-label">Pilih file
                                baru...</label></div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal
                            {{ $docInfo['max'] }}.</small>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.pengabdian.index') }}" class="btn btn-secondary mr-2">Batal</a>
            <button type="submit" id="submitBtn" class="btn btn-primary shadow-sm">
                <i class="fas fa-save fa-sm mr-1"></i> Perbarui Data
            </button>
        </div>
    </form>

    {{-- TEMPLATE --}}
    <script type="text/html" id="mahasiswa-baru-template">
        <div class="row mahasiswa-baru-item mb-3">
            <div class="col-md-4"><div class="form-group mb-md-0"><label>NIM</label><input type="text" name="mahasiswa_baru[__INDEX__][nim]" class="form-control" placeholder="NIM"></div></div>
            <div class="col-md-4"><div class="form-group mb-md-0"><label>Nama</label><input type="text" name="mahasiswa_baru[__INDEX__][nama]" class="form-control" placeholder="Nama"></div></div>
            <div class="col-md-3"><div class="form-group mb-md-0"><label>Prodi</label><input type="text" name="mahasiswa_baru[__INDEX__][prodi]" class="form-control" placeholder="Prodi"></div></div>
            <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger btn-sm btn-hapus-mhs-baru"><i class="fas fa-trash"></i></button></div>
        </div>
    </script>
    <script>
        // Clean diagnostic code for form submission
        (function() {
            var form = document.getElementById('pengabdianForm');
            if (!form) return;

            function humanFileSize(bytes) {
                if (bytes === 0) return '0 B';
                var i = Math.floor(Math.log(bytes) / Math.log(1024));
                var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
            }


        })();
    </script>

    <script type="text/html" id="sumber-dana-template">
         <div class="row sumber-dana-item mb-3">
            <div class="col-md-3"><div class="form-group mb-0"><label>Jenis <span class="text-danger">*</span></label><select name="sumber_dana[__INDEX__][jenis]" class="form-control jenis-sumber-dana" required><option value="" disabled selected>— Pilih —</option><option value="Internal">Internal</option><option value="Eksternal">Eksternal</option></select></div></div>
            <div class="col-md-4"><div class="form-group mb-0"><label>Nama Sumber <span class="text-danger">*</span></label><select name="sumber_dana[__INDEX__][nama_sumber]" class="form-control nama-sumber-dana" required></select></div></div>
            <div class="col-md-3"><div class="form-group mb-0"><label>Jumlah Dana <span class="text-danger">*</span></label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">Rp</span></div><input type="text" name="sumber_dana[__INDEX__][jumlah_dana]" class="form-control jumlah-dana" placeholder="0" required></div></div></div>
            <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-danger btn-sm btn-hapus-sumber-dana"><i class="fas fa-trash"></i></button></div>
        </div>
    </script>

    {{-- Template untuk Dosen Baru Anggota --}}
    <script type="text/html" id="dosen-baru-anggota-template">
        <div class="dosen-baru-anggota-item mb-3">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group mb-md-0"><label>NIK</label><input type="text" name="dosen_baru_anggota[__INDEX__][nik]" class="form-control" placeholder="NIK"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-md-0"><label>Nama</label><input type="text" name="dosen_baru_anggota[__INDEX__][nama]" class="form-control" placeholder="Nama"></div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-md-0"><label>NIDN</label><input type="text" name="dosen_baru_anggota[__INDEX__][nidn]" class="form-control" placeholder="NIDN"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-md-0"><label>Prodi</label><input type="text" name="dosen_baru_anggota[__INDEX__][prodi]" class="form-control" placeholder="Prodi"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-md-0"><label>Bidang Keahlian</label><input type="text" name="dosen_baru_anggota[__INDEX__][bidang_keahlian]" class="form-control" placeholder="Bidang Keahlian"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-md-0"><label>Email</label><input type="email" name="dosen_baru_anggota[__INDEX__][email]" class="form-control" placeholder="Email"></div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-dosen-baru-anggota"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    </script>
@endsection

@push('scripts')
    {{-- Dependensi JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Logika JavaScript --}}
    <script>
        const sumberDanaOptions = {
            'Internal': ['LPPM', 'Universitas', 'Fakultas', 'Prodi', 'Mandiri'],
            'Eksternal': ['DRPM', 'Swasta', 'Gereja', 'LSM', 'Sekolah', 'Fakultas Bisnis', 'Lainnya']
        };









        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: "{{ session('error') }}",
                });
            @elseif ($errors->any())
                const errorMessages = @json($errors->all());
                let formattedErrors = '<ul class="text-left mb-0" style="padding-left: 1.2em;">';
                errorMessages.forEach(function(message) {
                    formattedErrors += '<li>' + message + '</li>';
                });
                formattedErrors += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    html: formattedErrors,
                });
            @endif
            const PengabdianForm = {
                init: function() {
                    this.initPlugins();
                    this.initDosenLogic();
                    this.initDosenBaruLogic();
                    this.initMahasiswaBaruLogic();
                    this.initSumberDanaLogic();
                    this.initLuaranLogic();
                    this.initFileInputs();
                },

                initPlugins: function() {
                    $(".datepicker").flatpickr({
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        allowInput: true
                    });
                    initSelect2WithClear('#ketua_nik');
                    initSelect2WithClear('#anggota, #mahasiswa_ids, #hki_anggota_dosen');
                },

                initDosenLogic: function() {
                    $('#ketua_nik, #anggota, #hki_anggota_dosen').on('change', () => this
                        .updateDosenRoles());
                    this.updateDosenRoles();
                },

                updateDosenRoles: function() {
                    const ketuaNik = $('#ketua_nik').val();
                    const anggotaPengabdianSelect = $('#anggota');
                    const anggotaHkiSelect = $('#hki_anggota_dosen');
                    const selectedPengabdian = anggotaPengabdianSelect.val() || [];
                    const selectedHki = anggotaHkiSelect.val() || [];

                    const updateSelectOptions = (currentSelect, otherSelectedNiks) => {
                        const niksToDisable = [ketuaNik, ...otherSelectedNiks].filter(Boolean);
                        currentSelect.find('option').each(function() {
                            const option = $(this);
                            const optionNik = option.val();
                            if (!optionNik) return;
                            option.prop('disabled', niksToDisable.includes(optionNik));
                        });
                        let currentValues = currentSelect.val() || [];
                        if (Array.isArray(currentValues) && currentValues.includes(ketuaNik)) {
                            currentValues = currentValues.filter(nik => nik !== ketuaNik);
                            currentSelect.val(currentValues);
                        }
                        currentSelect.trigger('change.select2');
                    };
                    updateSelectOptions(anggotaPengabdianSelect, selectedHki);
                    updateSelectOptions(anggotaHkiSelect, selectedPengabdian);
                },

                initDosenBaruLogic: function() {
                    // === KETUA SECTION ===
                    const showFormKetuaButton = $('#btn-tampilkan-form-dosen-baru-ketua');
                    const dosenBaruKetuaSection = $('#dosen-baru-ketua-section');
                    const ketuaNikSelect = $('#ketua_nik');
                    const ketuaNikRequired = $('#ketua-nik-required');
                    
                    // External ketua form inputs
                    const externalKetuaInputs = [
                        '#dosen_baru_ketua_nik',
                        '#dosen_baru_ketua_nama',
                        '#dosen_baru_ketua_nidn',
                        '#dosen_baru_ketua_jabatan',
                        '#dosen_baru_ketua_prodi',
                        '#dosen_baru_ketua_bidang_keahlian',
                        '#dosen_baru_ketua_email'
                    ];

                    // Function to check if external ketua form has any data
                    const hasExternalKetuaData = () => {
                        return externalKetuaInputs.some(selector => {
                            const val = $(selector).val();
                            return val && val.trim() !== '';
                        });
                    };

                    // Function to clear external ketua form
                    const clearExternalKetuaForm = () => {
                        externalKetuaInputs.forEach(selector => {
                            $(selector).val('').removeClass('is-invalid');
                        });
                        // Hide any validation errors
                        dosenBaruKetuaSection.find('.invalid-feedback').hide();
                    };

                    // Function to update mutual exclusivity state
                    const updateKetuaMutualExclusivity = () => {
                        const hasExternal = hasExternalKetuaData();
                        const hasInternal = ketuaNikSelect.val() && ketuaNikSelect.val() !== '';

                        if (hasExternal) {
                            // External ketua is filled - disable internal select
                            ketuaNikSelect.prop('disabled', true).addClass('bg-light');
                            ketuaNikSelect.val('').trigger('change.select2');
                            ketuaNikRequired.hide();
                            // Add visual indicator (only if not already present)
                            if (!$('#ketua-disabled-hint').length) {
                                ketuaNikSelect.closest('.form-group').find('label').first().append(
                                    ' <small class="text-muted" id="ketua-disabled-hint">(Dinonaktifkan karena Dosen Eksternal dipilih)</small>'
                                );
                            }
                        } else if (hasInternal) {
                            // Internal ketua is selected - clear and disable external form
                            clearExternalKetuaForm();
                            externalKetuaInputs.forEach(selector => {
                                $(selector).prop('disabled', true).addClass('bg-light');
                            });
                            // Add visual indicator to external form (only if not already present)
                            if (!$('#external-ketua-disabled-hint').length) {
                                dosenBaruKetuaSection.find('h6').first().append(
                                    ' <small class="text-muted" id="external-ketua-disabled-hint">(Dinonaktifkan karena Dosen FTI dipilih)</small>'
                                );
                            }
                        } else {
                            // Neither is filled - enable both
                            ketuaNikSelect.prop('disabled', false).removeClass('bg-light');
                            ketuaNikRequired.show();
                            $('#ketua-disabled-hint').remove();
                            
                            externalKetuaInputs.forEach(selector => {
                                $(selector).prop('disabled', false).removeClass('bg-light');
                            });
                            $('#external-ketua-disabled-hint').remove();
                        }
                    };

                    // Toggle external ketua form visibility
                    showFormKetuaButton.on('click', function() {
                        dosenBaruKetuaSection.toggle();
                        if (dosenBaruKetuaSection.is(':visible')) {
                            $(this).removeClass('btn-info').addClass('btn-secondary');
                            $(this).html('<i class="fas fa-minus fa-sm mr-1"></i> Sembunyikan Form Ketua');
                        } else {
                            $(this).removeClass('btn-secondary').addClass('btn-info');
                            $(this).html('<i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal (Ketua)');
                            // Clear form when hiding
                            clearExternalKetuaForm();
                            updateKetuaMutualExclusivity();
                        }
                    });

                    // Listen to internal ketua select changes
                    ketuaNikSelect.on('change', function() {
                        updateKetuaMutualExclusivity();
                    });

                    // Listen to external ketua input changes
                    externalKetuaInputs.forEach(selector => {
                        $(document).on('input change', selector, function() {
                            updateKetuaMutualExclusivity();
                        });
                    });

                    // Jika ada data 'old' untuk ketua, tampilkan formnya
                    @if (old('dosen_baru_ketua') && (old('dosen_baru_ketua.nik') || old('dosen_baru_ketua.nama')))
                        showFormKetuaButton.click();
                        updateKetuaMutualExclusivity();
                    @endif

                    // Initial state check
                    updateKetuaMutualExclusivity();

                    // === ANGGOTA SECTION ===
                    const showFormAnggotaButton = $('#btn-tampilkan-form-dosen-baru-anggota');
                    const dosenBaruAnggotaSection = $('#dosen-baru-anggota-section');
                    const anggotaContainer = $('#dosen-baru-anggota-container');
                    const anggotaTemplate = $('#dosen-baru-anggota-template');
                    const addAnggotaButton = $('#btn-tambah-dosen-baru-anggota');

                    const manageDeleteButtonsAnggota = () => {
                        const rows = anggotaContainer.find('.dosen-baru-anggota-item');
                        // Tampilkan dulu semua tombol hapus
                        rows.find('.btn-hapus-dosen-baru-anggota').show();
                        // Kemudian, sembunyikan tombol hapus HANYA pada baris pertama
                        rows.first().find('.btn-hapus-dosen-baru-anggota').hide();
                    };

                    const addAnggotaRow = () => {
                        const idx = new Date().getTime();
                        const newEl = anggotaTemplate.html().replace(/__INDEX__/g, idx);
                        anggotaContainer.append(newEl);
                        manageDeleteButtonsAnggota();
                    };

                    const clearAnggotaForm = () => {
                        // Clear all rows
                        anggotaContainer.empty();
                        // Remove validation errors
                        dosenBaruAnggotaSection.find('.invalid-feedback').hide();
                        dosenBaruAnggotaSection.find('.is-invalid').removeClass('is-invalid');
                    };

                    // Toggle button behavior (show/hide)
                    showFormAnggotaButton.on('click', function() {
                        if (dosenBaruAnggotaSection.is(':visible')) {
                            // Hide the form
                            dosenBaruAnggotaSection.hide();
                            $(this).removeClass('btn-secondary').addClass('btn-info');
                            $(this).html('<i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal (Anggota)');
                            // Clear form when hiding
                            clearAnggotaForm();
                        } else {
                            // Show the form
                            dosenBaruAnggotaSection.show();
                            $(this).removeClass('btn-info').addClass('btn-secondary');
                            $(this).html('<i class="fas fa-minus fa-sm mr-1"></i> Sembunyikan Form Anggota');
                            // Jika belum ada baris, tambahkan satu
                            if (anggotaContainer.children('.dosen-baru-anggota-item').length === 0) {
                                addAnggotaRow();
                            }
                        }
                    });

                    addAnggotaButton.on('click', addAnggotaRow);

                    // Event Delegation untuk Hapus
                    anggotaContainer.on('click', '.btn-hapus-dosen-baru-anggota', function() {
                        $(this).closest('.dosen-baru-anggota-item').remove();

                        // Cek jika kontainer kosong
                        if (anggotaContainer.children('.dosen-baru-anggota-item').length === 0) {
                            // Auto-hide when all rows are deleted
                            dosenBaruAnggotaSection.hide();
                            showFormAnggotaButton.removeClass('btn-secondary').addClass('btn-info');
                            showFormAnggotaButton.html('<i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal (Anggota)');
                        } else {
                            // Perbarui status tombol hapus jika ada baris tersisa
                            manageDeleteButtonsAnggota();
                        }
                    });

                    // Jika ada data 'old' untuk anggota, tampilkan formnya
                    @if (old('dosen_baru_anggota') && count(old('dosen_baru_anggota')) > 0)
                        const oldAnggota = @json(old('dosen_baru_anggota'));
                        let hasData = false;
                        for (let i = 0; i < oldAnggota.length; i++) {
                            if (oldAnggota[i].nik || oldAnggota[i].nama) {
                                hasData = true;
                                break;
                            }
                        }
                        if (hasData) {
                            showFormAnggotaButton.click();
                            manageDeleteButtonsAnggota();
                        }
                    @endif
                },


                initMahasiswaBaruLogic: function() {
                    const showFormButton = $('#btn-tampilkan-form-mhs-baru');
                    const mahasiswaBaruSection = $('#mahasiswa-baru-section');
                    const container = $('#mahasiswa-baru-container');
                    const template = $('#mahasiswa-baru-template');
                    const addButton = $('#btn-tambah-mhs-baru');
                    
                    const manageDeleteButtons = () => {
                        const rows = container.find('.mahasiswa-baru-item');
                        // Tampilkan dulu semua tombol hapus
                        rows.find('.btn-hapus-mhs-baru').show();
                        // Kemudian, sembunyikan tombol hapus HANYA pada baris pertama
                        rows.first().find('.btn-hapus-mhs-baru').hide();
                    };
                    
                    const addRow = () => {
                        const idx = new Date().getTime();
                        const templateHTML = template.html();

                        if (!templateHTML) {
                            return;
                        }

                        const processedHTML = templateHTML.replace(/__INDEX__/g, idx);
                        container.append(processedHTML);
                        manageDeleteButtons();
                    };
                    
                    const clearMahasiswaForm = () => {
                        // Clear all rows
                        container.empty();
                        // Remove validation errors
                        mahasiswaBaruSection.find('.invalid-feedback').hide();
                        mahasiswaBaruSection.find('.is-invalid').removeClass('is-invalid');
                    };
                    
                    // Toggle button behavior (show/hide)
                    showFormButton.on('click', function() {
                        if (mahasiswaBaruSection.is(':visible')) {
                            // Hide the form
                            mahasiswaBaruSection.hide();
                            $(this).removeClass('btn-secondary').addClass('btn-info');
                            $(this).html('<i class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa Baru (jika belum terdaftar)');
                            // Clear form when hiding
                            clearMahasiswaForm();
                        } else {
                            // Show the form
                            mahasiswaBaruSection.show();
                            $(this).removeClass('btn-info').addClass('btn-secondary');
                            $(this).html('<i class="fas fa-minus fa-sm mr-1"></i> Sembunyikan Form Mahasiswa');
                            // Jika belum ada baris, tambahkan satu
                            if (container.children('.mahasiswa-baru-item').length === 0) {
                                addRow();
                            }
                        }
                    });
                    
                    addButton.on('click', function() {
                        addRow();
                    });
                    
                    // Event Delegation untuk Hapus
                    container.on('click', '.btn-hapus-mhs-baru', function() {
                        $(this).closest('.mahasiswa-baru-item').remove();
                        
                        // Cek jika kontainer kosong
                        if (container.children('.mahasiswa-baru-item').length === 0) {
                            // Auto-hide when all rows are deleted
                            mahasiswaBaruSection.hide();
                            showFormButton.removeClass('btn-secondary').addClass('btn-info');
                            showFormButton.html('<i class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa Baru (jika belum terdaftar)');
                        } else {
                            // Perbarui status tombol hapus jika ada baris tersisa
                            manageDeleteButtons();
                        }
                    });
                    
                    // Jika ada data 'old', tampilkan formnya
                    @if (old('mahasiswa_baru'))
                        showFormButton.click();
                        manageDeleteButtons();
                    @endif
                },

                initSumberDanaLogic: function() {
                    this.populateExistingSumberDana();
                    $('#btn-tambah-sumber-dana').on('click', () => this.addSumberDanaItem());
                    $('#sumber-dana-container').on('click', '.btn-hapus-sumber-dana', (e) => this
                        .removeSumberDanaItem(e.currentTarget));
                    $('#sumber-dana-container').on('change', '.jenis-sumber-dana', (e) => this
                        .handleJenisChange(e.currentTarget));
                    $('#sumber-dana-container').on('input', '.jumlah-dana', (e) => this
                        .handleJumlahDanaInput(e.currentTarget));
                },

                populateExistingSumberDana: function() {
                    if ($('.sumber-dana-item').length === 0) {
                        this.addSumberDanaItem();
                    } else {
                        $('.sumber-dana-item').each((index, element) => {
                            const jenisSelect = $(element).find('.jenis-sumber-dana');
                            const namaSumberSelect = $(element).find('.nama-sumber-dana');
                            const jumlahDanaInput = $(element).find('.jumlah-dana');
                            this.updateNamaSumberOptions(namaSumberSelect, jenisSelect.val());
                            const savedNamaSumber = namaSumberSelect.data('selected-value');
                            if (savedNamaSumber) {
                                namaSumberSelect.val(savedNamaSumber);
                            }
                            if (jumlahDanaInput.val()) {
                                jumlahDanaInput.val(this.formatRupiah(jumlahDanaInput.val()));
                            }
                        });
                        this.calculateTotal();
                        this.toggleHapusButton();
                    }
                },

                handleJenisChange: function(element) {
                    const selectedJenis = $(element).val();
                    const namaSumberSelect = $(element).closest('.sumber-dana-item').find(
                        '.nama-sumber-dana');
                    this.updateNamaSumberOptions(namaSumberSelect, selectedJenis);
                },

                updateNamaSumberOptions: function(namaSumberSelect, selectedJenis) {
                    namaSumberSelect.empty().append(
                        '<option value="" disabled selected>— Pilih Sumber —</option>');
                    if (sumberDanaOptions[selectedJenis]) {
                        sumberDanaOptions[selectedJenis].forEach(sumber => {
                            namaSumberSelect.append($('<option>', {
                                value: sumber,
                                text: sumber
                            }));
                        });
                    }
                },

                addSumberDanaItem: function() {
                    const index = new Date().getTime();
                    const template = $('#sumber-dana-template').html().replace(/__INDEX__/g, index);
                    $('#sumber-dana-container').append(template);
                    this.toggleHapusButton();
                },

                removeSumberDanaItem: function(element) {
                    $(element).closest('.sumber-dana-item').remove();
                    this.toggleHapusButton();
                    this.calculateTotal();
                },

                toggleHapusButton: function() {
                    const rows = $('.sumber-dana-item');
                    rows.find('.btn-hapus-sumber-dana').show();
                    rows.first().find('.btn-hapus-sumber-dana').hide();
                },

                handleJumlahDanaInput: function(element) {
                    let value = $(element).val().replace(/[^0-9]/g, '');
                    $(element).val(this.formatRupiah(value));
                    this.calculateTotal();
                },

                calculateTotal: function() {
                    let total = 0;
                    $('.jumlah-dana').each(function() {
                        total += parseInt($(this).val().replace(/[^0-9]/g, '')) || 0;
                    });
                    $('#total_biaya').val(this.formatRupiah(total.toString()));
                },

                formatRupiah: function(angka) {
                    return (angka || '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                initLuaranLogic: function() {
                    $('.luaran-checkbox').on('change', () => {
                        const isHkiChecked = $('[value="HKI"]').is(':checked');
                        const detailHkiContainer = $('#detail-hki');

                        // Selalu nonaktifkan semua 'required' di dalam container HKI saat ada perubahan
                        detailHkiContainer.find('input, select').prop('required', false);

                        if (isHkiChecked) {
                            detailHkiContainer.show();
                            // Jadikan semua field teks dan select di dalam HKI menjadi wajib
                            detailHkiContainer.find('input:not([type="file"]), select').prop(
                                'required', true);

                            // LOGIKA BARU YANG DIPERBAIKI:
                            // Cek apakah sudah ada div '.current-file'. 
                            // Ini menandakan file lama sudah ada.
                            const hasExistingFile = detailHkiContainer.find('.current-file')
                                .length > 0;

                            if (!hasExistingFile) {
                                // Jika TIDAK ada file lama, maka input file BARU menjadi wajib.
                                detailHkiContainer.find('input[type="file"]').prop('required',
                                    true);
                            }

                        } else {
                            detailHkiContainer.hide();
                            // Pastikan semua field tidak wajib jika HKI tidak dicentang
                            detailHkiContainer.find('input, select').prop('required', false);
                        }
                        // Jalankan event ini saat halaman dimuat untuk mengatur status awal form
                    }).filter(':checked').trigger('change');
                },

                initFileInputs: function() {
                    // Biarkan fungsi ini HANYA bertanggung jawab untuk menampilkan nama file.
                    // Jangan ganggu proses submit.
                    $('.custom-file-input').on('change', function() {
                        let fileName = $(this).val().split('\\').pop();
                        $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                            'Pilih file...');
                    });
                }

                // initFileInputs: function() {
                //     $('.custom-file-input').on('change', function() {
                //         let fileName = $(this).val().split('\\').pop();
                //         $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                //             'Pilih file...');
                //         // Ensure the submit button is enabled after selecting a file
                //         const submitBtn = $('#pengabdianForm').find('button[type="submit"]');
                //         submitBtn.prop('disabled', false).removeClass('disabled');
                //         submitBtn.css('pointer-events', 'auto');
                //         console.log('[PengabdianForm] file selected, enabling submit button',
                //             submitBtn.length, 'disabled=', submitBtn.prop('disabled'));

                //         // Remove any stray modal backdrops that might cover the page
                //         if ($('.modal-backdrop').length) {
                //             console.log('[PengabdianForm] removing stray modal-backdrop elements:',
                //                 $('.modal-backdrop').length);
                //             $('.modal-backdrop').remove();
                //             $('body').removeClass('modal-open').css('padding-right', '');
                //         }

                //         // Defensive: attach a forced click handler to submit the form even if browser/overlay blocks native click
                //         submitBtn.off('click.force').on('click.force', function(e) {
                //             e.preventDefault();
                //             console.log('[PengabdianForm] forced submit triggered');
                //             var form = $('#pengabdianForm')[0];
                //             if (form) form.submit();
                //         });
                //         // Persist the selection event so we know the input had a file selected
                //         try {
                //             var fieldName = $(this).attr('name') || 'unknown';
                //             var inputFiles = this.files || [];
                //             var sel = JSON.parse(localStorage.getItem(
                //                 'last_pengabdian_file_selection') || 'null') || {
                //                 picks: []
                //             };
                //             var filesArr = [];
                //             for (var i = 0; i < inputFiles.length; i++) {
                //                 filesArr.push({
                //                     name: inputFiles[i].name,
                //                     size: inputFiles[i].size
                //                 });
                //             }
                //             sel.picks.push({
                //                 time: new Date().toISOString(),
                //                 field: fieldName,
                //                 files: filesArr
                //             });
                //             // keep only last 10 entries
                //             if (sel.picks.length > 10) sel.picks = sel.picks.slice(-10);
                //             localStorage.setItem('last_pengabdian_file_selection', JSON.stringify(
                //                 sel));
                //         } catch (e) {
                //             console.warn('Could not save file selection debug', e);
                //         }
                //     });
                //     // Defensive: make sure form submit button is enabled on page load
                //     $(function() {
                //         const submitBtn = $('#pengabdianForm').find('button[type="submit"]');
                //         submitBtn.prop('disabled', false).removeClass('disabled');
                //         submitBtn.css('pointer-events', 'auto');

                //         // remove leftover modal backdrops on load
                //         if ($('.modal-backdrop').length) {
                //             console.log('[PengabdianForm] removing modal-backdrop on load:', $(
                //                 '.modal-backdrop').length);
                //             $('.modal-backdrop').remove();
                //             $('body').removeClass('modal-open').css('padding-right', '');
                //         }
                //     });
                // }
            };

            // Initialize with error handling
            try {
                PengabdianForm.init();


            } catch (error) {
                // Silent error handling
            }

            // Small helper: if URL has ?highlight=slug then focus the corresponding file input
            (function() {
                var params = new URLSearchParams(window.location.search);
                var h = params.get('highlight');
                if (!h) return;
                var mapping = {
                    'laporan_akhir': 'dokumen[laporan_akhir]',
                    'surat_tugas': 'dokumen[surat_tugas]',
                    'surat_permohonan': 'dokumen[surat_permohonan]',
                    'ucapan_terima_kasih': 'dokumen[ucapan_terima_kasih]',
                    'kerjasama': 'dokumen[kerjasama]'
                };
                var inputName = mapping[h];
                if (!inputName) return;
                // Find input and add a brief visual highlight and focus
                var $input = $('input[type="file"][name="' + inputName + '"]');
                if ($input.length) {
                    // add class temporarily
                    $input.closest('.form-group').css('box-shadow', '0 0 0 3px rgba(78,115,223,0.15)');
                    setTimeout(function() {
                        $input.closest('.form-group').css('box-shadow', '');
                    }, 3000);
                    // scroll into view and focus the input
                    $('html,body').animate({
                        scrollTop: $input.offset().top - 120
                    }, 300, function() {
                        $input.focus();
                    });
                }
            })();
        });
    </script>
@endpush
