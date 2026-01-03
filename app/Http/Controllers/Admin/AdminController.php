<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Luaran;
use App\Models\JenisLuaran;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!$request->has('year')) {
            // Redirect ke route ini lagi DENGAN parameter TAHUN 2024
            return redirect()->route('admin.dashboard', ['year' => '2024']); // <-- UBAH DI SINI
        }

        // Get year filter parameter - default to current year
        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);

        // Get available years from pengabdian data for dropdown
        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->whereNotNull('tanggal_pengabdian')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        // 1. Latest pengabdian (used by dashboard view)
        // Eager-load dokumen and the dokumen's jenisDokumen for per-type status
        // Show the most recently added pengabdian (by created_at)
        $latestPengabdian = Pengabdian::with(['ketua', 'dokumen.jenisDokumen'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // 2. Dynamic Quick Statistics based on filter
        if ($filterYear === 'all') {
            // Show all time statistics
            $totalPengabdian = Pengabdian::count();
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
            $pengabdianDenganMahasiswa = Pengabdian::whereHas('mahasiswa')->count();

            // Comparison with previous year
            $previousYear = $currentYear - 1;
            $totalPengabdianComparison = Pengabdian::whereYear('tanggal_pengabdian', $currentYear)->count();
            $totalPengabdianPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousYear)->count();

            $dosenTerlibatComparison = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->whereYear('pengabdian.tanggal_pengabdian', $currentYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->whereYear('pengabdian.tanggal_pengabdian', $previousYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $pengabdianDenganMahasiswaComparison = Pengabdian::whereYear('tanggal_pengabdian', $currentYear)
                ->whereHas('mahasiswa')->count();
            $pengabdianDenganMahasiswaPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousYear)
                ->whereHas('mahasiswa')->count();

            $yearLabel = "vs $previousYear";
        } else {
            // Show filtered year statistics  
            $totalPengabdian = Pengabdian::whereYear('tanggal_pengabdian', $filterYear)->count();
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $pengabdianDenganMahasiswa = Pengabdian::whereYear('tanggal_pengabdian', $filterYear)
                ->whereHas('mahasiswa')->count();

            // Comparison with previous year from filtered year
            $previousFilterYear = $filterYear - 1;
            $totalPengabdianComparison = $totalPengabdian;
            $totalPengabdianPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousFilterYear)->count();

            $dosenTerlibatComparison = $totalDosenTerlibat;
            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->whereYear('pengabdian.tanggal_pengabdian', $previousFilterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $pengabdianDenganMahasiswaComparison = $pengabdianDenganMahasiswa;
            $pengabdianDenganMahasiswaPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousFilterYear)
                ->whereHas('mahasiswa')->count();

            $yearLabel = "vs $previousFilterYear";
        }

        // Calculate percentage changes
        $percentageChangePengabdian = $totalPengabdianPrevious > 0 ?
            round((($totalPengabdianComparison - $totalPengabdianPrevious) / $totalPengabdianPrevious) * 100, 1) : ($totalPengabdianComparison > 0 ? 100 : 0);

        $percentageChangeDosen = $dosenTerlibatPrevious > 0 ?
            round((($dosenTerlibatComparison - $dosenTerlibatPrevious) / $dosenTerlibatPrevious) * 100, 1) : ($dosenTerlibatComparison > 0 ? 100 : 0);

        $percentageChangeMahasiswa = $pengabdianDenganMahasiswaPrevious > 0 ?
            round((($pengabdianDenganMahasiswaComparison - $pengabdianDenganMahasiswaPrevious) / $pengabdianDenganMahasiswaPrevious) * 100, 1) : ($pengabdianDenganMahasiswaComparison > 0 ? 100 : 0);

        // Calculate percentage of pengabdian with mahasiswa
        $persentasePengabdianDenganMahasiswa = $totalPengabdian > 0 ?
            round(($pengabdianDenganMahasiswa / $totalPengabdian) * 100, 1) : 0;

        // Calculate stats per prodi (Kolaborasi, Khusus Informatika, Khusus Sistem Informasi)
        if ($filterYear === 'all') {
            // All time stats per prodi
            // Pengabdian Kolaborasi: memiliki minimal 1 dosen dari Informatika DAN minimal 1 dosen dari Sistem Informasi
            $pengabdianKolaborasi = DB::table('pengabdian')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Informatika');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Sistem Informasi');
                })
                ->count();

            // Pengabdian Khusus Informatika: hanya memiliki dosen dari prodi Informatika
            $pengabdianKhususInformatika = DB::table('pengabdian')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Informatika');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', '!=', 'Informatika');
                })
                ->count();

            // Pengabdian Khusus Sistem Informasi: hanya memiliki dosen dari prodi Sistem Informasi
            $pengabdianKhususSistemInformasi = DB::table('pengabdian')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Sistem Informasi');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', '!=', 'Sistem Informasi');
                })
                ->count();

            // Count unique dosen per prodi (all time)
            $dosenInformatika = DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Informatika')
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenSistemInformasi = DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Sistem Informasi')
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            // Count unique mahasiswa per prodi (all time)
            $mahasiswaInformatika = DB::table('pengabdian_mahasiswa')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Informatika')
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim');

            $mahasiswaSistemInformasi = DB::table('pengabdian_mahasiswa')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Sistem Informasi')
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim');
        } else {
            // Filtered year stats per prodi
            // Pengabdian Kolaborasi: memiliki minimal 1 dosen dari Informatika DAN minimal 1 dosen dari Sistem Informasi
            $pengabdianKolaborasi = DB::table('pengabdian')
                ->whereYear('tanggal_pengabdian', $filterYear)
                ->whereExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Informatika');
                })
                ->whereExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Sistem Informasi');
                })
                ->count();

            // Pengabdian Khusus Informatika: hanya memiliki dosen dari prodi Informatika
            $pengabdianKhususInformatika = DB::table('pengabdian')
                ->whereYear('tanggal_pengabdian', $filterYear)
                ->whereExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Informatika');
                })
                ->whereNotExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', '!=', 'Informatika');
                })
                ->count();

            // Pengabdian Khusus Sistem Informasi: hanya memiliki dosen dari prodi Sistem Informasi
            $pengabdianKhususSistemInformasi = DB::table('pengabdian')
                ->whereYear('tanggal_pengabdian', $filterYear)
                ->whereExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', 'Sistem Informasi');
                })
                ->whereNotExists(function ($query) use ($filterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', '!=', 'Sistem Informasi');
                })
                ->count();

            // Count unique dosen per prodi (filtered year)
            $dosenInformatika = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Informatika')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenSistemInformasi = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Sistem Informasi')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            // Count unique mahasiswa per prodi (filtered year)
            $mahasiswaInformatika = DB::table('pengabdian_mahasiswa')
                ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Informatika')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim');

            $mahasiswaSistemInformasi = DB::table('pengabdian_mahasiswa')
                ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Sistem Informasi')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim');
        }

        // Get total number of all lecturers in FTI (only Informatika and Sistem Informasi)
        $totalDosenKeseluruhan = Dosen::fti()->count();

        $stats = [
            'total_pengabdian' => $totalPengabdian,
            'total_dosen' => $totalDosenTerlibat,
            'total_dosen_keseluruhan' => $totalDosenKeseluruhan,
            'total_mahasiswa' => $pengabdianDenganMahasiswa,
            'current_year' => $filterYear === 'all' ? $currentYear : $filterYear,
            'previous_year' => $filterYear === 'all' ? $currentYear - 1 : $filterYear - 1,
            'percentage_change_pengabdian' => $percentageChangePengabdian,
            'percentage_change_dosen' => $percentageChangeDosen,
            'percentage_change_mahasiswa' => $percentageChangeMahasiswa,
            'persentase_pengabdian_dengan_mahasiswa' => $persentasePengabdianDenganMahasiswa,
            'year_label' => $yearLabel,
            'filter_year' => $filterYear,
            'pengabdian_kolaborasi' => $pengabdianKolaborasi,
            'pengabdian_khusus_informatika' => $pengabdianKhususInformatika,
            'pengabdian_khusus_sistem_informasi' => $pengabdianKhususSistemInformasi,
            'dosen_informatika' => $dosenInformatika,
            'dosen_sistem_informasi' => $dosenSistemInformasi,
            'mahasiswa_informatika' => $mahasiswaInformatika,
            'mahasiswa_sistem_informasi' => $mahasiswaSistemInformasi,
        ];

        // 3. Tasks / actions for "Perlu Tindakan"
        // Determine the id for 'Laporan Akhir' dynamically (fallback to null if not found)
        $laporanAkhir = JenisDokumen::where('nama_jenis_dokumen', 'Laporan Akhir')->first();
        $laporanAkhirId = $laporanAkhir ? $laporanAkhir->id_jenis_dokumen : null;

        if ($laporanAkhirId) {
            $tanpaLaporanAkhir = Pengabdian::whereDoesntHave('dokumen', function ($q) use ($laporanAkhirId) {
                $q->where('id_jenis_dokumen', $laporanAkhirId);
            })->count();
        } else {
            // If jenis dokumen not present in DB, fallback to count pengabdian without any dokumen
            $tanpaLaporanAkhir = Pengabdian::whereDoesntHave('dokumen')->count();
        }

        $hkiTanpaDokumen = Luaran::whereHas('jenisLuaran', function ($q) {
            $q->where('nama_jenis_luaran', 'HKI');
        })
            ->whereDoesntHave('detailHki.dokumen')
            ->count();

        $tasks = [
            [
                'label' => "$tanpaLaporanAkhir Pengabdian belum ada Laporan Akhir",
                'count' => $tanpaLaporanAkhir,
                'url' => route('admin.pengabdian.index', ['filter' => 'no_report'])
            ],
            [
                'label' => "$hkiTanpaDokumen Luaran HKI belum ada dokumen",
                'count' => $hkiTanpaDokumen,
                'url' => route('admin.hki.index', ['filter' => 'no_doc'])
            ],
        ];

        // Additional data for charts (kept for backward compatibility with the Blade)
        // Data for document completeness chart - will be calculated after completeness logic

        // Hitung total pengabdian (sebagai ketua + anggota) untuk setiap dosen FTI dengan filter tahun
        $dosenQuery = Dosen::fti()->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('tanggal_pengabdian', $filterYear);
            }
        }]);

        $dosenCounts = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')
            ->get();

        $namaDosen = $dosenCounts->pluck('nama');
        $jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');

        // Treemap data for Luaran - only show items with count > 0 dengan filter tahun
        $luaranCounts = JenisLuaran::withCount(['luaran as luaran_count' => function ($query) use ($filterYear) {
            $query->whereHas('pengabdian', function ($q) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $q->whereYear('tanggal_pengabdian', $filterYear);
                }
            });
        }])->get();

        $dataTreemap = $luaranCounts->filter(function ($item) {
            return $item->luaran_count > 0; // Only include items with actual data
        })->map(function ($item) {
            return ['g' => $item->nama_jenis_luaran, 'v' => $item->luaran_count];
        })->values()->toArray(); // values() to reset array keys

        // Get list of document types for per-pengabdian status display
        $jenisDokumenList = JenisDokumen::orderBy('nama_jenis_dokumen')->get();

        // Determine completeness for each of the latest pengabdian based on required document names
        $requiredDocNames = [
            'Surat Tugas Dosen',
            'Surat Permohonan',
            'Surat Ucapan Terima Kasih',
            'MoU/MoA/Dokumen Kerja Sama Kegiatan',
            'Laporan Akhir',
        ];

        $requiredJenis = JenisDokumen::whereIn('nama_jenis_dokumen', $requiredDocNames)->get()->keyBy('nama_jenis_dokumen');

        $completenessMap = [];
        foreach ($latestPengabdian as $p) {
            $present = 0;
            foreach ($requiredDocNames as $name) {
                if (isset($requiredJenis[$name])) {
                    $id = $requiredJenis[$name]->id_jenis_dokumen;
                    if ($p->dokumen->contains('id_jenis_dokumen', $id)) {
                        $present++;
                    }
                }
                // If the jenisDokumen is not seeded, we treat it as missing (so completeness will be false)
            }
            $isComplete = ($present === count($requiredDocNames));
            $completenessMap[$p->id_pengabdian] = $isComplete;
        }

        // Build a list of all Pengabdian that are missing one or more required documents dengan filter tahun
        $pengabdianQuery = Pengabdian::with(['ketua', 'dokumen.jenisDokumen']);
        if ($filterYear !== 'all') {
            $pengabdianQuery->whereYear('tanggal_pengabdian', $filterYear);
        }
        $allPengabdian = $pengabdianQuery->get();
        $pengabdianNeedingDocs = [];
        foreach ($allPengabdian as $p) {
            $missing = [];
            foreach ($requiredDocNames as $name) {
                if (isset($requiredJenis[$name])) {
                    $id = $requiredJenis[$name]->id_jenis_dokumen;
                    if (! $p->dokumen->contains('id_jenis_dokumen', $id)) {
                        $missing[] = $name;
                    }
                } else {
                    // If jenisDokumen not found in DB, consider it missing
                    $missing[] = $name;
                }
            }
            if (count($missing) > 0) {
                $pengabdianNeedingDocs[] = [
                    'id' => $p->id_pengabdian,
                    'judul' => $p->judul_pengabdian,
                    'ketua' => $p->ketua->nama ?? '-',
                    'missing' => $missing,
                ];
            }
        }

        $needActionCount = count($pengabdianNeedingDocs);

        // Calculate correct document completeness based on all 5 required documents
        $totalPengabdianFiltered = $allPengabdian->count();
        $pengabdianLengkap = $totalPengabdianFiltered - $needActionCount;
        $pengabdianTidakLengkap = $needActionCount;

        // Compute counts per required document name
        $missingCounts = array_fill_keys($requiredDocNames, 0);
        foreach ($pengabdianNeedingDocs as $p) {
            foreach ($p['missing'] as $m) {
                if (isset($missingCounts[$m])) {
                    $missingCounts[$m]++;
                } else {
                    // In case a missing name isn't in requiredDocNames (unlikely), add it
                    $missingCounts[$m] = 1;
                }
            }
        }

        // Return the dashboard view with variables the Blade expects
        return view('admin.dashboard', compact(
            'latestPengabdian',
            'tasks',
            'stats',
            'totalPengabdian',
            'totalDosenTerlibat',
            'pengabdianDenganMahasiswa',
            'laporanAkhirId',
            'pengabdianLengkap',
            'pengabdianTidakLengkap',
            'tanpaLaporanAkhir',
            'hkiTanpaDokumen',
            'namaDosen',
            'jumlahPengabdianDosen',
            'dataTreemap',
            'jenisDokumenList',
            'completenessMap',
            'pengabdianNeedingDocs',
            'needActionCount',
            'missingCounts',
            'filterYear',
            'availableYears',
            'currentYear'
        ));
    }
}
