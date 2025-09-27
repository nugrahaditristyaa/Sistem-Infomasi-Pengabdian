<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\JenisDokumen;
use App\Models\KategoriSpmi;
use App\Models\JenisLuaran;
use App\Models\Luaran;      // Tambahkan Model Luaran
use App\Models\DetailHki;   // Tambahkan Model DetailHki
use App\Models\Dokumen;     // Tambahkan Model Dokumen
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengabdianController extends Controller
{
    /**
     * Display a listing of the service activities.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pengabdian = Pengabdian::with(['dosen', 'mahasiswa', 'mitra', 'sumberDana', 'dokumen', 'luaran'])
            ->orderBy('tanggal_pengabdian', 'desc')
            ->paginate(10);

        return view('admin.pengabdian.index', compact('pengabdian'));
    }

    /**
     * Show the form for creating a new service activity.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $dosen = Dosen::orderBy('nama')->get();
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $jenisDokumen = JenisDokumen::orderBy('nama_jenis_dokumen')->get();
        $kategoriSpmi = KategoriSpmi::orderBy('kode_spmi')->get();
        $jenisLuaran = JenisLuaran::orderBy('nama_jenis_luaran')->get();

        return view('admin.pengabdian.create', compact('dosen', 'mahasiswa', 'jenisDokumen', 'kategoriSpmi', 'jenisLuaran'));
    }

    /**
     * Store a newly created service activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // --- VALIDASI DATA ---
        $request->validate([
            'judul_pengabdian' => 'required|string|max:255',
            'tanggal_pengabdian' => 'required|date',
            'ketua_nik' => 'required|exists:dosen,nik',
            'dosen_ids' => 'nullable|array',
            'dosen_ids.*' => 'nullable|exists:dosen,nik',
            'mahasiswa_ids' => 'nullable|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,nim',
            // mahasiswa baru (opsional)
            'mahasiswa_baru' => 'nullable|array',
            'mahasiswa_baru.*.nim' => 'nullable|numeric|unique:mahasiswa,nim',
            'mahasiswa_baru.*.nama' => 'nullable|string|max:255',
            'mahasiswa_baru.*.prodi' => 'nullable|string|max:255',
            'nama_mitra' => 'nullable|string|max:255',
            'lokasi_kegiatan' => 'nullable|string|max:255',
            'sumber_dana' => 'required|array',
            'sumber_dana.*.jenis' => 'required|in:Internal,Eksternal',
            'sumber_dana.*.nama_sumber' => 'required|string|max:255',
            'sumber_dana.*.jumlah_dana' => 'required|string',

            // --- VALIDASI LUARAN (TAMBAHAN) ---
            'luaran_jenis' => 'nullable|array',
            'luaran_jenis.*' => 'string|in:Laporan Akhir,HKI,Jurnal Internasional,Jurnal Nasional,Buku,Lainnya',
            
            // Validasi Detail HKI (hanya jika HKI dipilih)
            'luaran_data.HKI.no_pendaftaran' => 'required_if:luaran_jenis.*,==,HKI|string|max:255',
            'luaran_data.HKI.tanggal_permohonan' => 'required_if:luaran_jenis.*,==,HKI|date',
            'luaran_data.HKI.judul_ciptaan' => 'required_if:luaran_jenis.*,==,HKI|string|max:255',
            'luaran_data.HKI.pemegang_hak_cipta' => 'required_if:luaran_jenis.*,==,HKI|string|max:255',
            'luaran_data.HKI.jenis_ciptaan' => 'required_if:luaran_jenis.*,==,HKI|string|max:255',
            'luaran_data.HKI.anggota_dosen' => 'nullable|array',
            'luaran_data.HKI.anggota_dosen.*' => 'exists:dosen,nik',
            'dokumen.hki' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Maks 5MB
        ], [
            'mahasiswa_baru.*.nim.numeric' => 'NIM harus berupa angka.',
            'mahasiswa_baru.*.nim.unique' => 'NIM ini sudah terdaftar. Silakan gunakan NIM lain.',
            'luaran_data.HKI.*.required_if' => 'Field :attribute wajib diisi ketika luaran HKI dipilih.',
            'dokumen.hki.mimes' => 'Dokumen HKI harus berformat: pdf, doc, docx.',
            'dokumen.hki.max' => 'Ukuran Dokumen HKI maksimal 5MB.',
        ]);

        try {
            DB::beginTransaction();

            $pengabdian = Pengabdian::create([
                'judul_pengabdian' => $request->judul_pengabdian,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $request->ketua_nik, // simpan langsung ke tabel pengabdian
            ]);

            // Attach dosen (ketua + anggota) ke pivot pengabdian_dosen
            $dosenData = [];
            // masukkan ketua ke pivot dengan status 'ketua'
            $dosenData[$request->ketua_nik] = ['status_anggota' => 'ketua'];
            // masukkan anggota ke pivot dengan status 'anggota'
            if (!empty($request->dosen_ids)) {
                foreach ($request->dosen_ids as $nik) {
                    if (!$nik) { continue; }
                    if ($nik === $request->ketua_nik) { continue; } // hindari duplikasi jika anggota sama dengan ketua
                    $dosenData[$nik] = ['status_anggota' => 'anggota'];
                }
            }
            $pengabdian->dosen()->sync($dosenData);

            // Attach mahasiswa
            $nimMahasiswaToAttach = [];
            if (!empty($request->mahasiswa_ids)) {
                $nimMahasiswaToAttach = array_filter($request->mahasiswa_ids);
            }

            // Mahasiswa baru
            if ($request->filled('mahasiswa_baru')) {
                foreach ($request->mahasiswa_baru as $mhsBaru) {
                    if (!empty($mhsBaru['nim']) && !empty($mhsBaru['nama'])) {
                        $mahasiswa = Mahasiswa::firstOrCreate(
                            ['nim' => $mhsBaru['nim']],
                            [
                                'nama' => $mhsBaru['nama'],
                                'prodi' => $mhsBaru['prodi'] ?? null,
                            ]
                        );
                        $nimMahasiswaToAttach[] = $mahasiswa->nim;
                    }
                }
            }

            if (!empty($nimMahasiswaToAttach)) {
                $pengabdian->mahasiswa()->attach(array_unique($nimMahasiswaToAttach));
            }

            // Create mitra (satu saja)
            if ($request->filled('nama_mitra')) {
                $pengabdian->mitra()->create([
                    'nama_mitra'   => $request->nama_mitra,
                    'lokasi_mitra' => $request->lokasi_kegiatan,
                ]);
            }
            // Create sumber dana
            if ($request->sumber_dana) {
                foreach ($request->sumber_dana as $danaData) {
                    $namaSumber = $danaData['nama_sumber'] === 'Lainnya'
                        ? ($danaData['nama_lainnya'] ?? 'Lainnya')
                        : $danaData['nama_sumber'];
                    $jumlahDana = (int) preg_replace('/[^0-9]/', '', $danaData['jumlah_dana']);

                    $pengabdian->sumberDana()->create([
                        'jenis' => $danaData['jenis'],
                        'nama_sumber' => $namaSumber,
                        'jumlah_dana' => $jumlahDana,
                    ]);
                }
            }

            // ==========================================================
            // --- BLOK KODE UNTUK MEMPROSES INPUT LUARAN (FINAL) ---
            // ==========================================================
            if ($request->has('luaran_jenis') && is_array($request->luaran_jenis)) {
                foreach ($request->luaran_jenis as $jenisLuaranValue) {
                    
                    // 1. Cari ID jenis luaran dari tabel master `jenis_luaran`
                    $jenisLuaranModel = JenisLuaran::where('nama_jenis_luaran', $jenisLuaranValue)->first();
                    if (!$jenisLuaranModel) {
                        continue; // Lewati jika jenis luaran tidak ada di database
                    }

                    // 2. Simpan ke tabel `luaran`
                    $luaranRecord = $pengabdian->luaran()->create([
                        'id_jenis_luaran' => $jenisLuaranModel->id_jenis_luaran,
                    ]);

                    // 3. JIKA luarannya adalah HKI, proses data detailnya
                    if ($jenisLuaranValue === 'HKI' && $request->has('luaran_data.HKI')) {
                        $hkiData = $request->luaran_data['HKI'];

                        // 3a. Simpan detail HKI ke tabel `detail_hki`
                        $detailHki = $luaranRecord->detailHki()->create([
                            'no_pendaftaran' => $hkiData['no_pendaftaran'],
                            'tgl_permohonan' => $hkiData['tanggal_permohonan'],
                            'judul_ciptaan' => $hkiData['judul_ciptaan'],
                            'pemegang_hak_cipta' => $hkiData['pemegang_hak_cipta'],
                            'jenis_ciptaan' => $hkiData['jenis_ciptaan'],
                        ]);

                        // 3b. Simpan anggota HKI (dosen) ke tabel pivot `anggota_hki`
                        if (!empty($hkiData['anggota_dosen'])) {
                            $detailHki->dosen()->attach($hkiData['anggota_dosen']);
                        }

                        // 3c. Handle upload dokumen HKI
                        if ($request->hasFile('dokumen.hki')) {
                            $file = $request->file('dokumen.hki');
                            // Simpan file di storage/app/public/dokumen_hki
                            $path = $file->store('dokumen_hki', 'public');

                            // Simpan info file ke tabel `dokumen`
                            // Asumsi ID 2 di tabel `jenis_dokumen` adalah untuk 'Dokumen HKI'
                            $pengabdian->dokumen()->create([
                                'id_jenis_dokumen' => 2,
                                'nama_dokumen' => $file->getClientOriginalName(),
                                'path_file' => $path,
                            ]);
                        }
                    }
                    // Anda bisa menambahkan 'else if' di sini untuk jenis luaran lain yang punya detail khusus
                }
            }


            DB::commit();

            return redirect()->route('admin.pengabdian.index')
                ->with('success', 'Data pengabdian berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified service activity.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $pengabdian = Pengabdian::with([
            'dosen',
            'mahasiswa',
            'mitra',
            'sumberDana',
            'dokumen.jenisDokumen',
            'luaran.jenisLuaran',
            'luaran.detailHki.dosen' // Eager load anggota HKI
        ])->findOrFail($id);

        return view('admin.pengabdian.show', compact('pengabdian'));
    }

    /**
     * Show the form for editing the specified service activity.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $pengabdian = Pengabdian::with(['dosen', 'mahasiswa', 'mitra', 'sumberDana'])->findOrFail($id);
        $dosen = Dosen::orderBy('nama')->get();
        $mahasiswa = Mahasiswa::orderBy('nama')->get();

        return view('admin.pengabdian.edit', compact('pengabdian', 'dosen', 'mahasiswa'));
    }

    /**
     * Update the specified service activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO: Implementasikan logika update untuk pengabdian dan luarannya.
        // Logika update akan mirip dengan store, tapi menggunakan sync() untuk relasi
        // dan updateOrCreate() atau update() untuk data lainnya.
        // Hapus data luaran lama, lalu buat yang baru dari request.
        
        $request->validate([
            'judul_pengabdian' => 'required|string|max:255',
            'tanggal_pengabdian' => 'required|date',
            'ketua_nik' => 'required|exists:dosen,nik',
            // ... validasi lainnya
        ]);

        try {
            DB::beginTransaction();

            $pengabdian = Pengabdian::findOrFail($id);
            $pengabdian->update([
                'judul_pengabdian' => $request->judul_pengabdian,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $request->ketua_nik,
            ]);

            // ... (Tambahkan logika update untuk luaran di sini)

            DB::commit();

            return redirect()->route('admin.pengabdian.index')
                ->with('success', 'Data pengabdian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service activity from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $pengabdian = Pengabdian::findOrFail($id);
            $pengabdian->delete();

            return redirect()->route('admin.pengabdian.index')
                ->with('success', 'Data pengabdian berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

