<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\JenisDokumen;
use App\Models\LuaranWajib;
use App\Models\JenisLuaran;
use App\Models\Luaran;
use App\Models\DetailHki;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Rules\UniqueNimsInArray;
use App\Rules\AllNimsMustHaveCorrectDigits;
use App\Rules\NimsMustNotExist;
use App\Rules\AllMahasiswaRowsMustBeComplete;
use Carbon\Carbon;
use App\Rules\ValidTanggal;


class PengabdianController extends Controller
{
    /**
     * Menampilkan daftar semua kegiatan pengabdian.
     */
    public function index(Request $request)
    {
        // Include luaran relations and a computed hki_count to quickly know which pengabdian has HKI
        $pengabdian = Pengabdian::with(['ketua', 'dosen', 'mahasiswa', 'mitra', 'luaran.jenisLuaran', 'luaran.detailHki'])
            ->withSum('sumberDana', 'jumlah_dana')
            ->withCount(['luaran as hki_count' => function ($q) {
                $q->whereHas('detailHki');
            }])
            ->latest('tanggal_pengabdian')
            ->get();

        return view('admin.pengabdian.index', compact('pengabdian'));
    }

    /**
     * Menampilkan form untuk membuat pengabdian baru.
     */
    public function create()
    {
        $dosen = Dosen::orderBy('nama')->get();
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $luaranWajib = LuaranWajib::orderBy('id_luaran_wajib')->get();
        $jenisLuaran = JenisLuaran::orderBy('id_jenis_luaran')->get();

        return view('admin.pengabdian.create', compact('dosen', 'mahasiswa', 'luaranWajib', 'jenisLuaran'));
    }

    /**
     * Menyimpan data pengabdian baru ke database.
     */
    public function store(Request $request)
    {
        // Menghapus format Rupiah dari input jumlah dana sebelum validasi
        if ($request->has('sumber_dana')) {
            $sumberDanaInput = $request->input('sumber_dana');
            foreach ($sumberDanaInput as $key => $dana) {
                if (isset($dana['jumlah_dana'])) {
                    $sumberDanaInput[$key]['jumlah_dana'] = preg_replace('/[^0-9]/', '', $dana['jumlah_dana']);
                }
            }
            $request->merge(['sumber_dana' => $sumberDanaInput]);
        }

        $request->validate([
            // --- Aturan Validasi ---
            'judul_pengabdian'      => 'required|string|max:255',
            'nama_mitra'            => 'required|string|max:255',
            'lokasi_kegiatan'       => 'required|string|max:255',
            'tanggal_pengabdian'    => ['required', new ValidTanggal(2000, 'Tanggal Pengabdian')],
            'jumlah_luaran_direncanakan' => 'required|integer|min:0',
            'ketua_nik'             => 'required|exists:dosen,nik',
            'dosen_ids'             => 'nullable|array',
            'dosen_ids.*'           => 'nullable|exists:dosen,nik|different:ketua_nik',
            'mahasiswa_ids'         => 'nullable|array',
            'mahasiswa_ids.*'       => 'nullable|exists:mahasiswa,nim',
            'mahasiswa_baru'        => ['nullable', 'array', new UniqueNimsInArray, new AllNimsMustHaveCorrectDigits, new NimsMustNotExist, new AllMahasiswaRowsMustBeComplete], // <-- SAYA MENAMBAHKAN ATURAN INI
            'mahasiswa_baru.*.nim'  => 'nullable|numeric',
            'mahasiswa_baru.*.nama' => 'nullable|string|max:255',
            'mahasiswa_baru.*.prodi' => 'nullable|string|max:255',
            'sumber_dana'               => 'required|array|min:1',
            'sumber_dana.*.jenis'       => 'required|string',
            'sumber_dana.*.nama_sumber' => 'required|string',
            'sumber_dana.*.jumlah_dana' => 'required|numeric|min:0',
            'id_luaran_wajib' => 'required|exists:luaran_wajib,id_luaran_wajib',
            'luaran_jenis'    => 'nullable|array',
            'luaran_data.HKI.no_pendaftaran'    => ['nullable', 'string', 'max:255', 'unique:detail_hki,no_pendaftaran', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.tanggal_permohonan' => [
                'nullable',
                new ValidTanggal(2000, 'Tanggal Permohonan HKI'),
                'after_or_equal:tanggal_pengabdian',
                Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))
            ],
            'luaran_data.HKI.judul_ciptaan'     => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.pemegang_hak_cipta' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.jenis_ciptaan'     => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'dokumen.laporan_akhir'       => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'dokumen.surat_tugas'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.surat_permohonan'    => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.ucapan_terima_kasih' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.kerjasama'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.hki'                 => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],

        ], [
            // --- Pesan Kustom (Custom Messages) ---
            'required_with' => ':attribute wajib diisi jika kolom lainnya terisi.',
            'required_if'   => ':attribute wajib diisi jika luaran HKI dipilih.',
            'string'        => ':attribute harus berupa teks.',
            'date'          => ':attribute harus berupa format tanggal yang valid.',
            'numeric'       => ':attribute harus berupa angka.',
            'digits'        => ':attribute harus terdiri dari :digits digit angka.',
            'max'           => [
                'string' => ':attribute tidak boleh lebih dari :max karakter.',
                'file'   => 'Ukuran file :attribute tidak boleh lebih dari :max KB.',
            ],
            'exists'        => ':attribute yang dipilih tidak valid.',
            'distinct'      => 'Terdapat duplikasi :attribute pada baris yang ditambahkan.',
            'mahasiswa_baru.*.nim.unique' => 'NIM Mahasiswa Baru sudah digunakan.',
            'different'     => ':attribute tidak boleh sama dengan Ketua Pengabdian.',
            'array'         => ':attribute harus berupa array.',
            'min'           => ':attribute minimal harus memiliki :min data.',
            'mimes'         => 'Format file :attribute harus :values.',
            'file'          => ':attribute harus berupa file.',
            'luaran_data.HKI.tanggal_permohonan.after_or_equal' => 'Tanggal Permohonan tidak boleh lebih awal dari Tanggal Pengabdian.',

        ], [
            // --- Nama Atribut (Custom Attributes) ---
            'judul_pengabdian'      => 'Judul Pengabdian',
            'nama_mitra'            => 'Nama Mitra',
            'lokasi_kegiatan'       => 'Lokasi Kegiatan Mitra',
            'tanggal_pengabdian'    => 'Tanggal Pengabdian',
            'ketua_nik'             => 'Dosen (Ketua)',
            'dosen_ids.*'           => 'Dosen (Anggota)',
            'mahasiswa_ids.*'       => 'Mahasiswa',
            'mahasiswa_baru.*.nim'  => 'NIM Mahasiswa Baru',
            'mahasiswa_baru.*.nama' => 'Nama Mahasiswa Baru',
            'mahasiswa_baru.*.prodi' => 'Prodi Mahasiswa Baru',
            'sumber_dana'           => 'Sumber Dana',
            'sumber_dana.*.jenis'   => 'Jenis Sumber Dana',
            'sumber_dana.*.nama_sumber' => 'Nama Sumber Dana',
            'sumber_dana.*.jumlah_dana' => 'Jumlah Dana',
            'id_luaran_wajib'       => 'Luaran Wajib',
            'luaran_data.HKI.no_pendaftaran'    => 'Nomor Pendaftaran HKI',
            'luaran_data.HKI.tanggal_permohonan' => 'Tanggal Permohonan HKI',
            'luaran_data.HKI.judul_ciptaan'     => 'Judul Ciptaan HKI',
            'luaran_data.HKI.pemegang_hak_cipta' => 'Pemegang Hak Cipta HKI',
            'luaran_data.HKI.jenis_ciptaan'     => 'Jenis Ciptaan HKI',
            'dokumen.laporan_akhir'       => 'Dokumen Laporan Akhir',
            'dokumen.surat_tugas'         => 'Dokumen Surat Tugas',
            'dokumen.surat_permohonan'    => 'Dokumen Surat Permohonan',
            'dokumen.ucapan_terima_kasih' => 'Dokumen Ucapan Terima Kasih',
            'dokumen.kerjasama'           => 'Dokumen Kerja Sama',
            'dokumen.hki'                 => 'Dokumen HKI',
        ]);

        DB::beginTransaction();
        try {
            $pengabdian = Pengabdian::create([
                'judul_pengabdian' => $request->judul_pengabdian,
                'id_luaran_wajib' => $request->id_luaran_wajib,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $request->ketua_nik,
                'jumlah_luaran_direncanakan' => $request->jumlah_luaran_direncanakan,
            ]);

            $dosenData = [$request->ketua_nik => ['status_anggota' => 'ketua']];
            if (!empty($request->dosen_ids)) {
                foreach (array_filter($request->dosen_ids) as $nik) {
                    if ($nik !== $request->ketua_nik) {
                        $dosenData[$nik] = ['status_anggota' => 'anggota'];
                    }
                }
            }
            $pengabdian->dosen()->sync($dosenData);

            $nimMahasiswa = $request->mahasiswa_ids ? array_filter($request->mahasiswa_ids) : [];
            if ($request->filled('mahasiswa_baru')) {
                foreach ($request->mahasiswa_baru as $mhs) {
                    if (!empty($mhs['nim']) && !empty($mhs['nama'])) {
                        $mahasiswa = Mahasiswa::firstOrCreate(
                            ['nim' => $mhs['nim']],
                            ['nama' => $mhs['nama'], 'prodi' => $mhs['prodi']]
                        );
                        $nimMahasiswa[] = $mahasiswa->nim;
                    }
                }
            }
            if (!empty($nimMahasiswa)) {
                $pengabdian->mahasiswa()->attach(array_unique($nimMahasiswa));
            }

            if ($request->filled('nama_mitra')) {
                $pengabdian->mitra()->create(['nama_mitra' => $request->nama_mitra, 'lokasi_mitra' => $request->lokasi_kegiatan]);
            }

            if ($request->sumber_dana) {
                foreach ($request->sumber_dana as $dana) {
                    $pengabdian->sumberDana()->create([
                        'jenis' => $dana['jenis'],
                        'nama_sumber' => $dana['nama_sumber'],
                        'jumlah_dana' => $dana['jumlah_dana'],
                    ]);
                }
            }

            $this->syncLuaranDanDokumen($request, $pengabdian, false);

            DB::commit();
            return redirect()->route('admin.pengabdian.index')->with('success', 'Data pengabdian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan pada server. Gagal menyimpan data.');
        }
    }

    /**
     * Menampilkan detail satu pengabdian.
     */
    public function show($id)
    {
        $pengabdian = Pengabdian::with([
            'dosen',
            'mahasiswa',
            'mitra',
            'sumberDana',
            'luaranWajib',
            'luaran.jenisLuaran',
            'luaran.detailHki.dosen',
            'luaran.detailHki.dokumen',
            'dokumen.jenisDokumen'
        ])->findOrFail($id);

        return view('admin.pengabdian.show', compact('pengabdian'));
    }

    /**
     * Menampilkan form untuk mengedit pengabdian.
     */
    public function edit($id)
    {
        $pengabdian = Pengabdian::with([
            'dosen',
            'mahasiswa',
            'mitra',
            'sumberDana',
            'luaran.jenisLuaran',
            'luaran.detailHki.dosen',
            'dokumen'
        ])->findOrFail($id);

        $dosen = Dosen::orderBy('nama')->get();
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $luaranWajib = LuaranWajib::orderBy('id_luaran_wajib')->get();
        $jenisLuaran = JenisLuaran::orderBy('id_jenis_luaran')->get();

        return view('admin.pengabdian.edit', compact(
            'pengabdian',
            'dosen',
            'mahasiswa',
            'luaranWajib',
            'jenisLuaran'
        ));
    }

    /**
     * Memperbarui data pengabdian di database.
     */
    public function update(Request $request, $id)
    {
        $pengabdian = Pengabdian::with('luaran.jenisLuaran', 'luaran.detailHki')->findOrFail($id);

        // Optional debug: if URL contains ?debug_snapshot=1, log incoming files for diagnosis
        if ($request->query('debug_snapshot') === '1') {
            try {
                $filesLog = [];
                if ($request->hasFile('dokumen')) {
                    foreach ($request->file('dokumen') as $k => $f) {
                        if ($f) {
                            $filesLog[$k] = [
                                'originalName' => $f->getClientOriginalName(),
                                'size' => $f->getSize(),
                                'mime' => $f->getClientMimeType()
                            ];
                        } else {
                            $filesLog[$k] = null;
                        }
                    }
                }
                Log::debug('DEBUG_SNAPSHOT incoming files for pengabdian.update id=' . $id, ['files' => $filesLog, 'all' => $request->all()]);
                // Return JSON immediately so XHR client can display it without continuing update flow
                return response()->json([
                    'debug' => true,
                    'files' => $filesLog,
                    'fields' => $request->except(['_token', '_method'])
                ]);
            } catch (\Exception $e) {
                Log::debug('DEBUG_SNAPSHOT failed to log incoming files: ' . $e->getMessage());
                return response()->json(['debug' => true, 'error' => $e->getMessage()], 500);
            }
        }

        // Menghapus format Rupiah dari input jumlah dana sebelum validasi
        if ($request->has('sumber_dana')) {
            $sumberDanaInput = $request->input('sumber_dana');
            foreach ($sumberDanaInput as $key => $dana) {
                if (isset($dana['jumlah_dana'])) {
                    $sumberDanaInput[$key]['jumlah_dana'] = preg_replace('/[^0-9]/', '', $dana['jumlah_dana']);
                }
            }
            $request->merge(['sumber_dana' => $sumberDanaInput]);
        }

        $hki = $pengabdian->luaran->firstWhere('jenisLuaran.nama_jenis_luaran', 'HKI');
        $hkiId = $hki ? optional($hki->detailHki)->id_detail_hki : null;

        $request->validate([
            // --- Aturan Validasi ---
            'judul_pengabdian'      => 'required|string|max:255',
            'nama_mitra'            => 'required|string|max:255',
            'lokasi_kegiatan'       => 'required|string|max:255',
            'tanggal_pengabdian'    => ['required', new ValidTanggal(2000)],
            'ketua_nik'             => 'required|exists:dosen,nik',
            'dosen_ids'             => 'nullable|array',
            'dosen_ids.*'           => 'nullable|exists:dosen,nik|different:ketua_nik',
            'mahasiswa_ids'         => 'nullable|array',
            'mahasiswa_ids.*'       => 'nullable|exists:mahasiswa,nim',
            'mahasiswa_baru'        => 'nullable|array',
            'mahasiswa_baru.*.nim'  => 'required_with:mahasiswa_baru.*.nama|nullable|numeric|digits:9|distinct|unique:mahasiswa,nim',
            'mahasiswa_baru.*.nama' => 'required_with:mahasiswa_baru.*.nim|nullable|string|max:255',
            'mahasiswa_baru.*.prodi' => 'required_with:mahasiswa_baru.*.nim|nullable|string|max:255',
            'sumber_dana'               => 'required|array|min:1',
            'sumber_dana.*.jenis'       => 'required|string',
            'sumber_dana.*.nama_sumber' => 'required|string',
            'sumber_dana.*.jumlah_dana' => 'required|numeric|min:0',
            'id_luaran_wajib' => 'required|exists:luaran_wajib,id_luaran_wajib',
            'luaran_jenis'    => 'nullable|array',
            'luaran_data.HKI.no_pendaftaran'    => ['nullable', 'string', 'max:255', Rule::unique('detail_hki', 'no_pendaftaran')->ignore($hkiId, 'id_detail_hki'), Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.tanggal_permohonan' => [
                'nullable',
                new ValidTanggal(2000),
                Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))
            ],
            'luaran_data.HKI.judul_ciptaan'     => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.pemegang_hak_cipta' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'luaran_data.HKI.jenis_ciptaan'     => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'dokumen.laporan_akhir'       => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'dokumen.surat_tugas'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.surat_permohonan'    => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.ucapan_terima_kasih' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'dokumen.kerjasama'           => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            // 'dokumen.hki'                 => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120', Rule::requiredIf(fn() => in_array('HKI', $request->luaran_jenis ?? []))],
            'dokumen.hki' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
                // Gunakan closure untuk logika kustom yang lebih sederhana
                Rule::requiredIf(function () use ($request, $hki) {
                    // Dokumen HKI hanya mungkin wajib jika luaran HKI dipilih
                    if (!in_array('HKI', $request->luaran_jenis ?? [])) {
                        return false; // Tidak wajib jika HKI tidak dipilih
                    }

                    // Cek apakah sudah ada dokumen HKI yang terlampir pada data lama
                    $dokumenHkiLama = optional(optional($hki)->detailHki)->dokumen;

                    // Wajib HANYA jika dokumen lama tidak ada (null)
                    return is_null($dokumenHkiLama);
                })
            ],

        ], [
            // --- Pesan Kustom (Custom Messages) ---
            'required'      => ':attribute wajib diisi.',
            'required_with' => ':attribute wajib diisi jika kolom lainnya terisi.',
            'required_if'   => ':attribute wajib diisi jika luaran HKI dipilih.',
            'string'        => ':attribute harus berupa teks.',
            'date'          => ':attribute harus berupa format tanggal yang valid.',
            'numeric'       => ':attribute harus berupa angka.',
            'digits'        => ':attribute harus terdiri dari :digits digit angka.',
            'max'           => ':attribute tidak boleh lebih dari :max karakter.',
            'file.max'      => 'Ukuran file :attribute tidak boleh lebih dari :max KB.',
            'exists'        => ':attribute yang dipilih tidak valid.',
            'unique'        => ':attribute ini sudah terdaftar.',
            'distinct'      => 'Terdapat duplikasi :attribute pada baris yang ditambahkan.',
            'different'     => ':attribute tidak boleh sama dengan Ketua Pengabdian.',
            'array'         => ':attribute harus berupa array.',
            'min'           => ':attribute minimal harus memiliki :min data.',
            'mimes'         => 'Format file :attribute harus :values.',
            'file'          => ':attribute harus berupa file.',

        ], [
            // --- Nama Atribut (Custom Attributes) ---
            'judul_pengabdian'      => 'Judul Pengabdian',
            'nama_mitra'            => 'Nama Mitra',
            'lokasi_kegiatan'       => 'Lokasi Kegiatan Mitra',
            'tanggal_pengabdian'    => 'Tanggal Pengabdian',
            'ketua_nik'             => 'Dosen (Ketua)',
            'dosen_ids.*'           => 'Dosen (Anggota)',
            'mahasiswa_ids.*'       => 'Mahasiswa',
            'mahasiswa_baru.*.nim'  => 'NIM Mahasiswa Baru',
            'mahasiswa_baru.*.nama' => 'Nama Mahasiswa Baru',
            'mahasiswa_baru.*.prodi' => 'Prodi Mahasiswa Baru',
            'sumber_dana'           => 'Sumber Dana',
            'sumber_dana.*.jenis'   => 'Jenis Sumber Dana',
            'sumber_dana.*.nama_sumber' => 'Nama Sumber Dana',
            'sumber_dana.*.jumlah_dana' => 'Jumlah Dana',
            'id_luaran_wajib'       => 'Luaran Wajib',
            'luaran_data.HKI.no_pendaftaran'    => 'Nomor Pendaftaran HKI',
            'luaran_data.HKI.tanggal_permohonan' => 'Tanggal Permohonan HKI',
            'luaran_data.HKI.judul_ciptaan'     => 'Judul Ciptaan HKI',
            'luaran_data.HKI.pemegang_hak_cipta' => 'Pemegang Hak Cipta HKI',
            'luaran_data.HKI.jenis_ciptaan'     => 'Jenis Ciptaan HKI',
            'dokumen.laporan_akhir'       => 'Dokumen Laporan Akhir',
            'dokumen.surat_tugas'         => 'Dokumen Surat Tugas',
            'dokumen.surat_permohonan'    => 'Dokumen Surat Permohonan',
            'dokumen.ucapan_terima_kasih' => 'Dokumen Ucapan Terima Kasih',
            'dokumen.kerjasama'           => 'Dokumen Kerja Sama',
            'dokumen.hki'                 => 'Dokumen HKI',
        ]);

        DB::beginTransaction();
        try {
            $pengabdian->update([
                'judul_pengabdian' => $request->judul_pengabdian,
                'id_luaran_wajib' => $request->id_luaran_wajib,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $request->ketua_nik,
                'jumlah_luaran_direncanakan' => $request->jumlah_luaran_direncanakan, 
            ]);

            $dosenData = [$request->ketua_nik => ['status_anggota' => 'ketua']];
            if (!empty($request->dosen_ids)) {
                foreach (array_filter($request->dosen_ids) as $nik) {
                    if ($nik !== $request->ketua_nik) {
                        $dosenData[$nik] = ['status_anggota' => 'anggota'];
                    }
                }
            }
            $pengabdian->dosen()->sync($dosenData);

            $nimMahasiswa = $request->mahasiswa_ids ? array_filter($request->mahasiswa_ids) : [];
            if ($request->filled('mahasiswa_baru')) {
                foreach ($request->mahasiswa_baru as $mhs) {
                    if (!empty($mhs['nim']) && !empty($mhs['nama'])) {
                        $mahasiswa = Mahasiswa::firstOrCreate(
                            ['nim' => $mhs['nim']],
                            ['nama' => $mhs['nama'], 'prodi' => $mhs['prodi'] ?? null]
                        );
                        $nimMahasiswa[] = $mahasiswa->nim;
                    }
                }
            }
            $pengabdian->mahasiswa()->sync(array_unique($nimMahasiswa));

            if ($request->filled('nama_mitra')) {
                $pengabdian->mitra()->updateOrCreate(
                    ['id_pengabdian' => $pengabdian->id_pengabdian], // Kondisi pencarian
                    [ // Data untuk diupdate atau dibuat
                        'nama_mitra' => $request->nama_mitra,
                        'lokasi_mitra' => $request->lokasi_kegiatan
                    ]
                );
            } else {
                $pengabdian->mitra()->delete(); // Hapus jika dikosongkan
            }

            // Sumber Dana: Update/buat yang ada di request, hapus yang tidak ada
            $danaIds = [];
            if ($request->sumber_dana) {
                foreach ($request->sumber_dana as $dana) {
                    $sumberDana = $pengabdian->sumberDana()->updateOrCreate(
                        ['id_sumber_dana' => $dana['id_sumber_dana'] ?? null], // Cari berdasarkan ID, atau buat baru jika ID null
                        [
                            'jenis' => $dana['jenis'],
                            'nama_sumber' => $dana['nama_sumber'],
                            'jumlah_dana' => $dana['jumlah_dana'],
                        ]
                    );
                    $danaIds[] = $sumberDana->id_sumber_dana;
                }
            }
            // Hapus sumber dana yang tidak ada lagi di request
            $pengabdian->sumberDana()->whereNotIn('id_sumber_dana', $danaIds)->delete();

            $this->syncLuaranDanDokumen($request, $pengabdian, true);

            DB::commit();
            return redirect()->route('admin.pengabdian.index')->with('success', 'Data pengabdian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Menghapus data pengabdian dari database.
     */
    public function destroy($id)
    {
        $pengabdian = Pengabdian::findOrFail($id);
        DB::beginTransaction();
        try {
            foreach ($pengabdian->dokumen as $doc) {
                Storage::disk('public')->delete($doc->path_file);
            }
            $pengabdian->delete();

            DB::commit();
            return redirect()->route('admin.pengabdian.index')->with('success', 'Data pengabdian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    /**
     * Method private untuk sinkronisasi Luaran dan Dokumen.
     */
    private function syncLuaranDanDokumen(Request $request, Pengabdian $pengabdian, $isUpdate = false)
    {
        // ==========================================================
        // BAGIAN LUARAN DENGAN LOGIKA BARU (TIDAK MENGHAPUS SEMUA)
        // ==========================================================
        $jenisLuaranTerpilih = $request->input('luaran_jenis', []);
        $luaranYangAda = $pengabdian->luaran()->with('jenisLuaran')->get();

        // 1. Hapus Luaran yang tidak lagi dipilih di form
        foreach ($luaranYangAda as $luaran) {
            if (!in_array($luaran->jenisLuaran->nama_jenis_luaran, $jenisLuaranTerpilih)) {
                $luaran->delete(); // Ini akan cascade ke detail_hki dan dokumennya jika ada
            }
        }

        // 2. Buat atau Update Luaran yang ada di form
        if (!empty($jenisLuaranTerpilih)) {
            foreach ($jenisLuaranTerpilih as $jenisLuaranValue) {
                $jenisLuaranModel = JenisLuaran::where('nama_jenis_luaran', $jenisLuaranValue)->first();
                if (!$jenisLuaranModel) continue;

                // Cek apakah luaran ini sudah ada, jika belum maka buat baru
                $luaranRecord = $pengabdian->luaran()->firstOrCreate(['id_jenis_luaran' => $jenisLuaranModel->id_jenis_luaran]);

                // Proses detail HKI jika jenisnya adalah HKI
                if ($jenisLuaranValue === 'HKI' && $request->has('luaran_data.HKI')) {
                    $hkiData = $request->luaran_data['HKI'];
                    // Gunakan updateOrCreate untuk detail HKI
                    $detailHki = $luaranRecord->detailHki()->updateOrCreate(
                        ['id_luaran' => $luaranRecord->id_luaran], // Kondisi pencarian
                        [ // Data untuk diupdate atau dibuat
                            'no_pendaftaran' => $hkiData['no_pendaftaran'],
                            'tgl_permohonan' => $hkiData['tanggal_permohonan'],
                            'judul_ciptaan' => $hkiData['judul_ciptaan'],
                            'pemegang_hak_cipta' => $hkiData['pemegang_hak_cipta'],
                            'jenis_ciptaan' => $hkiData['jenis_ciptaan'],
                        ]
                    );

                    if (!empty($hkiData['anggota_dosen'])) {
                        $detailHki->dosen()->sync($hkiData['anggota_dosen']);
                    }
                }
            }
        }

        // ==========================================================
        // BAGIAN DOKUMEN (LOGIKA INI SUDAH BENAR DAN TIDAK DIUBAH)
        // ==========================================================
        if ($request->hasFile('dokumen')) {
            $jenisDokumenMapping = [
                'surat_tugas' => 1,
                'hki' => 2,
                'surat_permohonan' => 3,
                'ucapan_terima_kasih' => 4,
                'kerjasama' => 5,
                'laporan_akhir' => 6
            ];

            foreach ($request->file('dokumen') as $key => $file) {
                if (array_key_exists($key, $jenisDokumenMapping)) {
                    $idJenisDokumen = $jenisDokumenMapping[$key];

                    // Hapus file lama HANYA JIKA ada file baru yang diunggah
                    if ($isUpdate) {
                        $dokumenLama = $pengabdian->dokumen()->where('id_jenis_dokumen', $idJenisDokumen)->first();

                        if ($dokumenLama) {
                            Storage::disk('public')->delete($dokumenLama->path_file);
                            $dokumenLama->delete();
                        }
                    }

                    // Simpan file baru
                    $path = $file->store('dokumen_pendukung/' . $key, 'public');
                    $dataDokumen = [
                        'id_jenis_dokumen' => $idJenisDokumen,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                    ];

                    if ($key === 'hki') {
                        $pengabdian->load('luaran.detailHki');
                        $luaranHki = $pengabdian->luaran->firstWhere('jenisLuaran.nama_jenis_luaran', 'HKI');
                        if ($luaranHki && $luaranHki->detailHki) {
                            $dataDokumen['id_detail_hki'] = $luaranHki->detailHki->id_detail_hki;
                        }
                    }
                    $pengabdian->dokumen()->create($dataDokumen);
                }
            }
        }
    }
}
