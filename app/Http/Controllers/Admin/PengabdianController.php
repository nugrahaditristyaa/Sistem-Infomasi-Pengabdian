<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\JenisDokumen;
use App\Models\JenisLuaran;
use App\Models\Luaran;
use App\Models\SumberDana;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\UploadedFile;


class PengabdianController extends Controller
{
    /**
     * Menampilkan daftar semua kegiatan pengabdian.
     */
    public function index(Request $request)
    {
        // Build base query with necessary relations and aggregates
        $query = Pengabdian::with(['ketua', 'dosen', 'mahasiswa', 'mitra', 'luaran.jenisLuaran', 'luaran.detailHki'])
            ->withSum('sumberDana', 'jumlah_dana')
            ->withCount(['luaran as hki_count' => function ($q) {
                $q->whereHas('detailHki');
            }]);

        // Filter by year (GET param: year). 'all' means no year filter.
        if ($request->filled('year') && $request->get('year') !== 'all') {
            $year = (int) $request->get('year');
            $query->whereYear('tanggal_pengabdian', $year);
        }

        // Filter by sumber_dana (GET param: sumber_dana). Accepts id or textual values.
        if ($request->filled('sumber_dana')) {
            $sd = $request->get('sumber_dana');
            $query->whereHas('sumberDana', function ($q) use ($sd) {
                $q->where(function ($sub) use ($sd) {
                    $sub->where('id_sumber_dana', $sd)
                        ->orWhere('jenis', $sd)
                        ->orWhere('nama_sumber', 'like', "%{$sd}%");
                });
            });
        }

        // Filter by luaran (GET param: luaran). Accepts jenis id, kode or name.
        if ($request->filled('luaran')) {
            $lu = $request->get('luaran');
            $query->whereHas('luaran', function ($q) use ($lu) {
                $q->where('id_jenis_luaran', $lu)
                    ->orWhereHas('jenisLuaran', function ($jq) use ($lu) {
                        $jq->where('id_jenis_luaran', $lu)
                            ->orWhere('nama_jenis_luaran', 'like', "%{$lu}%");
                    });
            });
        }

        // Finalize query ordering and fetch
        $pengabdian = $query->orderBy('tanggal_pengabdian', 'desc')->get();

        // Prepare filter lists for the modal (fall back to reasonable defaults)
        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $sumberDanaList = SumberDana::orderBy('nama_sumber')->get();
        $jenisLuaran = JenisLuaran::orderBy('nama_jenis_luaran')->get();

        return view('admin.pengabdian.index', compact('pengabdian', 'availableYears', 'sumberDanaList', 'jenisLuaran'));
    }

    /**
     * Export pengabdian list as XLSX (fallback CSV if PhpSpreadsheet not installed)
     */
    public function export(Request $request)
    {
        // Build same base query and filters as index
        $query = Pengabdian::with(['ketua', 'dosen', 'mahasiswa', 'mitra', 'luaran.jenisLuaran', 'luaran.detailHki'])
            ->withSum('sumberDana', 'jumlah_dana')
            ->withCount(['luaran as hki_count' => function ($q) {
                $q->whereHas('detailHki');
            }]);

        if ($request->filled('year') && $request->get('year') !== 'all') {
            $year = (int) $request->get('year');
            $query->whereYear('tanggal_pengabdian', $year);
        }

        if ($request->filled('sumber_dana')) {
            $sd = $request->get('sumber_dana');
            $query->whereHas('sumberDana', function ($q) use ($sd) {
                $q->where(function ($sub) use ($sd) {
                    $sub->where('id_sumber_dana', $sd)
                        ->orWhere('jenis', $sd)
                        ->orWhere('nama_sumber', 'like', "%{$sd}%");
                });
            });
        }

        if ($request->filled('luaran')) {
            $lu = $request->get('luaran');
            $query->whereHas('luaran', function ($q) use ($lu) {
                $q->where('id_jenis_luaran', $lu)
                    ->orWhereHas('jenisLuaran', function ($jq) use ($lu) {
                        $jq->where('id_jenis_luaran', $lu)
                            ->orWhere('nama_jenis_luaran', 'like', "%{$lu}%");
                    });
            });
        }

        $items = $query->orderBy('tanggal_pengabdian', 'desc')->get();

        $baseName = 'export_pengabdian_' . date('YmdHis');

        if (class_exists(Spreadsheet::class)) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $headers = ['No', 'Judul Pengabdian', 'Tanggal Pengabdian', 'Ketua', 'Anggota', 'Mahasiswa', 'Mitra', 'Jenis Sumber Dana', 'Nama Sumber Dana', 'Luaran Kegiatan', 'Total Dana'];
            $col = 1;
            foreach ($headers as $h) {
                $coord = Coordinate::stringFromColumnIndex($col) . '1';
                $sheet->setCellValue($coord, $h);
                $col++;
            }

            $row = 2;
            $no = 1;
            foreach ($items as $item) {
                $col = 1;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $no++);
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->judul_pengabdian);
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, optional($item->tanggal_pengabdian)->format('Y-m-d'));
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->ketua->nama ?? '-');
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->dosen->pluck('nama')->implode('; '));
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->mahasiswa->pluck('nama')->implode('; '));
                $col++;
                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->mitra->pluck('nama_mitra')->implode('; '));
                $col++;

                // Gabungkan jenis dan nama sumber dana yang terkait (pisah dengan '; ')
                $jenisSumber = $item->sumberDana->pluck('jenis')->filter()->unique()->values()->implode('; ');
                $namaSumber = $item->sumberDana->pluck('nama_sumber')->filter()->unique()->values()->implode('; ');

                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $jenisSumber ?: '-');
                $col++;

                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $namaSumber ?: '-');
                $col++;

                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->luaran->pluck('jenisLuaran.nama_jenis_luaran')->implode('; '));
                $col++;

                $coord = Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($coord, $item->sumber_dana_sum_jumlah_dana ?? 0);
                $col++;
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $filename = $baseName . '.xlsx';

            return response()->stream(function () use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }

        // CSV fallback
        $filename = $baseName . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($items) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($fh, ['No', 'Judul Pengabdian', 'Tanggal Pengabdian', 'Ketua', 'Anggota', 'Mahasiswa', 'Mitra', 'Jenis Sumber Dana', 'Nama Sumber Dana', 'Luaran Kegiatan', 'Total Dana']);
            $no = 1;
            foreach ($items as $item) {
                // Prepare jenis & nama sumber dana for CSV
                $jenisSumberCsv = $item->sumberDana->pluck('jenis')->filter()->unique()->values()->implode('; ');
                $namaSumberCsv = $item->sumberDana->pluck('nama_sumber')->filter()->unique()->values()->implode('; ');

                fputcsv($fh, [
                    $no++,
                    $item->judul_pengabdian,
                    optional($item->tanggal_pengabdian)->format('Y-m-d'),
                    $item->ketua->nama ?? '-',
                    $item->dosen->pluck('nama')->implode('; '),
                    $item->mahasiswa->pluck('nama')->implode('; '),
                    $item->mitra->pluck('nama_mitra')->implode('; '),
                    $jenisSumberCsv ?: '-',
                    $namaSumberCsv ?: '-',
                    $item->luaran->pluck('jenisLuaran.nama_jenis_luaran')->implode('; '),
                    $item->sumber_dana_sum_jumlah_dana ?? 0
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate and return a template XLSX for pengabdian import.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Use exact import field names as column headers to avoid user confusion
        // switched ketua_nik->nama_ketua and dosen_niks->nama_anggota to prefer name-based import
        $headers = [
            'judul_pengabdian',
            'tanggal_pengabdian',
            'nama_ketua',
            'nama_anggota',
            'mahasiswa_nims',
            'nama_mitra',
            'lokasi_kegiatan',
            'jenis_sumber_dana',
            'nama_sumber_dana',
            'luaran_kegiatan',
            'total_dana'
        ];

        $col = 1;
        foreach ($headers as $h) {
            $coord = Coordinate::stringFromColumnIndex($col) . '1';
            $sheet->setCellValue($coord, $h);
            $col++;
        }

        // sample row (use example NAMES for ketua/anggota to guide user)
        $sample = [
            'Pelatihan Pembuatan Database',
            date('Y-m-d'),
            'Dr. Yacob Supranto',
            'Dr. Budi Santoso;Dr. Ani Putri',
            '18012345;18054321',
            'PT Contoh',
            'Yogyakarta',
            'internal;eksternal',
            'LPPM;Prodi',
            'HKI;Publikasi',
            '5000000'
        ];
        $col = 1;
        foreach ($sample as $s) {
            $coord = Coordinate::stringFromColumnIndex($col) . '2';
            $sheet->setCellValue($coord, $s);
            $col++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_pengabdian.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Import pengabdian from uploaded XLSX/CSV file. Validation rules:
     * - Required header: judul_pengabdian
     * - Skip rows with existing judul_pengabdian (report as skipped)
     * - Attach only existing dosen (by nik or name like) and mahasiswa (by nim)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'File kosong atau tidak ada data baris.');
        }

        // Build header map (case-insensitive) and normalize to canonical keys
        $headerRaw = array_values($rows[1]);
        $headerMap = [];
        $normalize = function ($h) {
            $s = strtolower(trim((string)$h));
            if ($s === '') return '';
            if (str_contains($s, 'judul')) return 'judul_pengabdian';
            if (str_contains($s, 'tanggal')) return 'tanggal_pengabdian';
            if (str_contains($s, 'ketua')) return 'ketua';
            if (str_contains($s, 'dosen')) return 'dosen';
            if (str_contains($s, 'anggota')) return 'dosen';
            if (str_contains($s, 'mahasiswa')) return 'mahasiswa';
            if (str_contains($s, 'lokasi')) return 'lokasi_kegiatan';
            if (str_contains($s, 'nama_mitra') || str_contains($s, 'mitra')) return 'nama_mitra';
            if (str_contains($s, 'jenis_sumber')) return 'jenis_sumber_dana';
            if (str_contains($s, 'nama_sumber')) return 'nama_sumber_dana';
            if (str_contains($s, 'luaran')) return 'luaran_kegiatan';
            if (str_contains($s, 'total') || str_contains($s, 'jumlah')) return 'total_dana';
            // fallback: keep only alphanum and underscores
            return preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '_', $s));
        };

        foreach ($headerRaw as $i => $h) {
            $headerMap[$i] = $normalize($h);
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        for ($r = 2; $r <= count($rows); $r++) {
            $cols = array_values($rows[$r]);
            if (count(array_filter($cols)) === 0) continue; // empty row

            $rowAssoc = [];
            foreach ($cols as $i => $val) {
                $key = $headerMap[$i] ?? ('col' . $i);
                $rowAssoc[$key] = trim((string)$val);
            }

            $judul = $rowAssoc['judul_pengabdian'] ?? $rowAssoc['judul'] ?? null;
            if (!$judul) {
                $errors[] = "Baris $r: Judul kosong.";
                $skipped++;
                continue;
            }
            // Skip if already exists
            if (Pengabdian::where('judul_pengabdian', $judul)->exists()) {
                $errors[] = "Baris $r: Judul sudah ada (dilewati).";
                $skipped++;
                continue;
            }

            $tanggalRaw = $rowAssoc['tanggal_pengabdian'] ?? $rowAssoc['tanggal'] ?? null;
            try {
                $tanggal = $tanggalRaw ? Carbon::parse(str_replace('/', '-', $tanggalRaw)) : null;
            } catch (\Exception $e) {
                $tanggal = null;
            }
            $ketuaVal = $rowAssoc['ketua'] ?? $rowAssoc['ketua_nik'] ?? null;
            $ketua = null;

            $findDosen = function ($val) {
                $val = trim((string)$val);
                if ($val === '') return null;
                // 1) try exact name match
                $d = Dosen::where('nama', $val)->first();
                if ($d) return $d;
                // 2) try exact NIK match
                $d = Dosen::where('nik', $val)->first();
                if ($d) return $d;
                // 3) fallback to partial name
                return Dosen::where('nama', 'like', "%{$val}%")->first();
            };

            if ($ketuaVal) {
                $ketua = $findDosen($ketuaVal);
            }

            $dosenVal = $rowAssoc['dosen'] ?? $rowAssoc['dosen_niks'] ?? $rowAssoc['dosen_niks'] ?? null;
            $dosenSync = [];
            if ($ketua) $dosenSync[$ketua->nik] = ['status_anggota' => 'ketua'];
            if ($dosenVal) {
                $parts = preg_split('/[;,]+/', $dosenVal);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!$p) continue;
                    $d = $findDosen($p);
                    if ($d && ($d->nik !== ($ketua->nik ?? null))) $dosenSync[$d->nik] = ['status_anggota' => 'anggota'];
                }
            }

            $mhsNims = $rowAssoc['mahasiswa_nims (pisah dengan ; )'] ?? $rowAssoc['mahasiswa_nims'] ?? $rowAssoc['mahasiswa'] ?? null;
            $mhsAttach = [];
            if ($mhsNims) {
                $parts = preg_split('/[;,]+/', $mhsNims);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!$p) continue;
                    $m = Mahasiswa::where('nim', $p)->first();
                    if ($m) $mhsAttach[] = $m->nim;
                }
            }

            // create pengabdian
            DB::beginTransaction();
            try {
                $p = Pengabdian::create([
                    'judul_pengabdian' => $judul,
                    'tanggal_pengabdian' => $tanggal,
                    'ketua_pengabdian' => $ketua->nik ?? null,
                ]);

                if (!empty($dosenSync)) $p->dosen()->sync($dosenSync);
                if (!empty($mhsAttach)) $p->mahasiswa()->attach(array_unique($mhsAttach));

                // mitra: create if nama_mitra provided
                $namaMitra = $rowAssoc['nama_mitra'] ?? null;
                $lokasiMitra = $rowAssoc['lokasi_kegiatan'] ?? ($rowAssoc['lokasi'] ?? null);
                if ($namaMitra) {
                    $p->mitra()->create([
                        'nama_mitra' => trim($namaMitra),
                        'lokasi_mitra' => $lokasiMitra ? trim($lokasiMitra) : null,
                    ]);
                }

                // sumber dana: use provided jenis/nama/total if present
                $jenisSumber = $rowAssoc['jenis_sumber_dana (pisah ; )'] ?? $rowAssoc['jenis_sumber_dana'] ?? null;
                $namaSumber = $rowAssoc['nama_sumber_dana (pisah ; )'] ?? $rowAssoc['nama_sumber_dana'] ?? null;
                $totalDana = isset($rowAssoc['total_dana (numeric)']) ? preg_replace('/[^0-9.-]/', '', $rowAssoc['total_dana (numeric)']) : (isset($rowAssoc['total_dana']) ? preg_replace('/[^0-9.-]/', '', $rowAssoc['total_dana']) : null);

                if ($jenisSumber || $namaSumber || $totalDana) {
                    $partsJenis = $jenisSumber ? preg_split('/[;,]+/', $jenisSumber) : ['import'];
                    $partsNama = $namaSumber ? preg_split('/[;,]+/', $namaSumber) : ['Import'];
                    // create a single sumberDana record combining first entries
                    $p->sumberDana()->create([
                        'jenis' => trim($partsJenis[0] ?? 'import'),
                        'nama_sumber' => trim($partsNama[0] ?? 'Import Excel'),
                        'jumlah_dana' => is_numeric($totalDana) ? (float)$totalDana : 0,
                    ]);
                }

                // Luaran: if provided in import, sync using existing helper
                $luaranRaw = $rowAssoc['luaran_kegiatan'] ?? $rowAssoc['luaran'] ?? null;
                if ($luaranRaw) {
                    $luaranParts = array_filter(array_map('trim', preg_split('/[;,]+/', $luaranRaw)));
                    if (!empty($luaranParts)) {
                        $fakeReq = new \Illuminate\Http\Request();
                        $fakeReq->merge(['luaran_jenis' => $luaranParts]);
                        // sync luaran records (this will create luaran entries matching JenisLuaran)
                        $this->syncLuaranDanDokumen($fakeReq, $p, false);
                    }
                }

                DB::commit();
                $created++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Baris $r: Gagal simpan - " . $e->getMessage();
                $skipped++;
            }
        }

        $msg = "Import selesai. Berhasil: $created, Dilewati: $skipped.";
        if (!empty($errors)) session()->flash('import_errors', array_slice($errors, 0, 100));

        return redirect()->route('admin.pengabdian.index')->with('success', $msg);
    }

    /**
     * Menampilkan form untuk membuat pengabdian baru.
     */
    public function create()
    {
        $dosen = Dosen::orderBy('nama')->get();
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        $jenisLuaran = JenisLuaran::orderBy('id_jenis_luaran')->get();

        return view('admin.pengabdian.create', compact('dosen', 'mahasiswa', 'jenisLuaran'));
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
            // Judul pengabdian harus unik di tabel pengabdian
            'judul_pengabdian'      => ['required', 'string', 'max:255', Rule::unique('pengabdian', 'judul_pengabdian')],
            'nama_mitra'            => 'nullable|string|max:255',
            // 'nama_mitra'            => 'required|string|max:255',
            // 'lokasi_kegiatan'       => 'required|string|max:255',
            'lokasi_kegiatan'       => 'nullable|string|max:255',
            'tanggal_pengabdian'    => ['required', new ValidTanggal(2000, 'Tanggal Pengabdian')],
            'jumlah_luaran_direncanakan'   => 'required|array|min:1',
            'jumlah_luaran_direncanakan.*' => 'required|exists:jenis_luaran,nama_jenis_luaran',
            
            // Ketua: required UNLESS dosen_baru_ketua is filled
            'ketua_nik'             => 'required_without:dosen_baru_ketua.nik|nullable|exists:dosen,nik',
            
            // Dosen Baru Ketua
            'dosen_baru_ketua.nik'  => 'required_with:dosen_baru_ketua.nama|nullable|string|max:255|unique:dosen,nik',
            'dosen_baru_ketua.nama' => 'required_with:dosen_baru_ketua.nik|nullable|string|max:255',
            'dosen_baru_ketua.nidn' => 'nullable|string|max:255',
            'dosen_baru_ketua.jabatan' => 'nullable|string|max:255',
            'dosen_baru_ketua.prodi' => 'required_with:dosen_baru_ketua.nik|nullable|string|max:255',
            'dosen_baru_ketua.bidang_keahlian' => 'nullable|string|max:255',
            'dosen_baru_ketua.email' => 'required_with:dosen_baru_ketua.nik|nullable|email|max:255|unique:dosen,email',
            
            'anggota'               => 'nullable|array',
            'anggota.*'             => 'nullable|exists:dosen,nik|different:ketua_nik',
            
            // Dosen Baru Anggota
            'dosen_baru_anggota'        => 'nullable|array',
            'dosen_baru_anggota.*.nik'  => 'required_with:dosen_baru_anggota.*.nama|nullable|string|max:255|distinct|unique:dosen,nik',
            'dosen_baru_anggota.*.nama' => 'nullable|string|max:255',
            'dosen_baru_anggota.*.nidn' => 'nullable|string|max:255',
            'dosen_baru_anggota.*.jabatan' => 'nullable|string|max:255',
            'dosen_baru_anggota.*.prodi' => 'nullable|string|max:255',
            'dosen_baru_anggota.*.bidang_keahlian' => 'nullable|string|max:255',
            'dosen_baru_anggota.*.email' => 'nullable|email|max:255',
            
            'mahasiswa_ids'         => 'nullable|array',
            'mahasiswa_ids.*'       => 'nullable|exists:mahasiswa,nim',
            'mahasiswa_baru'        => ['nullable', 'array', new UniqueNimsInArray, new AllNimsMustHaveCorrectDigits, new NimsMustNotExist, new AllMahasiswaRowsMustBeComplete], // <-- SAYA MENAMBAHKAN ATURAN INI
            // NIM Mahasiswa Baru: hanya angka, 8 digit, unik dan tidak duplikat di array
            'mahasiswa_baru.*.nim'  => 'required_with:mahasiswa_baru.*.nama|nullable|numeric|digits:8|distinct|unique:mahasiswa,nim',
            'mahasiswa_baru.*.nama' => 'nullable|string|max:255',
            'mahasiswa_baru.*.prodi' => 'nullable|string|max:255',
            'sumber_dana'               => 'required|array|min:1',
            'sumber_dana.*.jenis'       => 'required|string',
            'sumber_dana.*.nama_sumber' => 'required|string',
            'sumber_dana.*.jumlah_dana' => 'required|numeric|min:0',
            // id_luaran_wajib removed: no longer required/validated
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
            'required_without' => ':attribute wajib diisi jika tidak mengisi Dosen Eksternal.',
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
            'judul_pengabdian.unique' => 'judul pengabdian sudah digunakan',
            'distinct'      => 'Terdapat duplikasi :attribute pada baris yang ditambahkan.',
            'jumlah_luaran_direncanakan.required' => 'Jenis Luaran Yang Direncanakan wajib diisi',
            'mahasiswa_baru.*.nim.unique' => 'NIM Mahasiswa Baru sudah digunakan.',
            'different'     => ':attribute tidak boleh sama dengan Ketua Pengabdian.',
            'array'         => ':attribute harus berupa array.',
            'min'           => ':attribute minimal harus memiliki :min data.',
            'mimes'         => 'Format file :attribute harus :values.',
            'file'          => ':attribute harus berupa file.',
            'luaran_data.HKI.tanggal_permohonan.after_or_equal' => 'Tanggal Permohonan tidak boleh lebih awal dari Tanggal Pengabdian.',
            'ketua_nik.required_without' => 'Pilih Ketua dari Dosen FTI atau tambahkan Dosen Eksternal (harus memilih salah satu).',

        ], [
            // --- Nama Atribut (Custom Attributes) ---
            'judul_pengabdian'      => 'Judul Pengabdian',
            'nama_mitra'            => 'Nama Mitra',
            'lokasi_kegiatan'       => 'Lokasi Kegiatan Mitra',
            'tanggal_pengabdian'    => 'Tanggal Pengabdian',
            'ketua_nik'             => 'Dosen (Ketua)',
            'dosen_baru_ketua.nik'  => 'NIK Dosen Ketua Baru',
            'dosen_baru_ketua.nama' => 'Nama Dosen Ketua Baru',
            'dosen_baru_ketua.nidn' => 'NIDN Dosen Ketua Baru',
            'dosen_baru_ketua.jabatan' => 'Jabatan Dosen Ketua Baru',
            'dosen_baru_ketua.prodi' => 'Prodi Dosen Ketua Baru',
            'dosen_baru_ketua.bidang_keahlian' => 'Bidang Keahlian Dosen Ketua Baru',
            'dosen_baru_ketua.email' => 'Email Dosen Ketua Baru',
            'anggota.*'             => 'Dosen (Anggota)',
            'dosen_baru_anggota.*.nik'  => 'NIK Dosen Anggota Baru',
            'dosen_baru_anggota.*.nama' => 'Nama Dosen Anggota Baru',
            'dosen_baru_anggota.*.nidn' => 'NIDN Dosen Anggota Baru',
            'dosen_baru_anggota.*.jabatan' => 'Jabatan Dosen Anggota Baru',
            'dosen_baru_anggota.*.prodi' => 'Prodi Dosen Anggota Baru',
            'dosen_baru_anggota.*.bidang_keahlian' => 'Bidang Keahlian Dosen Anggota Baru',
            'dosen_baru_anggota.*.email' => 'Email Dosen Anggota Baru',
            'mahasiswa_ids.*'       => 'Mahasiswa',
            'mahasiswa_baru.*.nim'  => 'NIM Mahasiswa Baru',
            'mahasiswa_baru.*.nama' => 'Nama Mahasiswa Baru',
            'mahasiswa_baru.*.prodi' => 'Prodi Mahasiswa Baru',
            'sumber_dana'           => 'Sumber Dana',
            'sumber_dana.*.jenis'   => 'Jenis Sumber Dana',
            'sumber_dana.*.nama_sumber' => 'Nama Sumber Dana',
            'sumber_dana.*.jumlah_dana' => 'Jumlah Dana',
            // 'id_luaran_wajib' => 'Luaran Wajib', // removed attribute mapping
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
            'jumlah_luaran_direncanakan'        => 'Jenis Luaran yang Direncanakan',
            'jumlah_luaran_direncanakan.*'      => 'Jenis Luaran',
        ]);

        DB::beginTransaction();
        try {
            // === CREATE DOSEN BARU KETUA (if provided) ===
            $ketuaNik = $request->ketua_nik;
            if ($request->filled('dosen_baru_ketua.nik') && $request->filled('dosen_baru_ketua.nama')) {
                $dosenKetua = Dosen::firstOrCreate(
                    ['nik' => $request->input('dosen_baru_ketua.nik')],
                    [
                        'nama' => $request->input('dosen_baru_ketua.nama'),
                        'nidn' => $request->input('dosen_baru_ketua.nidn'),
                        'jabatan' => $request->input('dosen_baru_ketua.jabatan'),
                        'prodi' => $request->input('dosen_baru_ketua.prodi'),
                        'bidang_keahlian' => $request->input('dosen_baru_ketua.bidang_keahlian'),
                        'email' => $request->input('dosen_baru_ketua.email'),
                    ]
                );
                $ketuaNik = $dosenKetua->nik;
            }

            $pengabdian = Pengabdian::create([
                'judul_pengabdian' => $request->judul_pengabdian,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $ketuaNik,
                'jumlah_luaran_direncanakan' => $request->jumlah_luaran_direncanakan,
            ]);

            // === BUILD DOSEN DATA ===
            $dosenData = [$ketuaNik => ['status_anggota' => 'ketua']];
            
            // Ambil ID dosen dari input anggota utama
            $mainDosenIds = $request->anggota ? array_filter($request->anggota) : [];
            
            // === CREATE DOSEN BARU ANGGOTA (if provided) ===
            $newAnggotaIds = [];
            if ($request->filled('dosen_baru_anggota')) {
                foreach ($request->dosen_baru_anggota as $dsn) {
                    if (!empty($dsn['nik']) && !empty($dsn['nama'])) {
                        $dosenAnggota = Dosen::firstOrCreate(
                            ['nik' => $dsn['nik']],
                            [
                                'nama' => $dsn['nama'],
                                'nidn' => $dsn['nidn'] ?? null,
                                'jabatan' => $dsn['jabatan'] ?? null,
                                'prodi' => $dsn['prodi'] ?? null,
                                'bidang_keahlian' => $dsn['bidang_keahlian'] ?? null,
                                'email' => $dsn['email'] ?? null,
                            ]
                        );
                        $newAnggotaIds[] = $dosenAnggota->nik;
                    }
                }
            }
            
            // Ambil ID dosen dari input anggota HKI (jika ada)
            $hkiDosenIds = [];
            if ($request->has('luaran_data.HKI.anggota_dosen')) {
                $hkiDosenIds = $request->input('luaran_data.HKI.anggota_dosen');
                $hkiDosenIds = is_array($hkiDosenIds) ? array_filter($hkiDosenIds) : [];
            }

            // Gabungkan semua list anggota (unik)
            $allAnggotaIds = array_unique(array_merge($mainDosenIds, $newAnggotaIds, $hkiDosenIds));

            if (!empty($allAnggotaIds)) {
                foreach ($allAnggotaIds as $nik) {
                    // Pastikan ketua tidak masuk sebagai anggota
                    if ($nik !== $ketuaNik) {
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
        $jenisLuaran = JenisLuaran::orderBy('id_jenis_luaran')->get();

        return view('admin.pengabdian.edit', compact(
            'pengabdian',
            'dosen',
            'mahasiswa',
            'jenisLuaran'
        ));
    }

    /**
     * Memperbarui data pengabdian di database.
     */
    public function update(Request $request, $id)
    {
        if (session('_token') !== $request->input('_token')) {
            return response()->json(['error' => 'CSRF Token Mismatch: Token tidak valid atau kadaluarsa.'], 419);
        }

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
            // Judul pengabdian harus unik, kecuali untuk record yang sedang diupdate
            'judul_pengabdian'      => ['required', 'string', 'max:255', Rule::unique('pengabdian', 'judul_pengabdian')->ignore($pengabdian->id_pengabdian, 'id_pengabdian')],
            'nama_mitra'            => 'nullable|string|max:255',
            // 'nama_mitra'            => 'required|string|max:255',
            // 'lokasi_kegiatan'       => 'required|string|max:255',
            'lokasi_kegiatan'       => 'nullable|string|max:255',
            'tanggal_pengabdian'    => ['required', new ValidTanggal(2000)],
            'jumlah_luaran_direncanakan'   => 'required|array|min:1',
            'jumlah_luaran_direncanakan.*' => 'required|exists:jenis_luaran,nama_jenis_luaran',
            'ketua_nik'             => 'required|exists:dosen,nik',
            'anggota'             => 'nullable|array',
            'anggota.*'           => 'nullable|exists:dosen,nik|different:ketua_nik',
            'mahasiswa_ids'         => 'nullable|array',
            'mahasiswa_ids.*'       => 'nullable|exists:mahasiswa,nim',
            'mahasiswa_baru'        => 'nullable|array',
            'mahasiswa_baru.*.nim'  => 'required_with:mahasiswa_baru.*.nama|nullable|numeric|digits:8|distinct|unique:mahasiswa,nim',
            'mahasiswa_baru.*.nama' => 'required_with:mahasiswa_baru.*.nim|nullable|string|max:255',
            'mahasiswa_baru.*.prodi' => 'required_with:mahasiswa_baru.*.nim|nullable|string|max:255',
            'sumber_dana'               => 'required|array|min:1',
            'sumber_dana.*.jenis'       => 'required|string',
            'sumber_dana.*.nama_sumber' => 'required|string',
            'sumber_dana.*.jumlah_dana' => 'required|numeric|min:0',
            // id_luaran_wajib removed: no longer required/validated
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
            'judul_pengabdian.unique' => 'judul pengabdian sudah digunakan',
            'unique'        => ':attribute ini sudah terdaftar.',
            'jumlah_luaran_direncanakan.required' => 'Jenis Luaran Yang Direncanakan wajib diisi',
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
            'anggota.*'           => 'Dosen (Anggota)',
            'mahasiswa_ids.*'       => 'Mahasiswa',
            'mahasiswa_baru.*.nim'  => 'NIM Mahasiswa Baru',
            'mahasiswa_baru.*.nama' => 'Nama Mahasiswa Baru',
            'mahasiswa_baru.*.prodi' => 'Prodi Mahasiswa Baru',
            'sumber_dana'           => 'Sumber Dana',
            'sumber_dana.*.jenis'   => 'Jenis Sumber Dana',
            'sumber_dana.*.nama_sumber' => 'Nama Sumber Dana',
            'sumber_dana.*.jumlah_dana' => 'Jumlah Dana',
            // 'id_luaran_wajib' => 'Luaran Wajib', // removed attribute mapping
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
            'jumlah_luaran_direncanakan'        => 'Jenis Luaran yang Direncanakan',
            'jumlah_luaran_direncanakan.*'      => 'Jenis Luaran',
        ]);

        DB::beginTransaction();
        try {
            $pengabdian->update([
                'judul_pengabdian' => $request->judul_pengabdian,
                'tanggal_pengabdian' => $request->tanggal_pengabdian,
                'ketua_pengabdian' => $request->ketua_nik,
                'jumlah_luaran_direncanakan' => $request->jumlah_luaran_direncanakan,
            ]);

            $dosenData = [$request->ketua_nik => ['status_anggota' => 'ketua']];
            
            // Ambil ID dosen dari input anggota utama
            $mainDosenIds = $request->anggota ? array_filter($request->anggota) : [];
            
            // Ambil ID dosen dari input anggota HKI (jika ada)
            $hkiDosenIds = [];
            // Perhatikan: saat update, data HKI mungkin ada di request meski HKI tidak dicentang jika user tidak mengubah centangnya?
            // Tapi validasi / UI biasanya handle. Kita cek existence key-nya.
            if ($request->has('luaran_data.HKI.anggota_dosen')) {
                $hkiDosenIds = $request->input('luaran_data.HKI.anggota_dosen');
                $hkiDosenIds = is_array($hkiDosenIds) ? array_filter($hkiDosenIds) : [];
            }

            // Gabungkan kedua list anggota (unik)
            $allAnggotaIds = array_unique(array_merge($mainDosenIds, $hkiDosenIds));

            if (!empty($allAnggotaIds)) {
                foreach ($allAnggotaIds as $nik) {
                     // Pastikan ketua tidak masuk sebagai anggota
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
