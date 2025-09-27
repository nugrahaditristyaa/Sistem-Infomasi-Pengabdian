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

        .current-file a {
            font-weight: 600;
            color: #4e73df;
        }

        #select2-ketua_nik-container,
        #select2-id_luaran_wajib-container {
            color: #858796;
        }
    </style>
@endpush

@section('content')
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

        <!-- Debug panel: shows last captured FormData summary (saved to localStorage) -->
        <div id="last-submit-debug" style="display:none;margin-bottom:1rem;">
            <div class="alert alert-warning" role="alert">
                <strong>Debug info:</strong>
                <div id="last-submit-debug-content" style="margin-top:6px;font-size:13px;color:#856404"></div>
                <div style="margin-top:8px;">
                    <button type="button" id="copy-debug-json" class="btn btn-sm btn-outline-secondary">Salin JSON</button>
                    <button type="button" id="clear-debug-json" class="btn btn-sm btn-outline-danger">Hapus</button>
                </div>
                <div class="small text-muted mt-2">Catatan: info ini hanya bersifat diagnostik dan disimpan di browser Anda
                    (localStorage).</div>
            </div>
        </div>

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
                                value="{{ old('nama_mitra', optional($pengabdian->mitra->first())->nama_mitra) }}" required>
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
                                value="{{ old('lokasi_kegiatan', optional($pengabdian->mitra->first())->lokasi_mitra) }}"
                                required>
                            @error('lokasi_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tanggal_pengabdian">Tanggal Pengabdian <span class="text-danger">*</span></label>
                    {{-- PERBAIKAN 1: Menggunakan class 'datepicker' untuk Flatpickr --}}
                    <input type="text" class="form-control datepicker @error('tanggal_pengabdian') is-invalid @enderror"
                        id="tanggal_pengabdian" name="tanggal_pengabdian"
                        value="{{ old('tanggal_pengabdian', $pengabdian->tanggal_pengabdian) }}" required>
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
                        <option value="" disabled>— Pilih Ketua — </option>
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
                <div class="form-group">
                    <label for="dosen_ids">Pilih Dosen (Anggota)</label>
                    @php
                        $anggota_ids = $pengabdian->dosen
                            ->where('pivot.status_anggota', 'anggota')
                            ->pluck('nik')
                            ->toArray();
                    @endphp
                    <select id="dosen_ids" name="dosen_ids[]"
                        class="form-control @error('dosen_ids.*') is-invalid @enderror" multiple
                        data-placeholder="Pilih satu atau lebih...">
                        @foreach ($dosen as $d)
                            <option value="{{ $d->nik }}"
                                {{ in_array($d->nik, old('dosen_ids', $anggota_ids)) ? 'selected' : '' }}>
                                {{ $d->nama }} - {{ $d->nidn }}
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_ids.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
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
                <button type="button" class="btn btn-sm btn-outline-info" id="btn-tampilkan-form-mhs-baru">
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
                                            name="mahasiswa_baru[{{ $index }}][nim]" class="form-control"
                                            placeholder="NIM" value="{{ $mhs['nim'] ?? '' }}"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-md-0"><label>Nama</label><input type="text"
                                            name="mahasiswa_baru[{{ $index }}][nama]" class="form-control"
                                            placeholder="Nama" value="{{ $mhs['nama'] ?? '' }}"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-md-0"><label>Prodi</label><input type="text"
                                            name="mahasiswa_baru[{{ $index }}][prodi]" class="form-control"
                                            placeholder="Prodi" value="{{ $mhs['prodi'] ?? '' }}"></div>
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
                <div class="form-group">
                    <label for="id_luaran_wajib">Luaran Wajib <span class="text-danger">*</span></label>
                    <select id="id_luaran_wajib" name="id_luaran_wajib"
                        class="form-control @error('id_luaran_wajib') is-invalid @enderror" required>
                        @foreach ($luaranWajib as $lw)
                            <option value="{{ $lw->id_luaran_wajib }}"
                                {{ old('id_luaran_wajib', $pengabdian->id_luaran_wajib) == $lw->id_luaran_wajib ? 'selected' : '' }}>
                                {{ $lw->nama_luaran }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_luaran_wajib')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <hr>
                <div class="form-group">
                    <label>Luaran Tambahan (Opsional)</label>
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
                            {{-- PERBAIKAN 2: Menggunakan class 'datepicker' dan Carbon untuk format --}}
                            <input type="text" class="form-control datepicker"
                                name="luaran_data[HKI][tanggal_permohonan]"
                                value="{{ old('luaran_data.HKI.tanggal_permohonan', optional($detailHki)->tgl_permohonan ? \Carbon\Carbon::parse($detailHki->tgl_permohonan)->format('Y-m-d') : '') }}"
                                required>
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
            <button type="submit" class="btn btn-primary shadow-sm">
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
        // Diagnostic: capture form submit and record form data (files) to localStorage and a visible banner
        (function() {
            var form = document.getElementById('pengabdianForm');
            if (!form) return;

            function humanFileSize(bytes) {
                if (bytes === 0) return '0 B';
                var i = Math.floor(Math.log(bytes) / Math.log(1024));
                var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
            }

            form.addEventListener('submit', function(evt) {
                try {
                    var fd = new FormData(form);
                    var summary = {
                        files: [],
                        fields: {}
                    };
                    for (var pair of fd.entries()) {
                        var key = pair[0];
                        var val = pair[1];
                        if (val instanceof File) {
                            if (val && val.name) {
                                summary.files.push({
                                    field: key,
                                    name: val.name,
                                    size: val.size,
                                    human: humanFileSize(val.size)
                                });
                            }
                        } else {
                            // for big fields, only store length
                            var str = String(val || '');
                            summary.fields[key] = str.length > 100 ? str.slice(0, 100) + '...(' + str.length +
                                ' chars)' : str;
                        }
                    }

                    // Persist to localStorage so it's available after navigation
                    try {
                        localStorage.setItem('last_pengabdian_submit_debug', JSON.stringify({
                            time: new Date().toISOString(),
                            summary: summary
                        }));
                    } catch (e) {
                        console.warn('Could not save debug info to localStorage', e);
                    }

                    // Show a small in-page banner so the user can see and copy quickly
                    var banner = document.getElementById('submit-debug-banner');
                    if (!banner) {
                        banner = document.createElement('div');
                        banner.id = 'submit-debug-banner';
                        banner.style.position = 'fixed';
                        banner.style.right = '20px';
                        banner.style.top = '80px';
                        banner.style.zIndex = 2147483647;
                        banner.style.background = '#fff3cd';
                        banner.style.border = '1px solid #ffeeba';
                        banner.style.padding = '12px 16px';
                        banner.style.boxShadow = '0 2px 6px rgba(0,0,0,0.12)';
                        banner.style.fontSize = '13px';
                        banner.style.color = '#856404';
                        document.body.appendChild(banner);
                    }
                    var html = '<strong>Debug: form submission captured</strong><br/>';
                    if (summary.files.length) {
                        html += '<div style="margin-top:6px;"><em>Files:</em><ul style="margin:6px 0 0 18px;">';
                        summary.files.forEach(function(f) {
                            html += '<li>' + f.field + ': ' + f.name + ' (' + f.human + ')</li>';
                        });
                        html += '</ul></div>';
                    } else {
                        html += '<div style="margin-top:6px;">No files detected in FormData (0)</div>';
                    }
                    html +=
                        '<div style="margin-top:6px;font-size:12px;color:#6c757d;">Saved to localStorage key: <code>last_pengabdian_submit_debug</code></div>';
                    banner.innerHTML = html;

                    // leave banner visible for a few seconds so user can copy
                    setTimeout(function() {
                        try {
                            banner.parentNode && banner.parentNode.removeChild(banner);
                        } catch (e) {}
                    }, 8000);

                } catch (err) {
                    console.error('Error collecting submit debug info', err);
                }
                // allow the form to submit normally
            }, true);
        })();
    </script>
    <script>
        // Capture-level submit snapshot (v2) - logs inputs, file lists and FormData entries
        (function() {
            function humanFileSize(bytes) {
                if (bytes === 0) return '0 B';
                var i = Math.floor(Math.log(bytes) / Math.log(1024));
                var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
            }

            function snapshotForm(formEl) {
                try {
                    var snap = {
                        time: new Date().toISOString(),
                        location: window.location.href,
                        ua: navigator.userAgent,
                        formId: formEl.id || null,
                        inputs: [],
                        formDataHasFiles: false,
                        formDataEntries: []
                    };
                    // inputs
                    var inputs = formEl.querySelectorAll('input, select, textarea');
                    inputs.forEach(function(inp) {
                        try {
                            var obj = {
                                name: inp.name || null,
                                type: inp.type || inp.tagName,
                                disabled: inp.disabled || false
                            };
                            if (inp.type === 'file') {
                                obj.files = [];
                                var fls = inp.files || [];
                                for (var i = 0; i < fls.length; i++) {
                                    obj.files.push({
                                        name: fls[i].name,
                                        size: fls[i].size,
                                        human: humanFileSize(fls[i].size)
                                    });
                                }
                            } else {
                                obj.valuePreview = (inp.value || '').toString().slice(0, 200);
                            }
                            snap.inputs.push(obj);
                        } catch (e) {
                            /* ignore per-input */
                        }
                    });

                    // FormData entries
                    try {
                        var fd = new FormData(formEl);
                        for (var pair of fd.entries()) {
                            var key = pair[0];
                            var val = pair[1];
                            if (val instanceof File) {
                                snap.formDataHasFiles = true;
                                snap.formDataEntries.push({
                                    key: key,
                                    fileName: val.name,
                                    size: val.size,
                                    human: humanFileSize(val.size)
                                });
                            } else {
                                snap.formDataEntries.push({
                                    key: key,
                                    valuePreview: (val || '').toString().slice(0, 200)
                                });
                            }
                        }
                    } catch (e) {
                        snap.formDataError = String(e);
                    }

                    try {
                        localStorage.setItem('last_pengabdian_submit_debug_v2', JSON.stringify(snap));
                    } catch (e) {
                        console.warn('save v2 failed', e);
                    }
                    // also put into previous key for older UI
                    try {
                        localStorage.setItem('last_pengabdian_submit_debug', JSON.stringify({
                            time: snap.time,
                            summary: {
                                files: snap.formDataEntries.filter(function(e) {
                                    return e.fileName
                                }).map(function(f) {
                                    return {
                                        field: f.key,
                                        name: f.fileName,
                                        size: f.size,
                                        human: f.human
                                    };
                                })
                            }
                        }));
                    } catch (e) {}
                    console.log('[DEBUG v2] snapshot saved', snap);
                    return snap;
                } catch (e) {
                    console.error('snapshot error', e);
                    return null;
                }
            }

            // Listen at document capture phase to ensure we see submit before other handlers
            document.addEventListener('submit', function(ev) {
                try {
                    var formEl = ev.target && (ev.target.tagName === 'FORM' ? ev.target : ev.target.closest &&
                        ev.target.closest('form'));
                    if (!formEl) return;
                    snapshotForm(formEl);
                } catch (e) {
                    console.warn('submit-capture fail', e);
                }
                // do not prevent submission
            }, true);

            // Also log when the submit button is clicked
            document.addEventListener('click', function(ev) {
                try {
                    var btn = ev.target.closest && ev.target.closest(
                        'button[type="submit"], input[type="submit"]');
                    if (!btn) return;
                    var formEl = btn.form || document.getElementById('pengabdianForm');
                    if (!formEl) return;
                    // snapshot immediately
                    snapshotForm(formEl);
                } catch (e) {
                    console.warn('submit-click-capture fail', e);
                }
            }, true);
        })();
    </script>
    <!-- Floating debug button (always visible) -->
    <button id="show-last-debug-btn" title="Tampilkan debug terakhir"
        style="position:fixed;right:18px;bottom:18px;z-index:2147483647;background:#ffc107;border:0;padding:10px 12px;border-radius:50px;box-shadow:0 2px 8px rgba(0,0,0,0.15);color:#212529;font-weight:600">DBG</button>

    <!-- Persistent banner container for debug (hidden by default) -->
    <div id="submit-debug-banner-container"
        style="display:none;position:fixed;right:20px;top:80px;z-index:2147483647;background:#fff3cd;border:1px solid #ffeeba;padding:12px 16px;box-shadow:0 2px 6px rgba(0,0,0,0.12);font-size:13px;color:#856404;max-width:360px;">
        <div id="submit-debug-banner-inner"></div>
        <div style="margin-top:8px;text-align:right;"><button id="close-submit-debug-banner"
                class="btn btn-sm btn-light">Tutup</button></div>
    </div>

    <script>
        (function() {
            function humanFileSize(bytes) {
                if (bytes === 0) return '0 B';
                var i = Math.floor(Math.log(bytes) / Math.log(1024));
                var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
            }

            function renderDebugInto(container, raw) {
                try {
                    var parsed = JSON.parse(raw);
                } catch (e) {
                    container.innerText = 'Tidak dapat membaca debug data';
                    return;
                }
                var html = '<div><strong>Waktu:</strong> ' + (parsed.time || '') + '</div>';
                if (parsed.summary && parsed.summary.files && parsed.summary.files.length) {
                    html += '<div style="margin-top:6px;"><em>Files:</em><ul style="margin:6px 0 0 18px;">';
                    parsed.summary.files.forEach(function(f) {
                        html += '<li>' + f.field + ': ' + f.name + ' (' + (f.human || humanFileSize(f.size ||
                            0)) + ')</li>';
                    });
                    html += '</ul></div>';
                } else {
                    html += '<div style="margin-top:6px;">No files captured in last submit.</div>';
                }
                html +=
                    '<div style="margin-top:6px;font-size:12px;color:#6c757d;">localStorage key: <code>last_pengabdian_submit_debug</code></div>';
                container.innerHTML = html;
            }

            document.getElementById('show-last-debug-btn').addEventListener('click', function() {
                try {
                    var raw = localStorage.getItem('last_pengabdian_submit_debug');
                    var banner = document.getElementById('submit-debug-banner-container');
                    var inner = document.getElementById('submit-debug-banner-inner');
                    if (!raw) {
                        inner.innerHTML =
                            '<div><strong>Tidak ada debug tersimpan.</strong><div class="small text-muted">Silakan lakukan submit untuk menangkap data.</div></div>';
                    } else {
                        renderDebugInto(inner, raw);
                    }
                    banner.style.display = 'block';
                } catch (e) {
                    console.warn(e);
                    alert('Gagal menampilkan debug');
                }
            });

            document.getElementById('close-submit-debug-banner').addEventListener('click', function() {
                document.getElementById('submit-debug-banner-container').style.display = 'none';
            });
        })();
    </script>
    <script>
        // Populate last-submit debug panel from localStorage and wire copy/clear buttons
        (function() {
            try {
                var raw = localStorage.getItem('last_pengabdian_submit_debug');
                if (!raw) return;
                var parsed = JSON.parse(raw);
                var container = document.getElementById('last-submit-debug');
                var content = document.getElementById('last-submit-debug-content');
                if (!container || !content) return;
                var html = '<div><strong>Waktu:</strong> ' + (parsed.time || '') + '</div>';
                if (parsed.summary && parsed.summary.files && parsed.summary.files.length) {
                    html += '<div style="margin-top:6px;"><em>Files:</em><ul style="margin:6px 0 0 18px;">';
                    parsed.summary.files.forEach(function(f) {
                        html += '<li>' + f.field + ': ' + f.name + ' (' + f.human + ')</li>';
                    });
                    html += '</ul></div>';
                } else {
                    html += '<div style="margin-top:6px;">No files captured in last submit.</div>';
                }
                content.innerHTML = html;
                container.style.display = 'block';

                document.getElementById('copy-debug-json').addEventListener('click', function() {
                    try {
                        navigator.clipboard.writeText(raw).then(function() {
                            alert('Debug JSON disalin ke clipboard.');
                        }, function(err) {
                            alert('Gagal menyalin: ' + err);
                        });
                    } catch (e) {
                        prompt('Salin manual JSON berikut:', raw);
                    }
                });

                document.getElementById('clear-debug-json').addEventListener('click', function() {
                    if (!confirm('Hapus debug info yang tersimpan di browser?')) return;
                    try {
                        localStorage.removeItem('last_pengabdian_submit_debug');
                    } catch (e) {}
                    container.style.display = 'none';
                });
            } catch (e) {
                console.warn('Tidak bisa membaca debug info dari localStorage', e);
            }
        })();
    </script>
    <script>
        // also show last file-selection entries (what inputs had files chosen)
        (function() {
            try {
                var selRaw = localStorage.getItem('last_pengabdian_file_selection');
                var target = document.getElementById('last-submit-debug-content');
                if (!selRaw || !target) return;
                var parsedSel = JSON.parse(selRaw);
                if (!parsedSel.picks || !parsedSel.picks.length) return;
                var html =
                    '<div style="margin-top:8px;"><strong>Terakhir memilih file:</strong><ul style="margin:6px 0 0 18px;">';
                parsedSel.picks.slice(-5).reverse().forEach(function(p) {
                    if (p.files && p.files.length) {
                        html += '<li>' + p.time + ' — ' + p.field + ': ' + p.files.map(function(f) {
                            return f.name + ' (' + (f.size ? (Math.round(f.size / 1024) + ' KB') :
                                '?') + ')';
                        }).join(', ') + '</li>';
                    } else {
                        html += '<li>' + p.time + ' — ' + p.field + ': (no files)</li>';
                    }
                });
                html += '</ul></div>';
                target.innerHTML = target.innerHTML + html;
            } catch (e) {
                console.warn('No selection debug', e);
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
                    // PERBAIKAN 4: Inisialisasi Flatpickr
                    $(".datepicker").flatpickr({
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        allowInput: true
                    });
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
                    $('#ketua_nik, #dosen_ids, #hki_anggota_dosen').on('change', () => this
                        .updateDosenRoles());
                    this.updateDosenRoles();
                },

                updateDosenRoles: function() {
                    const ketuaNik = $('#ketua_nik').val();
                    const anggotaPengabdianSelect = $('#dosen_ids');
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

                initMahasiswaBaruLogic: function() {
                    const showFormButton = $('#btn-tampilkan-form-mhs-baru');
                    const mahasiswaBaruSection = $('#mahasiswa-baru-section');
                    const container = $('#mahasiswa-baru-container');
                    const template = $('#mahasiswa-baru-template');
                    const addButton = $('#btn-tambah-mhs-baru');
                    const manageDeleteButtons = () => {
                        const rows = container.find('.mahasiswa-baru-item');
                        rows.find('.btn-hapus-mhs-baru').show();
                        rows.first().find('.btn-hapus-mhs-baru').hide();
                    };
                    const addRow = () => {
                        const idx = new Date().getTime();
                        container.append(template.html().replace(/__INDEX__/g, idx));
                        manageDeleteButtons();
                    };
                    showFormButton.on('click', function() {
                        mahasiswaBaruSection.show();
                        $(this).hide();
                        if (container.children('.mahasiswa-baru-item').length === 0) {
                            addRow();
                        }
                    });
                    addButton.on('click', addRow);
                    container.on('click', '.btn-hapus-mhs-baru', function() {
                        $(this).closest('.mahasiswa-baru-item').remove();
                        if (container.children('.mahasiswa-baru-item').length === 0) {
                            mahasiswaBaruSection.hide();
                            showFormButton.show();
                        } else {
                            manageDeleteButtons();
                        }
                    });
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
            PengabdianForm.init();

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
