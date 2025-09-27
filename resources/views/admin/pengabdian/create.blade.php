@extends('admin.layouts.main')

@section('title', 'Form Pengabdian Masyarakat')

@push('styles')
    {{-- Dependensi CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    <style>
        /* == BAGIAN UMUM UNTUK SELECT2 == */
        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        /* Perataan vertikal untuk dropdown single-select */
        .select2-container .select2-selection--single {
            display: flex;
            align-items: center;
            height: calc(1.5em + 0.75rem + 2px);
        }

        /* Style untuk error (tidak berubah) */
        .is-invalid+.select2-container .select2-selection--single,
        .is-invalid+.select2-container .select2-selection--multiple {
            border-color: #e74a3b !important;
        }


        /* == KODE PERBAIKAN UNTUK MULTI-SELECT == */

        /* Menargetkan kotak multi-select secara paksa */
        .select2-container--bootstrap4 .select2-selection--multiple {
            display: flex !important;
            align-items: center !important;
            min-height: calc(1.5em + 0.75rem + 2px);
            padding: 0 8px !important;
        }

        /* Memastikan placeholder tidak memiliki margin yang mengganggu */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__placeholder {
            margin: 0 !important;
        }

        /* Style untuk tag/item yang dipilih (tidak berubah) */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.2rem 0.6rem;
            color: #495057;
            margin-right: 5px !important;
            margin-top: 4px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #6c757d;
            margin-left: 5px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc3545;
        }

        /* Mengubah warna teks untuk dropdown Ketua Dosen */
        #select2-ketua_nik-container {
            color: #858796;
        }

        #select2-id_luaran_wajib-container {
            color: #858796;
        }


        #mahasiswa-baru-section {
            display: none;
        }

        /* Selalu sembunyikan tombol hapus pada baris pertama sumber dana */
        #sumber-dana-container .sumber-dana-item:first-child .btn-hapus-sumber-dana {
            display: none;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Selalu sembunyikan tombol hapus di baris pertama mahasiswa baru */
        #mahasiswa-baru-container .mahasiswa-baru-item:first-child .btn-hapus-mhs-baru {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Input Pengabdian Masyarakat</h1>
        <a href="{{ route('admin.pengabdian.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <form id="pengabdianForm" action="{{ route('admin.pengabdian.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Informasi Utama --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt fa-fw mr-2"></i>Informasi Utama</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="judul_pengabdian">Judul Pengabdian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul_pengabdian') is-invalid @enderror"
                        id="judul_pengabdian" name="judul_pengabdian" value="{{ old('judul_pengabdian') }}" required
                        placeholder="Masukkan Judul Pengabdian">
                    @error('judul_pengabdian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_mitra">Nama Mitra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_mitra') is-invalid @enderror"
                                id="nama_mitra" name="nama_mitra" value="{{ old('nama_mitra') }}" required
                                placeholder="Masukkan Nama Mitra">
                            @error('nama_mitra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lokasi_kegiatan">Lokasi Kegiatan Mitra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lokasi_kegiatan') is-invalid @enderror"
                                id="lokasi_kegiatan" name="lokasi_kegiatan" value="{{ old('lokasi_kegiatan') }}" required
                                placeholder="Masukkan Lokasi Mitra">
                            @error('lokasi_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tanggal_pengabdian">Tanggal Pengabdian <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_pengabdian') is-invalid @enderror"
                        id="tanggal_pengabdian" name="tanggal_pengabdian" value="{{ old('tanggal_pengabdian') }}" required>
                    @error('tanggal_pengabdian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Tim Pengabdian --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users fa-fw mr-2"></i>Tim Pengabdian</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="ketua_nik">Pilih Dosen (Ketua) <span class="text-danger">*</span></label>
                    <select id="ketua_nik" name="ketua_nik" class="form-control @error('ketua_nik') is-invalid @enderror"
                        required>
                        <option value="" disabled selected>— Pilih Ketua — </option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->nik }}" {{ old('ketua_nik') == $d->nik ? 'selected' : '' }}>
                                {{ $d->nama }} - {{ $d->nidn }}</option>
                        @endforeach
                    </select>
                    @error('ketua_nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="dosen_ids">Pilih Dosen (Anggota)</label>
                            <select id="dosen_ids" name="dosen_ids[]"
                                class="form-control @error('dosen_ids.*') is-invalid @enderror" multiple
                                data-placeholder="Pilih satu atau lebih...">
                                @foreach ($dosen as $d)
                                    <option value="{{ $d->nik }}"
                                        {{ in_array($d->nik, old('dosen_ids', [])) ? 'selected' : '' }}>
                                        {{ $d->nama }} -
                                        {{ $d->nidn }}</option>
                                @endforeach
                            </select>
                            @error('dosen_ids.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mahasiswa Terlibat --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate fa-fw mr-2"></i>Mahasiswa yang
                    Terlibat</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="mahasiswa_ids">Pilih Mahasiswa</label>
                    <select id="mahasiswa_ids" name="mahasiswa_ids[]"
                        class="form-control @error('mahasiswa_ids.*') is-invalid @enderror" multiple
                        data-placeholder="Pilih satu atau lebih...">
                        @foreach ($mahasiswa as $m)
                            <option value="{{ $m->nim }}"
                                {{ in_array($m->nim, old('mahasiswa_ids', [])) ? 'selected' : '' }}>{{ $m->nama }} -
                                {{ $m->nim }}</option>
                        @endforeach
                    </select>
                    @error('mahasiswa_ids.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 1. TAMBAHKAN TOMBOL INI --}}
                <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-mhs-baru">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa Baru
                </button>

                {{-- 2. BUNGKUS BAGIAN INI DENGAN DIV BARU --}}
                <div id="mahasiswa-baru-section" style="display: none;">
                    <hr class="mt-4 mb-3">
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-plus-circle fa-fw mr-1"></i>Tambah
                        Mahasiswa Baru (Opsional)</h6>
                    @error('mahasiswa_baru')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div id="mahasiswa-baru-container">
                        @forelse (old('mahasiswa_baru', [['nim' => '', 'nama' => '', 'prodi' => '']]) as $index => $mhs)
                            <div class="row mahasiswa-baru-item mb-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0">
                                        <label>NIM</label>
                                        <input type="text" name="mahasiswa_baru[{{ $index }}][nim]"
                                            class="form-control @error('mahasiswa_baru.' . $index . '.nim') is-invalid @enderror"
                                            placeholder="NIM" value="{{ $mhs['nim'] ?? '' }}">
                                        @error('mahasiswa_baru.' . $index . '.nim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0">
                                        <label>Nama</label>
                                        <input type="text" name="mahasiswa_baru[{{ $index }}][nama]"
                                            class="form-control @error('mahasiswa_baru.' . $index . '.nama') is-invalid @enderror"
                                            placeholder="Nama mahasiswa" value="{{ $mhs['nama'] ?? '' }}">
                                        @error('mahasiswa_baru.' . $index . '.nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3"> {{-- Diubah ke col-md-3 --}}
                                    <div class="form-group mb-md-0">
                                        <label>Prodi</label>
                                        <input type="text" name="mahasiswa_baru[{{ $index }}][prodi]"
                                            class="form-control @error('mahasiswa_baru.' . $index . '.prodi') is-invalid @enderror"
                                            placeholder="Program Studi" value="{{ $mhs['prodi'] ?? '' }}">
                                        @error('mahasiswa_baru.' . $index . '.prodi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    {{-- KEMBALIKAN LOGIKA DISABLED INI --}}
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-mhs-baru">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-info mt-2" id="btn-tambah-mhs-baru"><i
                            class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa</button>
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
                    @forelse (old('sumber_dana', []) as $index => $dana)
                        <div class="row sumber-dana-item mb-3">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label>Jenis <span class="text-danger">*</span></label>
                                    <select name="sumber_dana[{{ $index }}][jenis]"
                                        class="form-control jenis-sumber-dana @error('sumber_dana.' . $index . '.jenis') is-invalid @enderror">
                                        <option value="" disabled {{ !isset($dana['jenis']) ? 'selected' : '' }}>—
                                            Pilih Jenis —</option>
                                        <option value="Internal"
                                            {{ ($dana['jenis'] ?? '') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="Eksternal"
                                            {{ ($dana['jenis'] ?? '') == 'Eksternal' ? 'selected' : '' }}>Eksternal
                                        </option>
                                    </select>
                                    @error('sumber_dana.' . $index . '.jenis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label>Nama Sumber <span class="text-danger">*</span></label>
                                    <select name="sumber_dana[{{ $index }}][nama_sumber]"
                                        class="form-control nama-sumber-dana @error('sumber_dana.' . $index . '.nama_sumber') is-invalid @enderror">
                                        {{-- Options are populated by JS --}}
                                    </select>
                                    @error('sumber_dana.' . $index . '.nama_sumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <label>Jumlah Dana <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="text" name="sumber_dana[{{ $index }}][jumlah_dana]"
                                            class="form-control jumlah-dana @error('sumber_dana.' . $index . '.jumlah_dana') is-invalid @enderror"
                                            placeholder="0" value="{{ $dana['jumlah_dana'] ?? '' }}">
                                    </div>
                                    @error('sumber_dana.' . $index . '.jumlah_dana')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-sumber-dana"><i
                                        class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <button type="button" class="btn btn-sm btn-info" id="btn-tambah-sumber-dana"><i
                        class="fas fa-plus fa-sm mr-1"></i> Tambah Sumber Dana</button>
                <hr>
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_biaya" class="font-weight-bold">Total Biaya Pengabdian</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="text" class="form-control" id="total_biaya" readonly placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Luaran Kegiatan --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks fa-fw mr-2"></i>Luaran Kegiatan</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="id_luaran_wajib">Luaran Wajib <span class="text-danger">*</span></label>
                    <select id="id_luaran_wajib" name="id_luaran_wajib"
                        class="form-control @error('id_luaran_wajib') is-invalid @enderror" required>
                        <option value="" disabled selected>— Pilih Luaran Wajib —</option>
                        @foreach ($luaranWajib as $lw)
                            <option value="{{ $lw->id_luaran_wajib }}"
                                {{ old('id_luaran_wajib') == $lw->id_luaran_wajib ? 'selected' : '' }}>
                                {{ $lw->nama_luaran }}</option>
                        @endforeach
                    </select>
                    @error('id_luaran_wajib')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <hr>
                <div class="form-group">
                    <label>Luaran Tambahan (Opsional)</label>
                    <div class="checkbox-group @error('luaran_jenis') is-invalid @enderror">
                        @foreach ($jenisLuaran as $jl)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input luaran-checkbox"
                                    id="luaran_{{ $jl->id_jenis_luaran }}" name="luaran_jenis[]"
                                    value="{{ $jl->nama_jenis_luaran }}"
                                    {{ in_array($jl->nama_jenis_luaran, old('luaran_jenis', [])) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="luaran_{{ $jl->id_jenis_luaran }}">{{ $jl->nama_jenis_luaran }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('luaran_jenis')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Detail HKI Container --}}
                <div id="detail-hki" class="luaran-detail"
                    style="{{ in_array('HKI', old('luaran_jenis', [])) ? '' : 'display:none;' }}">
                    <hr>
                    <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-copyright fa-fw mr-1"></i>Detail HKI
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hki_no_pendaftaran">Nomor Pendaftaran <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('luaran_data.HKI.no_pendaftaran') is-invalid @enderror"
                                    id="hki_no_pendaftaran" name="luaran_data[HKI][no_pendaftaran]"
                                    value="{{ old('luaran_data.HKI.no_pendaftaran') }}"
                                    placeholder="Masukkan nomor pendaftaran">
                                @error('luaran_data.HKI.no_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- KODE PENGGANTI UNTUK TANGGAL PERMOHONAN HKI --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hki_tanggal_permohonan">Tanggal Permohonan <span
                                        class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control @error('luaran_data.HKI.tanggal_permohonan') is-invalid @enderror"
                                    id="hki_tanggal_permohonan" name="luaran_data[HKI][tanggal_permohonan]"
                                    value="{{ old('luaran_data.HKI.tanggal_permohonan') }}">
                                @error('luaran_data.HKI.tanggal_permohonan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hki_judul_ciptaan">Judul Ciptaan <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('luaran_data.HKI.judul_ciptaan') is-invalid @enderror"
                            name="luaran_data[HKI][judul_ciptaan]" id="hki_judul_ciptaan"
                            value="{{ old('luaran_data.HKI.judul_ciptaan') }}">
                        @error('luaran_data.HKI.judul_ciptaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hki_pemegang_hak_cipta">Pemegang Hak Cipta <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('luaran_data.HKI.pemegang_hak_cipta') is-invalid @enderror"
                                    name="luaran_data[HKI][pemegang_hak_cipta]" id="hki_pemegang_hak_cipta"
                                    value="{{ old('luaran_data.HKI.pemegang_hak_cipta') }}">
                                @error('luaran_data.HKI.pemegang_hak_cipta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hki_jenis_ciptaan">Jenis Ciptaan <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('luaran_data.HKI.jenis_ciptaan') is-invalid @enderror"
                                    name="luaran_data[HKI][jenis_ciptaan]" id="hki_jenis_ciptaan"
                                    value="{{ old('luaran_data.HKI.jenis_ciptaan') }}">
                                @error('luaran_data.HKI.jenis_ciptaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hki_anggota_dosen">Anggota Pencipta (Dosen)</label>
                        <select id="hki_anggota_dosen" name="luaran_data[HKI][anggota_dosen][]"
                            class="form-control @error('luaran_data.HKI.anggota_dosen.*') is-invalid @enderror" multiple
                            data-placeholder="Pilih satu atau lebih dosen...">
                            @foreach ($dosen as $d)
                                <option value="{{ $d->nik }}"
                                    {{ in_array($d->nik, old('luaran_data.HKI.anggota_dosen', [])) ? 'selected' : '' }}>
                                    {{ $d->nama }} - {{ $d->nidn }}
                                </option>
                            @endforeach
                        </select>
                        @error('luaran_data.HKI.anggota_dosen.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dokumen_hki">Upload Dokumen HKI <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('dokumen.hki') is-invalid @enderror"
                                name="dokumen[hki]" id="dokumen_hki">
                            <label class="custom-file-label" for="dokumen_hki">Pilih file...</label>
                        </div>
                        @error('dokumen.hki')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maks: 5MB.</small>
                    </div>
                </div>
            </div>
        </div>


        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-upload fa-fw mr-2"></i>Upload Dokumen
                    Pendukung</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="dokumen_laporan_akhir">Laporan Akhir Lengkap</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="dokumen_laporan_akhir"
                            name="dokumen[laporan_akhir]" accept=".pdf,.doc,.docx">
                        <label class="custom-file-label" for="dokumen_laporan_akhir">Pilih file</label>
                    </div>
                    <small class="form-text text-muted">Format file: PDF, DOC, atau DOCX. Maksimal 10MB.</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label for="dokumen_surat_tugas">Surat Tugas Dosen</label>
                            <div class="custom-file"><input type="file" class="custom-file-input"
                                    id="dokumen_surat_tugas" name="dokumen[surat_tugas]" accept=".pdf,.doc,.docx"><label
                                    class="custom-file-label" for="dokumen_surat_tugas">Pilih file</label></div><small
                                class="form-text text-muted">Maksimal 5MB.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label for="dokumen_surat_permohonan">Surat Permohonan</label>
                            <div class="custom-file"><input type="file" class="custom-file-input"
                                    id="dokumen_surat_permohonan" name="dokumen[surat_permohonan]"
                                    accept=".pdf,.doc,.docx"><label class="custom-file-label"
                                    for="dokumen_surat_permohonan">Pilih file</label></div><small
                                class="form-text text-muted">Maksimal 5MB.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label for="dokumen_ucapan_terima_kasih">Surat Ucapan Terima Kasih</label>
                            <div class="custom-file"><input type="file" class="custom-file-input"
                                    id="dokumen_ucapan_terima_kasih" name="dokumen[ucapan_terima_kasih]"
                                    accept=".pdf,.doc,.docx"><label class="custom-file-label"
                                    for="dokumen_ucapan_terima_kasih">Pilih file</label></div><small
                                class="form-text text-muted">Maksimal 5MB.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label for="dokumen_kerjasama">MoU/MoA/Dokumen Kerja Sama</label>
                            <div class="custom-file"><input type="file" class="custom-file-input"
                                    id="dokumen_kerjasama" name="dokumen[kerjasama]" accept=".pdf,.doc,.docx"><label
                                    class="custom-file-label" for="dokumen_kerjasama">Pilih file</label></div><small
                                class="form-text text-muted">Maksimal 5MB.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.pengabdian.index') }}" class="btn btn-secondary mr-2">Batal</a>
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-save fa-sm mr-1"></i> Simpan Data
            </button>
        </div>
    </form>

    {{-- Template untuk Mahasiswa Baru (tidak berubah) --}}
    <script type="text/html" id="mahasiswa-baru-template">
        <div class="row mahasiswa-baru-item mb-3">
            <div class="col-md-4">
                <div class="form-group mb-md-0"><label>Masukkan NIM Mahasiswa Baru</label><input type="text" name="mahasiswa_baru[__INDEX__][nim]" class="form-control" placeholder="NIM"></div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-md-0"><label>Masukkan Nama Mahasiswa Baru</label><input type="text" name="mahasiswa_baru[__INDEX__][nama]" class="form-control" placeholder="Nama mahasiswa"></div>
            </div>
            {{-- DI SINI KESALAHANNYA, UBAH KE col-md-3 --}}
            <div class="col-md-3">
                <div class="form-group mb-md-0"><label>Masukkan Prodi Mahasiswa Baru</label><input type="text" name="mahasiswa_baru[__INDEX__][prodi]" class="form-control" placeholder="Program Studi"></div>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm btn-hapus-mhs-baru"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </script>

    {{-- Template untuk Sumber Dana (tidak berubah) --}}
    <script type="text/html" id="sumber-dana-template">
        <div class="row sumber-dana-item mb-3">
            <div class="col-md-3"><div class="form-group mb-0"><label>Jenis <span class="text-danger">*</span></label><select name="sumber_dana[__INDEX__][jenis]" class="form-control jenis-sumber-dana" required><option value="" disabled selected>— Pilih Jenis —</option><option value="Internal">Internal</option><option value="Eksternal">Eksternal</option></select></div></div>
            <div class="col-md-4"><div class="form-group mb-0"><label>Nama Sumber <span class="text-danger">*</span></label><select name="sumber_dana[__INDEX__][nama_sumber]" class="form-control nama-sumber-dana" required><option value="" disabled selected>— Pilih Sumber —</option><option value="LPPM" data-jenis="Internal">LPPM</option><option value="Universitas" data-jenis="Internal">Universitas</option><option value="Fakultas" data-jenis="Internal">Fakultas</option><option value="Prodi" data-jenis="Internal">Prodi</option><option value="Mandiri" data-jenis="Internal">Mandiri</option><option value="DRPM" data-jenis="Eksternal">DRPM</option><option value="Swasta" data-jenis="Eksternal">Swasta</option><option value="Gereja" data-jenis="Eksternal">Gereja</option><option value="LSM" data-jenis="Eksternal">LSM</option><option value="Sekolah" data-jenis="Eksternal">Sekolah</option><option value="Fakultas Bisnis" data-jenis="Eksternal">Fakultas Bisnis</option><option value="Lainnya" data-jenis="Eksternal">Lainnya</option></select></div></div>
            <div class="col-md-3"><div class="form-group mb-0"><label>Jumlah Dana <span class="text-danger">*</span></label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">Rp</span></div><input type="text" name="sumber_dana[__INDEX__][jumlah_dana]" class="form-control jumlah-dana" placeholder="0" required></div></div></div>
            <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-danger btn-sm btn-hapus-sumber-dana"><i class="fas fa-trash"></i></button></div>
        </div>
    </script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>

    {{-- 2. Logika utama script --}}
    <script>
        // Data untuk opsi dropdown Nama Sumber
        const sumberDanaOptions = {
            'Internal': ['LPPM', 'Universitas', 'Fakultas', 'Prodi', 'Mandiri'],
            'Eksternal': ['DRPM', 'Swasta', 'Gereja', 'LSM', 'Sekolah', 'Fakultas Bisnis', 'Lainnya']
        };

        $(document).ready(function() {

            @if (session('success'))
                Swal.fire({
                    icon: 'success', // Ikon centang hijau
                    title: 'Berhasil!',
                    text: "{{ session('success') }}", // Ambil pesan dari session
                    timer: 2500, // Pop-up akan hilang setelah 2.5 detik
                    showConfirmButton: false // Sembunyikan tombol OK
                });

                // Cek 2: Jika tidak, apakah ada pesan 'error' umum?
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error', // Ikon silang merah
                    title: 'Terjadi Kesalahan!',
                    text: "{{ session('error') }}", // Ambil pesan dari session
                });

                // Cek 3: Jika tidak ada keduanya, apakah ada 'error' validasi?
            @elseif ($errors->any())
                const errorMessages = @json($errors->all());
                let formattedErrors = '<ul class="text-left mb-0" style="padding-left: 1.2em;">';
                errorMessages.forEach(function(message) {
                    formattedErrors += '<li>' + message + '</li>';
                });
                formattedErrors += '</ul>';

                Swal.fire({
                    icon: 'error', // Ikon silang merah
                    title: 'Input Tidak Valid',
                    html: formattedErrors, // Tampilkan daftar error validasi
                });
            @endif

            const PengabdianForm = {

                init: function() {
                    this.initPlugins();
                    this.initDosenLogic();
                    this.initMahasiswaBaruLogic();
                    this.initSumberDanaLogic();
                    this.initLuaranLogic();
                    this.initFileInputs();
                },

                initPlugins: function() {

                    $('#ketua_nik, #id_luaran_wajib').select2({
                        width: '100%'
                    });

                    $('#dosen_ids, #mahasiswa_ids, #hki_anggota_dosen').select2({
                        width: '100%',
                        placeholder: function() {
                            return $(this).data('placeholder');
                        }
                    });
                },

                initDosenLogic: function() {
                    // Event listener sekarang memantau ketiga dropdown dosen
                    $('#ketua_nik, #dosen_ids, #hki_anggota_dosen').on('change', () => this
                        .updateDosenRoles());
                    this.updateDosenRoles(); // Panggil saat halaman dimuat
                },

                // ===== FUNGSI YANG DIPERBARUI =====
                updateDosenRoles: function() {
                    const ketuaNik = $('#ketua_nik').val();
                    const anggotaPengabdianSelect = $('#dosen_ids');
                    const anggotaHkiSelect = $('#hki_anggota_dosen');

                    const selectedPengabdian = anggotaPengabdianSelect.val() || [];
                    const selectedHki = anggotaHkiSelect.val() || [];

                    // Fungsi terpisah untuk memperbarui satu dropdown
                    const updateSelectOptions = (currentSelect, otherSelectedNiks) => {
                        // Dosen yang dinonaktifkan adalah KETUA dan yang sudah dipilih di dropdown LAINNYA
                        const niksToDisable = [ketuaNik, ...otherSelectedNiks].filter(Boolean);

                        currentSelect.find('option').each(function() {
                            const option = $(this);
                            const optionNik = option.val();

                            if (!optionNik) return; // Lewati placeholder

                            // Nonaktifkan jika NIK ada di daftar niksToDisable
                            if (niksToDisable.includes(optionNik)) {
                                option.prop('disabled', true);
                            } else {
                                option.prop('disabled', false);
                            }
                        });

                        // Validasi akhir: pastikan NIK Ketua tidak ada di nilai yang terpilih
                        let currentValues = currentSelect.val() || [];
                        if (Array.isArray(currentValues) && currentValues.includes(ketuaNik)) {
                            currentValues = currentValues.filter(nik => nik !== ketuaNik);
                            currentSelect.val(currentValues);
                        }

                        // Refresh Select2 untuk menerapkan perubahan
                        currentSelect.trigger('change.select2');
                    };

                    // Perbarui dropdown Anggota Pengabdian berdasarkan pilihan di Anggota HKI
                    updateSelectOptions(anggotaPengabdianSelect, selectedHki);

                    // Perbarui dropdown Anggota HKI berdasarkan pilihan di Anggota Pengabdian
                    updateSelectOptions(anggotaHkiSelect, selectedPengabdian);
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
                        const newEl = template.html().replace(/__INDEX__/g, idx);
                        container.append(newEl);
                        manageDeleteButtons();
                    };

                    showFormButton.on('click', function() {
                        mahasiswaBaruSection.show();
                        $(this).hide();
                        // Jika belum ada baris, tambahkan satu
                        if (container.children('.mahasiswa-baru-item').length === 0) {
                            addRow();
                        }
                    });

                    addButton.on('click', addRow);

                    // Event Delegation untuk Hapus
                    container.on('click', '.btn-hapus-mhs-baru', function() {
                        $(this).closest('.mahasiswa-baru-item').remove();

                        // Cek jika kontainer kosong
                        if (container.children('.mahasiswa-baru-item').length === 0) {
                            mahasiswaBaruSection.hide();
                            showFormButton.show();
                        } else {
                            // Perbarui status tombol hapus jika ada baris tersisa
                            manageDeleteButtons();
                        }
                    });

                    // Jika ada data 'old', tampilkan formnya
                    @if (old('mahasiswa_baru') && count(array_filter(old('mahasiswa_baru')[0])))
                        showFormButton.click();
                        manageDeleteButtons
                            (); // Panggil ini untuk memastikan tombol disabled jika hanya satu baris
                    @endif
                },

                initSumberDanaLogic: function() {
                    $('#btn-tambah-sumber-dana').on('click', () => this.addSumberDanaItem());
                    $('#sumber-dana-container').on('click', '.btn-hapus-sumber-dana', (e) => this
                        .removeSumberDanaItem(e.currentTarget));
                    $('#sumber-dana-container').on('change', '.jenis-sumber-dana', (e) => this
                        .handleJenisChange(e.currentTarget));
                    $('#sumber-dana-container').on('input', '.jumlah-dana', (e) => this
                        .handleJumlahDanaInput(e.currentTarget));
                    $('#sumber-dana-container').on('change', '.nama-sumber-dana', () => this
                        .updateDuplicateOptions());
                    this.handleOldSumberDana(); // Tangani data lama saat halaman dimuat
                },

                updateDuplicateOptions: function() {
                    const selectedValues = new Set();
                    // 1. Kumpulkan semua nilai yang sudah dipilih
                    $('.nama-sumber-dana').each(function() {
                        const value = $(this).val();
                        if (value) {
                            selectedValues.add(value);
                        }
                    });

                    // 2. Loop ke setiap dropdown lagi untuk menyembunyikan/menampilkan opsi
                    $('.nama-sumber-dana').each(function() {
                        const currentDropdown = $(this);
                        const currentValue = currentDropdown.val();

                        currentDropdown.find('option').each(function() {
                            const option = $(this);
                            const optionValue = option.val();

                            // Logika yang diubah:
                            // Sembunyikan opsi jika nilainya ada di daftar pilihan lain
                            if (selectedValues.has(optionValue) && optionValue !==
                                currentValue) {
                                option
                                    .hide(); // <-- PERUBAHAN DI SINI: dari .prop('disabled', true) menjadi .hide()
                            } else {
                                option
                                    .show(); // <-- PERUBAHAN DI SINI: dari .prop('disabled', false) menjadi .show()
                            }
                        });
                    });
                },


                handleJenisChange: function(element) {
                    const selectedJenis = $(element).val();
                    const namaSumberSelect = $(element).closest('.sumber-dana-item').find(
                        '.nama-sumber-dana');
                    this.updateNamaSumberOptions(namaSumberSelect, selectedJenis);
                    this.updateDuplicateOptions(); // <-- BARIS INI DITAMBAHKAN
                },

                updateNamaSumberOptions: function(namaSumberSelect, selectedJenis) {
                    namaSumberSelect.empty().append(
                        '<option value="" disabled selected>— Pilih Sumber —</option>');
                    if (sumberDanaOptions[selectedJenis]) {
                        sumberDanaOptions[selectedJenis].forEach(function(sumber) {
                            namaSumberSelect.append($('<option>', {
                                value: sumber,
                                text: sumber
                            }));
                        });
                    }
                },

                addSumberDanaItem: function(data = {}) {
                    const index = $('.sumber-dana-item').length;
                    const template = $('#sumber-dana-template').html().replace(/__INDEX__/g, index);
                    const newRow = $(template);

                    $('#sumber-dana-container').append(newRow);
                    this.toggleHapusButton();
                    this.updateDuplicateOptions(); // <-- BARIS INI DITAMBAHKAN

                },

                removeSumberDanaItem: function(element) {
                    $(element).closest('.sumber-dana-item').remove();
                    this.toggleHapusButton();
                    this.calculateTotal();
                    this.updateDuplicateOptions(); // <-- BARIS INI DITAMBAHKAN

                    // V-- TAMBAHKAN BARIS INI DI AKHIR FUNGSI --V
                    $('#sumber-dana-container .sumber-dana-item:first-child .btn-hapus-sumber-dana').hide();
                },

                toggleHapusButton: function() {
                    const rows = $('.sumber-dana-item');

                    // Tampilkan dulu semua tombol hapus
                    rows.find('.btn-hapus-sumber-dana').show();

                    // Kemudian, secara spesifik sembunyikan tombol hapus HANYA pada baris pertama
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
                        const value = parseInt($(this).val().replace(/[^0-9]/g, '')) || 0;
                        total += value;
                    });
                    $('#total_biaya').val(this.formatRupiah(total.toString()));
                },

                formatRupiah: function(angka) {
                    return (angka || '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                handleOldSumberDana: function() {
                    const oldSumberDana = @json(old('sumber_dana', null));
                    if (oldSumberDana === null || oldSumberDana.length === 0) {
                        this.addSumberDanaItem(); // Tambah satu baris kosong jika tidak ada data lama
                    } else {
                        // Jika ada data lama, Blade sudah merender barisnya.
                        // Kita hanya perlu memicu logika JS untuk setiap baris.
                        $('.sumber-dana-item').each((index, element) => {
                            const jenisSelect = $(element).find('.jenis-sumber-dana');
                            const namaSumberSelect = $(element).find('.nama-sumber-dana');
                            const jumlahDanaInput = $(element).find('.jumlah-dana');

                            // 1. Perbarui opsi 'Nama Sumber' berdasarkan 'Jenis' yang sudah terpilih
                            this.updateNamaSumberOptions(namaSumberSelect, jenisSelect.val());

                            // 2. Pilih kembali 'Nama Sumber' yang lama
                            const oldNamaSumber = oldSumberDana[index] ? oldSumberDana[index]
                                .nama_sumber : '';
                            if (oldNamaSumber) {
                                namaSumberSelect.val(oldNamaSumber);
                            }

                            // 3. (INI BAGIAN BARUNYA) Format 'Jumlah Dana' yang lama
                            if (jumlahDanaInput.val()) {
                                jumlahDanaInput.val(this.formatRupiah(jumlahDanaInput.val()));
                            }
                        });
                        this.toggleHapusButton();
                        this.calculateTotal();
                        this.updateDuplicateOptions(); // <-- BARIS INI DITAMBAHKAN DI AKHIR
                    }
                },

                initLuaranLogic: function() {
                    $('.luaran-checkbox').on('change', () => {
                        let isHkiChecked = false;
                        $('.luaran-checkbox:checked').each(function() {
                            if ($(this).val().toUpperCase().includes('HKI') || $(this).val()
                                .toUpperCase().includes('HAK CIPTA')) {
                                isHkiChecked = true;
                            }
                        });

                        const detailHkiContainer = $('#detail-hki');
                        detailHkiContainer.toggle(isHkiChecked);
                        detailHkiContainer.find('input, select').prop('required', isHkiChecked);

                    }).filter(':checked').trigger('change');
                },

                initFileInputs: function() {
                    $('.custom-file-input').on('change', function() {
                        let fileName = $(this).val().split('\\').pop();
                        $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                            'Pilih file...');
                    });
                }
            };

            PengabdianForm.init();
        });
    </script>
@endpush
