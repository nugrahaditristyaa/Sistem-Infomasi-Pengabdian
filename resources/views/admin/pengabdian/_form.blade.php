{{-- Shared Form Partial for Create and Edit Pengabdian --}}
{{-- 
    Required variables:
    - $mode: 'create' or 'edit'
    - $pengabdian: null for create, model instance for edit
    - $dosen: collection of dosen
    - $mahasiswa: collection of mahasiswa
    - $jenisLuaran: collection of jenis luaran
--}}

@php
    $isEdit = $mode === 'edit';
    $pengabdian = $pengabdian ?? null;
@endphp

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
                value="{{ old('judul_pengabdian', $isEdit ? $pengabdian->judul_pengabdian : '') }}" required
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
                        id="nama_mitra" name="nama_mitra" 
                        value="{{ old('nama_mitra', $isEdit ? optional($pengabdian->mitra->first())->nama_mitra : '') }}" required
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
                        id="lokasi_kegiatan" name="lokasi_kegiatan" 
                        value="{{ old('lokasi_kegiatan', $isEdit ? optional($pengabdian->mitra->first())->lokasi_mitra : '') }}" required
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
                id="tanggal_pengabdian" name="tanggal_pengabdian" 
                value="{{ old('tanggal_pengabdian', $isEdit && $pengabdian ? \Carbon\Carbon::parse($pengabdian->tanggal_pengabdian)->format('Y-m-d') : '') }}" required>
            @error('tanggal_pengabdian')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Jenis Luaran yang Direncanakan <span class="text-danger">*</span></label>
            <small class="form-text text-muted mb-2">
                Pilih jenis-jenis luaran yang akan dicapai sesuai proposal.
            </small>
            @php
                $selectedDirencanakan = old('jumlah_luaran_direncanakan', 
                    $isEdit && $pengabdian 
                        ? (is_string($pengabdian->jumlah_luaran_direncanakan) 
                            ? json_decode($pengabdian->jumlah_luaran_direncanakan, true) ?? [] 
                            : (is_array($pengabdian->jumlah_luaran_direncanakan) ? $pengabdian->jumlah_luaran_direncanakan : []))
                        : []
                );
            @endphp
            <div class="checkbox-group @error('jumlah_luaran_direncanakan') is-invalid @enderror">
                @foreach ($jenisLuaran as $jl)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"
                            id="direncanakan_{{ $jl->id_jenis_luaran }}" name="jumlah_luaran_direncanakan[]"
                            value="{{ $jl->nama_jenis_luaran }}"
                            {{ in_array($jl->nama_jenis_luaran, $selectedDirencanakan) ? 'checked' : '' }}>
                        <label class="custom-control-label"
                            for="direncanakan_{{ $jl->id_jenis_luaran }}">{{ $jl->nama_jenis_luaran }}</label>
                    </div>
                @endforeach
            </div>
            @error('jumlah_luaran_direncanakan')
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
            <select id="ketua_nik" name="ketua_nik" class="form-control @error('ketua_nik') is-invalid @enderror" required>
                <option value="" disabled selected>— Pilih Ketua — </option>
                @foreach ($dosen as $d)
                    <option value="{{ $d->nik }}" 
                        {{ old('ketua_nik', $isEdit && $pengabdian ? $pengabdian->ketua_pengabdian : '') == $d->nik ? 'selected' : '' }}>
                        {{ $d->nama }} - {{ $d->nidn }}
                    </option>
                @endforeach
            </select>
            @error('ketua_nik')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Tombol Tambah Dosen Eksternal untuk Ketua --}}
            <button type="button" class="btn btn-sm btn-info mt-2" id="btn-tampilkan-form-dosen-ketua">
                <i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal
            </button>
        </div>

        {{-- Dosen Eksternal Ketua Section --}}
        <div id="dosen-baru-ketua-section" style="display: none;">
            <hr class="mt-4 mb-3">
            <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-plus-circle fa-fw mr-1"></i>Tambah
                Dosen Eksternal (Ketua) (Opsional)</h6>
            @error('dosen_baru_ketua')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div id="dosen-baru-ketua-container">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="form-group mb-md-0">
                            <label>NIK</label>
                            <input type="text" name="dosen_baru_ketua[nik]"
                                class="form-control @error('dosen_baru_ketua.nik') is-invalid @enderror"
                                placeholder="NIK" value="{{ old('dosen_baru_ketua.nik') ?? '' }}">
                            @error('dosen_baru_ketua.nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-md-0">
                            <label>Nama</label>
                            <input type="text" name="dosen_baru_ketua[nama]"
                                class="form-control @error('dosen_baru_ketua.nama') is-invalid @enderror"
                                placeholder="Nama dosen" value="{{ old('dosen_baru_ketua.nama') ?? '' }}">
                            @error('dosen_baru_ketua.nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-md-0">
                            <label>Prodi</label>
                            <input type="text" name="dosen_baru_ketua[prodi]"
                                class="form-control @error('dosen_baru_ketua.prodi') is-invalid @enderror"
                                placeholder="Program Studi" value="{{ old('dosen_baru_ketua.prodi') ?? '' }}">
                            @error('dosen_baru_ketua.prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-md-0">
                            <label>Email</label>
                            <input type="email" name="dosen_baru_ketua[email]"
                                class="form-control @error('dosen_baru_ketua.email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('dosen_baru_ketua.email') ?? '' }}">
                            @error('dosen_baru_ketua.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-md-0">
                            <label>Bidang Keahlian</label>
                            <input type="text" name="dosen_baru_ketua[bidang_keahlian]"
                                class="form-control @error('dosen_baru_ketua.bidang_keahlian') is-invalid @enderror"
                                placeholder="Bidang Keahlian"
                                value="{{ old('dosen_baru_ketua.bidang_keahlian') ?? '' }}">
                            @error('dosen_baru_ketua.bidang_keahlian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="btn-batalkan-dosen-ketua">
                    <i class="fas fa-times fa-sm mr-1"></i> Batalkan
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="anggota">Pilih Dosen (Anggota)</label>
                    @php
                        $anggota_ids = $isEdit && $pengabdian 
                            ? $pengabdian->dosen->where('pivot.status_anggota', 'anggota')->pluck('nik')->toArray()
                            : [];
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

                {{-- Tombol Tambah Dosen Eksternal untuk Anggota --}}
                <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-dosen-anggota">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen Eksternal
                </button>
            </div>
        </div>

        {{-- Dosen Eksternal Anggota Section --}}
        <div id="dosen-baru-anggota-section" style="display: none;">
            <hr class="mt-4 mb-3">
            <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-plus-circle fa-fw mr-1"></i>Tambah
                Dosen Eksternal (Anggota) (Opsional)</h6>
            @error('dosen_baru_anggota')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div id="dosen-baru-anggota-container">
                @forelse (old('dosen_baru_anggota', [['nik' => '', 'nama' => '', 'prodi' => '', 'email' => '', 'bidang_keahlian' => '']]) as $index => $dosen_ang)
                    <div class="row dosen-baru-anggota-item mb-3">
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>NIK</label>
                                <input type="text" name="dosen_baru_anggota[{{ $index }}][nik]"
                                    class="form-control @error('dosen_baru_anggota.' . $index . '.nik') is-invalid @enderror"
                                    placeholder="NIK" value="{{ $dosen_ang['nik'] ?? '' }}">
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
                                    placeholder="Nama dosen" value="{{ $dosen_ang['nama'] ?? '' }}">
                                @error('dosen_baru_anggota.' . $index . '.nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>Prodi</label>
                                <input type="text" name="dosen_baru_anggota[{{ $index }}][prodi]"
                                    class="form-control @error('dosen_baru_anggota.' . $index . '.prodi') is-invalid @enderror"
                                    placeholder="Program Studi" value="{{ $dosen_ang['prodi'] ?? '' }}">
                                @error('dosen_baru_anggota.' . $index . '.prodi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>Email</label>
                                <input type="email" name="dosen_baru_anggota[{{ $index }}][email]"
                                    class="form-control @error('dosen_baru_anggota.' . $index . '.email') is-invalid @enderror"
                                    placeholder="Email" value="{{ $dosen_ang['email'] ?? '' }}">
                                @error('dosen_baru_anggota.' . $index . '.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-md-0">
                                <label>Bidang Keahlian</label>
                                <input type="text"
                                    name="dosen_baru_anggota[{{ $index }}][bidang_keahlian]"
                                    class="form-control @error('dosen_baru_anggota.' . $index . '.bidang_keahlian') is-invalid @enderror"
                                    placeholder="Bidang Keahlian"
                                    value="{{ $dosen_ang['bidang_keahlian'] ?? '' }}">
                                @error('dosen_baru_anggota.' . $index . '.bidang_keahlian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-dosen-baru-anggota">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
            <button type="button" class="btn btn-sm btn-info mt-2" id="btn-tambah-dosen-anggota"><i
                    class="fas fa-plus fa-sm mr-1"></i> Tambah Dosen</button>
        </div>
    </div>
</div>

{{-- Mahasiswa Terlibat --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate fa-fw mr-2"></i>Mahasiswa yang Terlibat</h6>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="mahasiswa_ids">Pilih Mahasiswa</label>
            @php
                $mahasiswa_ids = $isEdit && $pengabdian 
                    ? $pengabdian->mahasiswa->pluck('nim')->toArray()
                    : [];
            @endphp
            <select id="mahasiswa_ids" name="mahasiswa_ids[]"
                class="form-control @error('mahasiswa_ids.*') is-invalid @enderror" multiple
                data-placeholder="Pilih satu atau lebih...">
                @foreach ($mahasiswa as $m)
                    <option value="{{ $m->nim }}"
                        {{ in_array($m->nim, old('mahasiswa_ids', $mahasiswa_ids)) ? 'selected' : '' }}>{{ $m->nama }} - {{ $m->nim }}</option>
                @endforeach
            </select>
            @error('mahasiswa_ids.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="button" class="btn btn-sm btn-info" id="btn-tampilkan-form-mhs-baru">
            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Mahasiswa Baru
        </button>

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
                                <input type="text" inputmode="numeric" pattern="\d*" maxlength="8"
                                    name="mahasiswa_baru[{{ $index }}][nim]"
                                    class="form-control nim-input @error('mahasiswa_baru.' . $index . '.nim') is-invalid @enderror"
                                    placeholder="NIM (hanya angka, 8 digit)" value="{{ $mhs['nim'] ?? '' }}"
                                    oninput="this.value = this.value.replace(/\D/g, '')">
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
                        <div class="col-md-3">
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
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-money-bill-wave fa-fw mr-2"></i>Sumber Dana</h6>
    </div>
    <div class="card-body">
        @error('sumber_dana')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <div id="sumber-dana-container">
            @php
                $allSumberDana = old('sumber_dana', 
                    $isEdit && $pengabdian 
                        ? $pengabdian->sumberDana->toArray() 
                        : []
                );
            @endphp
            @forelse ($allSumberDana as $index => $dana)
                <div class="row sumber-dana-item mb-3">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Jenis <span class="text-danger">*</span></label>
                            <select name="sumber_dana[{{ $index }}][jenis]"
                                class="form-control jenis-sumber-dana @error('sumber_dana.' . $index . '.jenis') is-invalid @enderror" required>
                                <option value="" disabled {{ !isset($dana['jenis']) ? 'selected' : '' }}>— Pilih Jenis —</option>
                                <option value="Internal" {{ ($dana['jenis'] ?? '') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                <option value="Eksternal" {{ ($dana['jenis'] ?? '') == 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
                            </select>
                            @error('sumber_dana.' . $index . '.jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Nama Sumber <span class="text-danger">*</span></label>
                            <select name="sumber_dana[{{ $index }}][nama_sumber]"
                                class="form-control nama-sumber-dana @error('sumber_dana.' . $index . '.nama_sumber') is-invalid @enderror" 
                                required
                                data-selected-value="{{ $dana['nama_sumber'] ?? '' }}">
                                {{-- Options are populated by JS --}}
                            </select>
                            @error('sumber_dana.' . $index . '.nama_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Jumlah Dana <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="text" name="sumber_dana[{{ $index }}][jumlah_dana]"
                                    class="form-control jumlah-dana @error('sumber_dana.' . $index . '.jumlah_dana') is-invalid @enderror"
                                    placeholder="0" value="{{ $dana['jumlah_dana'] ?? '' }}" required>
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
            <label>Hasil Luaran Kegiatan</label>
            @php
                $selectedLuaran = old('luaran_jenis', 
                    $isEdit && $pengabdian 
                        ? $pengabdian->luaran->pluck('jenisLuaran.nama_jenis_luaran')->toArray()
                        : []
                );
            @endphp
            <div class="checkbox-group @error('luaran_jenis') is-invalid @enderror">
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
            @error('luaran_jenis')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Detail HKI Container --}}
        @php
            $detailHki = $isEdit && $pengabdian 
                ? $pengabdian->luaran->firstWhere('jenisLuaran.nama_jenis_luaran', 'HKI')->detailHki ?? null
                : null;
        @endphp
        <div id="detail-hki" class="luaran-detail"
            style="{{ in_array('HKI', $selectedLuaran) ? '' : 'display:none;' }}">
            <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-copyright fa-fw mr-1"></i>Detail HKI</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hki_no_pendaftaran">Nomor Pendaftaran <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('luaran_data.HKI.no_pendaftaran') is-invalid @enderror"
                            id="hki_no_pendaftaran" name="luaran_data[HKI][no_pendaftaran]"
                            value="{{ old('luaran_data.HKI.no_pendaftaran', optional($detailHki)->no_pendaftaran) }}"
                            placeholder="Masukkan nomor pendaftaran">
                        @error('luaran_data.HKI.no_pendaftaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hki_tanggal_permohonan">Tanggal Permohonan <span class="text-danger">*</span></label>
                        <input type="date"
                            class="form-control @error('luaran_data.HKI.tanggal_permohonan') is-invalid @enderror"
                            id="hki_tanggal_permohonan" name="luaran_data[HKI][tanggal_permohonan]"
                            value="{{ old('luaran_data.HKI.tanggal_permohonan', optional($detailHki)->tgl_permohonan ? \Carbon\Carbon::parse($detailHki->tgl_permohonan)->format('Y-m-d') : '') }}">
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
                    value="{{ old('luaran_data.HKI.judul_ciptaan', optional($detailHki)->judul_ciptaan) }}" 
                    placeholder="Masukkan judul ciptaan">
                @error('luaran_data.HKI.judul_ciptaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hki_pemegang_hak_cipta">Pemegang Hak Cipta <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('luaran_data.HKI.pemegang_hak_cipta') is-invalid @enderror"
                            name="luaran_data[HKI][pemegang_hak_cipta]" id="hki_pemegang_hak_cipta"
                            value="{{ old('luaran_data.HKI.pemegang_hak_cipta', optional($detailHki)->pemegang_hak_cipta) }}"
                            placeholder="Masukkan pemegang hak cipta">
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
                            value="{{ old('luaran_data.HKI.jenis_ciptaan', optional($detailHki)->jenis_ciptaan) }}"
                            placeholder="Masukkan jenis ciptaan">
                        @error('luaran_data.HKI.jenis_ciptaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="hki_anggota_dosen">Anggota Pencipta (Dosen)</label>
                <small class="form-text text-info mb-2">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Field ini dapat memilih ketua dan anggota yang sudah dipilih sebelumnya
                    (tidak ada pembatasan duplikasi)
                </small>
                @php
                    $anggotaHkiNiks = optional($detailHki)->dosen 
                        ? $detailHki->dosen->pluck('nik')->toArray()
                        : [];
                @endphp
                <select id="hki_anggota_dosen" name="luaran_data[HKI][anggota_dosen][]"
                    class="form-control @error('luaran_data.HKI.anggota_dosen.*') is-invalid @enderror" multiple
                    data-placeholder="Pilih satu atau lebih dosen...">
                    @foreach ($dosen as $d)
                        <option value="{{ $d->nik }}"
                            {{ in_array($d->nik, old('luaran_data.HKI.anggota_dosen', $anggotaHkiNiks)) ? 'selected' : '' }}>
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
                @php 
                    $dokumenHki = optional($detailHki)->dokumen; 
                @endphp
                @if ($isEdit && $dokumenHki)
                    <div class="current-file mb-2">File saat ini: <a
                            href="{{ Storage::url($dokumenHki->path_file) }}"
                            target="_blank">{{ $dokumenHki->nama_file }}</a></div>
                @endif
                <div class="custom-file">
                    <input type="file" class="custom-file-input @error('dokumen.hki') is-invalid @enderror"
                        name="dokumen[hki]" id="dokumen_hki">
                    <label class="custom-file-label" for="dokumen_hki">{{ $isEdit ? 'Pilih file baru untuk mengganti...' : 'Pilih file...' }}</label>
                </div>
                @error('dokumen.hki')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maks: 5MB.</small>
            </div>
        </div>
    </div>
</div>

{{-- Upload Dokumen Pendukung --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-upload fa-fw mr-2"></i>Upload Dokumen Pendukung</h6>
    </div>
    <div class="card-body">
        @php
            $dokumenMapping = [
                'laporan_akhir' => ['label' => 'Laporan Akhir Lengkap', 'max' => '10MB'],
                'surat_tugas' => ['label' => 'Surat Tugas Dosen', 'max' => '5MB'],
                'surat_permohonan' => ['label' => 'Surat Permohonan', 'max' => '5MB'],
                'ucapan_terima_kasih' => ['label' => 'Surat Ucapan Terima Kasih', 'max' => '5MB'],
                'kerjasama' => ['label' => 'MoU/MoA/Dokumen Kerja Sama', 'max' => '5MB'],
            ];
        @endphp
        @foreach ($dokumenMapping as $key => $docInfo)
            <div class="form-group">
                <label for="dokumen_{{ $key }}">{{ $docInfo['label'] }}</label>
                @if ($isEdit && $pengabdian)
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
                @endif
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="dokumen_{{ $key }}"
                        name="dokumen[{{ $key }}]" accept=".pdf,.doc,.docx">
                    <label class="custom-file-label" for="dokumen_{{ $key }}">{{ $isEdit ? 'Pilih file baru...' : 'Pilih file' }}</label>
                </div>
                <small class="form-text text-muted">Format file: PDF, DOC, atau DOCX. Maksimal {{ $docInfo['max'] }}.</small>
            </div>
        @endforeach
    </div>
</div>
