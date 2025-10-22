<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\MonitoringKpi;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KaprodiController extends Controller
{
    /**
     * Dashboard untuk Kaprodi TI - menggunakan view InQA dengan filter prodi Informatika
     */
    public function dashboardTI(Request $request)
    {
        $prodi = 'Informatika';
        return $this->dashboard($request, $prodi);
    }

    /**
     * Dashboard untuk Kaprodi SI - menggunakan view InQA dengan filter prodi Sistem Informasi
     */
    public function dashboardSI(Request $request)
    {
        $prodi = 'Sistem Informasi';
        return $this->dashboard($request, $prodi);
    }

    /**
     * Display Dashboard dengan filter prodi
     * Menggunakan view inqa.dashboard dengan data yang difilter per prodi
     */
    private function dashboard(Request $request, $prodiFilter)
    {
        if (!$request->has('year')) {
            $route = $prodiFilter === 'Informatika' ? 'kaprodi.ti.dashboard' : 'kaprodi.si.dashboard';
            return redirect()->route($route, ['year' => date('Y')]);
        }

        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);

        // Basic KPI statistics (sama untuk semua)
        $totalKpi = Kpi::count();
        $totalMonitoring = MonitoringKpi::count();

        // FILTER PENGABDIAN: Hanya yang melibatkan dosen dari prodi ini
        $baseProdiFilter = function ($query) use ($prodiFilter) {
            $query->whereExists(function ($subQuery) use ($prodiFilter) {
                $subQuery->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        };

        // Pengabdian statistics dengan filter prodi
        if ($filterYear === 'all') {
            $previousYear = $currentYear - 1;

            // Total pengabdian yang melibatkan dosen prodi ini
            $totalPengabdian = Pengabdian::where($baseProdiFilter)->count();

            // Pengabdian dengan mahasiswa
            $pengabdianDenganMahasiswa = Pengabdian::where($baseProdiFilter)
                ->whereHas('mahasiswa')->count();

            // Comparison dengan tahun sebelumnya
            $totalPengabdianComparison = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $currentYear)->count();
            $totalPengabdianPrevious = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $currentYear - 1)->count();
            $yearLabel = "vs " . ($currentYear - 1);

            // Untuk Kaprodi, kita hitung pengabdian khusus prodi ini dan kolaborasi
            if ($prodiFilter === 'Informatika') {
                // Khusus TI: hanya dosen TI
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

                // Kolaborasi: TI + SI
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

                $pengabdianKhususSistemInformasi = 0; // Tidak relevan untuk Kaprodi TI
            } else {
                // Khusus SI: hanya dosen SI
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

                // Kolaborasi: SI + TI
                $pengabdianKolaborasi = DB::table('pengabdian')
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', 'Sistem Informasi');
                    })
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', 'Informatika');
                    })
                    ->count();

                $pengabdianKhususInformatika = 0; // Tidak relevan untuk Kaprodi SI
            }

            // Count dosen dari prodi ini yang terlibat
            $dosenInformatika = $prodiFilter === 'Informatika' ?
                DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Informatika')
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik') : 0;

            $dosenSistemInformasi = $prodiFilter === 'Sistem Informasi' ?
                DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Sistem Informasi')
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik') : 0;

            // Count mahasiswa dari prodi ini yang terlibat
            $mahasiswaInformatika = $prodiFilter === 'Informatika' ?
                DB::table('pengabdian_mahasiswa')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Informatika')
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim') : 0;

            $mahasiswaSistemInformasi = $prodiFilter === 'Sistem Informasi' ?
                DB::table('pengabdian_mahasiswa')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Sistem Informasi')
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim') : 0;

            $dosenTerlibatComparison = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->whereYear('pengabdian.tanggal_pengabdian', $currentYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->whereYear('pengabdian.tanggal_pengabdian', $previousYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        } else {
            // Filter dengan tahun tertentu
            $totalPengabdian = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $filterYear)->count();

            $pengabdianDenganMahasiswa = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $filterYear)
                ->whereHas('mahasiswa')->count();

            $previousFilterYear = $filterYear - 1;

            // Pengabdian khusus dan kolaborasi dengan filter tahun
            if ($prodiFilter === 'Informatika') {
                $pengabdianKhususInformatika = DB::table('pengabdian')
                    ->whereYear('tanggal_pengabdian', $filterYear)
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

                $pengabdianKolaborasi = DB::table('pengabdian')
                    ->whereYear('tanggal_pengabdian', $filterYear)
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

                $pengabdianKhususSistemInformasi = 0;
            } else {
                $pengabdianKhususSistemInformasi = DB::table('pengabdian')
                    ->whereYear('tanggal_pengabdian', $filterYear)
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

                $pengabdianKolaborasi = DB::table('pengabdian')
                    ->whereYear('tanggal_pengabdian', $filterYear)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', 'Sistem Informasi');
                    })
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', 'Informatika');
                    })
                    ->count();

                $pengabdianKhususInformatika = 0;
            }

            // Count dosen dan mahasiswa dengan filter tahun
            $dosenInformatika = $prodiFilter === 'Informatika' ?
                DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Informatika')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik') : 0;

            $dosenSistemInformasi = $prodiFilter === 'Sistem Informasi' ?
                DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', 'Sistem Informasi')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik') : 0;

            $mahasiswaInformatika = $prodiFilter === 'Informatika' ?
                DB::table('pengabdian_mahasiswa')
                ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Informatika')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim') : 0;

            $mahasiswaSistemInformasi = $prodiFilter === 'Sistem Informasi' ?
                DB::table('pengabdian_mahasiswa')
                ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
                ->where('mahasiswa.prodi', 'Sistem Informasi')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_mahasiswa.nim')
                ->count('pengabdian_mahasiswa.nim') : 0;

            $dosenTerlibatComparison = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->whereYear('pengabdian.tanggal_pengabdian', $previousFilterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $totalPengabdianComparison = $totalPengabdian;
            $totalPengabdianPrevious = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $previousFilterYear)->count();
            $yearLabel = "vs $previousFilterYear";
        }

        // Total dosen dari prodi ini
        $totalDosenKeseluruhan = Dosen::where('prodi', $prodiFilter)->count();

        // Calculate percentage changes
        $percentageChangePengabdian = $totalPengabdianPrevious > 0 ?
            round((($totalPengabdianComparison - $totalPengabdianPrevious) / $totalPengabdianPrevious) * 100, 1) : ($totalPengabdianComparison > 0 ? 100 : 0);

        $percentageChangeDosen = $dosenTerlibatPrevious > 0 ?
            round((($dosenTerlibatComparison - $dosenTerlibatPrevious) / $dosenTerlibatPrevious) * 100, 1) : ($dosenTerlibatComparison > 0 ? 100 : 0);

        // Calculate persentase pengabdian dengan mahasiswa
        $persentasePengabdianDenganMahasiswa = $totalPengabdian > 0 ?
            round(($pengabdianDenganMahasiswa / $totalPengabdian) * 100, 1) : 0;

        // Previous year mahasiswa percentage untuk comparison
        $pengabdianDenganMahasiswaPrevious = 0;
        $totalPengabdianPreviousForMahasiswa = 0;
        $persentasePengabdianDenganMahasiswaPrevious = 0;
        $percentageChangeMahasiswa = 0;

        if ($filterYear !== 'all') {
            $pengabdianDenganMahasiswaPrevious = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $previousFilterYear)
                ->whereHas('mahasiswa')
                ->count();

            $totalPengabdianPreviousForMahasiswa = Pengabdian::where($baseProdiFilter)
                ->whereYear('tanggal_pengabdian', $previousFilterYear)->count();

            $persentasePengabdianDenganMahasiswaPrevious = $totalPengabdianPreviousForMahasiswa > 0 ?
                round(($pengabdianDenganMahasiswaPrevious / $totalPengabdianPreviousForMahasiswa) * 100, 1) : 0;

            if ($persentasePengabdianDenganMahasiswaPrevious > 0) {
                $percentageChangeMahasiswa = round((($persentasePengabdianDenganMahasiswa - $persentasePengabdianDenganMahasiswaPrevious) / $persentasePengabdianDenganMahasiswaPrevious) * 100, 1);
            } else {
                $percentageChangeMahasiswa = $persentasePengabdianDenganMahasiswa > 0 ? 100 : 0;
            }
        }

        // Total dosen terlibat dari prodi ini
        if ($filterYear === 'all') {
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        } else {
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->where('dosen.prodi', $prodiFilter)
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        }

        // Average achievement (sama seperti InQA)
        $avgAchievement = MonitoringKpi::whereNotNull('nilai_capai')
            ->whereHas('kpi', function ($query) {
                $query->where('target', '>', 0);
            })
            ->get()
            ->map(function ($monitoring) {
                return ($monitoring->nilai_capai / $monitoring->kpi->target) * 100;
            })
            ->average() ?? 0;

        $thisMonthMonitoring = MonitoringKpi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $recentMonitoring = MonitoringKpi::with(['kpi', 'pengabdian'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Available years
        $availableYears = Pengabdian::where($baseProdiFilter)
            ->selectRaw('YEAR(tanggal_pengabdian) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Stats array
        $stats = [
            'total_pengabdian' => $totalPengabdian,
            'total_dosen' => $totalDosenTerlibat,
            'total_dosen_keseluruhan' => $totalDosenKeseluruhan,
            'total_mahasiswa' => $pengabdianDenganMahasiswa,
            'percentage_change_pengabdian' => $percentageChangePengabdian,
            'persentase_pengabdian_dengan_mahasiswa' => $persentasePengabdianDenganMahasiswa,
            'percentage_change_dosen' => $percentageChangeDosen,
            'percentage_change_mahasiswa' => $percentageChangeMahasiswa,
            'year_label' => $yearLabel,
            'filter_year' => $filterYear,
            'previous_year' => $filterYear === 'all' ? $currentYear - 1 : $filterYear - 1,
            'pengabdian_kolaborasi' => $pengabdianKolaborasi,
            'pengabdian_khusus_informatika' => $pengabdianKhususInformatika,
            'pengabdian_khusus_sistem_informasi' => $pengabdianKhususSistemInformasi,
            'dosen_informatika' => $dosenInformatika,
            'dosen_sistem_informasi' => $dosenSistemInformasi,
            'mahasiswa_informatika' => $mahasiswaInformatika,
            'mahasiswa_sistem_informasi' => $mahasiswaSistemInformasi,
        ];

        // Hitung total pengabdian untuk setiap dosen DARI PRODI INI dengan filter tahun
        $dosenQuery = Dosen::where('prodi', $prodiFilter)
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
            }]);

        // Filter hanya dosen yang memiliki pengabdian di tahun yang dipilih
        if ($filterYear !== 'all') {
            $dosenQuery->whereHas('pengabdian', function ($query) use ($filterYear) {
                $query->whereYear('tanggal_pengabdian', $filterYear);
            });
        } else {
            $dosenQuery->whereHas('pengabdian');
        }

        $dosenCounts = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

        $namaDosen = $dosenCounts->pluck('nama');
        $jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');

        // KPI Radar Chart Data (kosong untuk sementara, akan diimplementasikan nanti)
        $kpiRadarData = [];

        // Jenis Luaran Data (dengan filter prodi)
        $jenisLuaranData = $this->getJenisLuaranTreemapDataWithProdiFilter($filterYear, $prodiFilter);

        // Gunakan view inqa.dashboard
        return view('inqa.dashboard', compact(
            'totalKpi',
            'totalMonitoring',
            'avgAchievement',
            'thisMonthMonitoring',
            'recentMonitoring',
            'stats',
            'filterYear',
            'availableYears',
            'kpiRadarData',
            'namaDosen',
            'jumlahPengabdianDosen',
            'jenisLuaranData'
        ));
    }

    /**
     * Get Jenis Luaran Treemap Data dengan filter prodi
     */
    private function getJenisLuaranTreemapDataWithProdiFilter($filterYear, $prodiFilter)
    {
        $query = DB::table('luaran')
            ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
            ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->where('dosen.prodi', $prodiFilter);

        if ($filterYear !== 'all') {
            $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
        }

        $jenisLuaranData = $query
            ->select(
                'jenis_luaran.nama_jenis_luaran',
                DB::raw('COUNT(DISTINCT luaran.id_luaran) as jumlah')
            )
            ->groupBy('jenis_luaran.id_jenis_luaran', 'jenis_luaran.nama_jenis_luaran')
            ->orderBy('jumlah', 'desc')
            ->get();

        $treemapData = [];
        $colors = [
            '#4e73df',
            '#1cc88a',
            '#36b9cc',
            '#f6c23e',
            '#e74a3b',
            '#6f42c1',
            '#fd7e14',
            '#20c997',
            '#6610f2',
            '#e83e8c',
            '#17a2b8',
            '#28a745'
        ];

        foreach ($jenisLuaranData as $index => $item) {
            $treemapData[] = [
                'label' => $item->nama_jenis_luaran,
                'value' => $item->jumlah,
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)]
            ];
        }

        return $treemapData;
    }

    /**
     * Daftar pengabdian per prodi
     */
    public function pengabdianList(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if ($user->role === 'Kaprodi TI') {
            $prodi = 'Informatika';
        } elseif ($user->role === 'Kaprodi SI') {
            $prodi = 'Sistem Informasi';
        } else {
            abort(403, 'Unauthorized');
        }

        $query = DB::table('pengabdian')
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->leftJoin('dosen as ketua', 'pengabdian.ketua_pengabdian', '=', 'ketua.nik')
            ->where('dosen.prodi', $prodi)
            ->select('pengabdian.*', 'ketua.nama as nama_ketua')
            ->distinct('pengabdian.id_pengabdian');

        if ($request->has('year') && $request->year != 'all') {
            $query->whereYear('pengabdian.tanggal_pengabdian', $request->year);
        }

        $pengabdian = $query->orderBy('pengabdian.tanggal_pengabdian', 'desc')
            ->paginate(20);

        $availableYears = DB::table('pengabdian')
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->where('dosen.prodi', $prodi)
            ->selectRaw('DISTINCT YEAR(pengabdian.tanggal_pengabdian) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('kaprodi.pengabdian', compact('pengabdian', 'prodi', 'availableYears'));
    }
}
