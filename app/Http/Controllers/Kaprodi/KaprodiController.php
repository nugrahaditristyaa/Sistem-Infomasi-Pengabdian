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
            // Gunakan tahun dengan data pengabdian terbanyak sebagai default
            $baseProdiFilter = function ($query) use ($prodiFilter) {
                $query->where(function ($q) use ($prodiFilter) {
                    $q->whereExists(function ($subQuery) use ($prodiFilter) {
                        $subQuery->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', $prodiFilter);
                    })
                    ->orWhereExists(function ($subQuery) use ($prodiFilter) {
                        $subQuery->select(DB::raw(1))
                            ->from('dosen')
                            ->whereColumn('dosen.nik', 'pengabdian.ketua_pengabdian')
                            ->where('dosen.prodi', $prodiFilter);
                    });
                });
            };

            $mostRecentYear = Pengabdian::where($baseProdiFilter)
                ->selectRaw('YEAR(tanggal_pengabdian) as year, COUNT(*) as count')
                ->groupBy('year')
                ->orderBy('count', 'desc')
                ->orderBy('year', 'desc')
                ->value('year');

            $defaultYear = $mostRecentYear ?? date('Y');
            $route = $prodiFilter === 'Informatika' ? 'kaprodi.ti.dashboard' : 'kaprodi.si.dashboard';
            return redirect()->route($route, ['year' => $defaultYear]);
        }

        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);

        // Basic KPI statistics (sama untuk semua)
        $totalKpi = Kpi::count();
        $totalMonitoring = MonitoringKpi::count();

        // FILTER PENGABDIAN: Hanya yang melibatkan dosen dari prodi ini
        $baseProdiFilter = function ($query) use ($prodiFilter) {
            $query->where(function ($q) use ($prodiFilter) {
                $q->whereExists(function ($subQuery) use ($prodiFilter) {
                    $subQuery->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', $prodiFilter);
                })
                ->orWhereExists(function ($subQuery) use ($prodiFilter) {
                    $subQuery->select(DB::raw(1))
                        ->from('dosen')
                        ->whereColumn('dosen.nik', 'pengabdian.ketua_pengabdian')
                        ->where('dosen.prodi', $prodiFilter);
                });
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

            if ($pengabdianDenganMahasiswaPrevious > 0) {
                $percentageChangeMahasiswa = round((($pengabdianDenganMahasiswa - $pengabdianDenganMahasiswaPrevious) / $pengabdianDenganMahasiswaPrevious) * 100, 1);
            } else {
                $percentageChangeMahasiswa = $pengabdianDenganMahasiswa > 0 ? 100 : 0;
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

        // Filter logic removed to include all lecturers
        // if ($filterYear !== 'all') {
        //     $dosenQuery->whereHas('pengabdian', function ($query) use ($filterYear) {
        //         $query->whereYear('tanggal_pengabdian', $filterYear);
        //     });
        // } else {
        //     $dosenQuery->whereHas('pengabdian');
        // }

        $dosenCounts = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

        $namaDosen = $dosenCounts->pluck('nama');
        $jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');

        // KPI Radar Chart Data per Prodi
        $kpiRadarData = $this->getKpiRadarDataForProdi($filterYear, $prodiFilter);

        // Jenis Luaran Data (dengan filter prodi)
        $jenisLuaranData = $this->getJenisLuaranTreemapDataWithProdiFilter($filterYear, $prodiFilter);

        // Ambil judul pengabdian untuk word cloud (khusus untuk Kaprodi SI)
        $judulPengabdianSI = [];
        if ($prodiFilter === 'Sistem Informasi') {
            $judulQuery = Pengabdian::whereExists(function ($query) use ($prodiFilter) {
                $query->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });

            if ($filterYear !== 'all') {
                $judulQuery->whereYear('tanggal_pengabdian', $filterYear);
            }

            $judulPengabdianSI = $judulQuery->pluck('judul_pengabdian')->toArray();
        }

        // Gunakan view inqa.dashboard
        return view('dekan.dashboard', compact(
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
            'jenisLuaranData',
            'judulPengabdianSI',
            'prodiFilter'
        ));
    }

    /**
     * Rekap Pengabdian Dosen - Kaprodi TI
     */
    public function dosenRekapTI(Request $request)
    {
        return $this->dosenRekap($request, 'Informatika', 'kaprodi.ti');
    }

    /**
     * Rekap Pengabdian Dosen - Kaprodi SI
     */
    public function dosenRekapSI(Request $request)
    {
        return $this->dosenRekap($request, 'Sistem Informasi', 'kaprodi.si');
    }

    /**
     * Shared handler for rekap dosen for Kaprodi scopes
     */
    private function dosenRekap(Request $request, string $prodiFilter, string $routeBase)
    {
        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);

        // Available years (based on pengabdian that involve this prodi)
        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            })
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Dosen in this prodi with counts and eager-loaded pengabdian filtered by year
        $dosenQuery = Dosen::where('prodi', $prodiFilter)
            ->with(['pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
                $query->orderBy('tanggal_pengabdian', 'desc');
            }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
            }]);

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->paginate(20);

        // For Kaprodi, prodiOptions is just the single prodi, used for label if needed
        $prodiOptions = collect([$prodiFilter]);
        $filterProdi = $prodiFilter; // Set default filter to the prodi
        $userRole = auth('admin')->user()->role ?? '';

        return view('dekan.dosen.rekap', compact('dosenData', 'filterYear', 'filterProdi', 'availableYears', 'prodiOptions', 'routeBase', 'userRole'));
    }

    /**
     * Export Kaprodi rekap to CSV (TI)
     */
    public function exportDosenRekapTI(Request $request)
    {
        return $this->exportDosenRekap($request, 'Informatika');
    }

    /**
     * Export Kaprodi rekap to CSV (SI)
     */
    public function exportDosenRekapSI(Request $request)
    {
        return $this->exportDosenRekap($request, 'Sistem Informasi');
    }

    /**
     * Shared export handler for Kaprodi
     */
    private function exportDosenRekap(Request $request, string $prodiFilter)
    {
        $filterYear = $request->get('year', date('Y'));

        // Get dosen data
        $dosenQuery = Dosen::where('prodi', $prodiFilter)
            ->with(['pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
                $query->orderBy('tanggal_pengabdian', 'desc');
            }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
            }]);

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

        // Generate CSV
        $filename = 'rekap_pengabdian_dosen_' . str_replace(' ', '_', strtolower($prodiFilter)) . '_' . ($filterYear !== 'all' ? $filterYear : 'semua_tahun') . '_' . date('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($dosenData) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, ['No', 'Nama Dosen', 'NIK', 'NIDN', 'Program Studi', 'Bidang Keahlian', 'Jumlah Kegiatan', 'Judul Terlibat']);

            // Data rows
            $no = 1;
            foreach ($dosenData as $dosen) {
                $judulTerlibat = $dosen->pengabdian->pluck('judul')->unique()->implode('; ');

                fputcsv($file, [
                    $no++,
                    $dosen->nama,
                    $dosen->nik,
                    $dosen->nidn ?? '-',
                    $dosen->prodi,
                    $dosen->bidang_keahlian ?? '-',
                    $dosen->jumlah_pengabdian,
                    $judulTerlibat ?: '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dosen detail constrained to Kaprodi's prodi
     */
    public function dosenDetail(Request $request, $nik)
    {
        $role = auth('admin')->user()->role ?? '';
        $prodiFilter = $role === 'Kaprodi TI' ? 'Informatika' : 'Sistem Informasi';
        $filterYear = $request->get('year', date('Y'));

        $dosen = Dosen::where('prodi', $prodiFilter)
            ->with(['pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
                $query->orderBy('tanggal_pengabdian', 'desc');
            }])
            ->findOrFail($nik);

        if ($request->ajax()) {
            return response()->json([
                'dosen' => $dosen,
                'pengabdian' => $dosen->pengabdian->map(function ($pengabdian) {
                    return [
                        'id_pengabdian' => $pengabdian->id_pengabdian,
                        'judul' => $pengabdian->judul,
                        'tanggal_pengabdian' => $pengabdian->tanggal_pengabdian,
                        'status_anggota' => $pengabdian->pivot->status_anggota ?? 'Anggota',
                        'sumber_dana' => $pengabdian->sumberDana->nama_sumber ?? 'N/A'
                    ];
                })
            ]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
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
     * KPI Radar Data khusus prodi (TI / SI) mengikuti struktur InQaController
     */
    private function getKpiRadarDataForProdi($filterYear, $prodiFilter)
    {
        $kpis = Kpi::orderBy('kode')->get();
        $radarData = [];

        foreach ($kpis as $kpi) {
            $realisasi = $this->calculateKpiAchievementForProdi($kpi, $filterYear, $prodiFilter);
            $kpiType = $this->determineKpiTypeForProdi($kpi->kode);

            // Gunakan target dari database
            $targetValue = $kpi->target;

            $skorNormalisasi = $this->calculateNormalizedScoreForProdi($realisasi, $targetValue, $kpiType, $kpi->kode, $filterYear);
            $skorNormalisasi = max(0, min(100, $skorNormalisasi));

            $radarData[] = [
                'kode' => $kpi->kode,
                'indikator' => $kpi->indikator,
                'target' => $targetValue,
                'realisasi' => round($realisasi, 2),
                'skor_normalisasi' => round($skorNormalisasi, 1),
                'satuan' => $kpi->satuan,
                'tipe' => $kpiType,
                'status' => $skorNormalisasi >= 100 ? 'Tercapai' : 'Belum Tercapai',
                'detail' => $this->getKpiDetailForProdi($kpi->kode, $realisasi, $targetValue, $filterYear, $prodiFilter)
            ];
        }

        // Sederhanakan pengurutan: tampilkan realisasi tertinggi dulu
        usort($radarData, function ($a, $b) {
            return $b['realisasi'] <=> $a['realisasi'];
        });

        return $radarData;
    }

    private function determineKpiTypeForProdi($kpiCode)
    {
        if (in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9'])) return 'growth';
        if (in_array($kpiCode, ['PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j'])) return 'percentage';
        return 'standard';
    }

    private function calculateNormalizedScoreForProdi($realisasi, $target, $tipe, $kpiCode, $filterYear)
    {
        $skor = 0;
        switch ($tipe) {
            case 'standard':
            case 'percentage':
                if ($target > 0) $skor = ($realisasi / $target) * 100;
                break;
            case 'growth':
                if ($target > 0) {
                    $batasBawah = -100;
                    if ($realisasi >= $target) {
                        $skor = 100;
                    } else {
                        $range = $target - $batasBawah;
                        $posisiRelatif = $realisasi - $batasBawah;
                        $skor = ($posisiRelatif / $range) * 100;
                    }
                }
                break;
            default:
                if ($target > 0) $skor = ($realisasi / $target) * 100;
        }

        return max(0, min(100, $skor));
    }

    /**
     * Hitung capaian KPI berdasarkan pengabdian yang melibatkan dosen prodi tertentu
     */
    private function calculateKpiAchievementForProdi($kpi, $filterYear, $prodiFilter)
    {
        $pkmFilter = function ($q) use ($prodiFilter) {
            $q->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        };

        switch ($kpi->kode) {
            case 'KPI001': // Jumlah Pengabdian
                return Pengabdian::when($filterYear !== 'all', fn($q) => $q->whereYear('tanggal_pengabdian', $filterYear))
                    ->where($pkmFilter)
                    ->count();

            case 'KPI002': // Jumlah Dosen Terlibat (prodi ini)
                return DB::table('pengabdian_dosen')
                    ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->when($filterYear !== 'all', fn($q) => $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear))
                    ->where('dosen.prodi', $prodiFilter)
                    ->distinct('pengabdian_dosen.nik')
                    ->count('pengabdian_dosen.nik');

            case 'KPI003': // Jumlah Mahasiswa Terlibat (di pengabdian prodi ini)
                return DB::table('pengabdian_mahasiswa')
                    ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->when($filterYear !== 'all', fn($q) => $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear))
                    ->whereExists(function ($sub) use ($prodiFilter) {
                        $sub->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', $prodiFilter);
                    })
                    ->distinct('pengabdian_mahasiswa.nim')
                    ->count('pengabdian_mahasiswa.nim');

            case 'KPI004': // Jumlah Mitra
                return DB::table('pengabdian')
                    ->join('mitra', 'pengabdian.id_pengabdian', '=', 'mitra.id_pengabdian')
                    ->when($filterYear !== 'all', fn($q) => $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear))
                    ->where($pkmFilter)
                    ->count();

            case 'KPI005': // Jumlah Luaran
                return DB::table('luaran')
                    ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->when($filterYear !== 'all', fn($q) => $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear))
                    ->where($pkmFilter)
                    ->count();

            case 'IKT.I.5.g': // Persentase PkM Pendidikan/Pelatihan
                return $this->calculateKeywordPercentageForProdi(['siswa', 'sma', 'pembelajaran', 'pelatihan', 'latihan', 'pendampingan', 'sd', 'pengenalan', 'penulisan', 'pemanfaatan', 'peningkatan', 'uji', 'kompetisi', 'sekolah'], $filterYear, $prodiFilter);

            case 'IKT.I.5.h': // Persentase PkM INFOKOM
                return $this->calculateKeywordPercentageForProdi(['AI', 'Algoritma', 'Digital', 'ICT', 'Informatika', 'Komputer', 'Teknologi', 'TI', 'Web', 'Website', 'Aplikasi', 'Big Data', 'Sistem', 'Sistem Informasi', 'Program', 'Pemrograman', 'Internet of Things', 'IoT', 'Robotika', 'Android'], $filterYear, $prodiFilter);

            case 'IKT.I.5.j': // Persentase PkM dengan Mahasiswa
                return $this->calculateStudentInvolvementPercentageForProdi($filterYear, $prodiFilter);

            case 'PGB.I.7.4': // Persentase PkM dana eksternal
                return $this->calculateExternalFundingPercentageForProdi($filterYear, $prodiFilter);

            case 'PGB.I.7.9': // Pertumbuhan 3 tahun
                return $this->calculateThreeYearGrowthPercentageForProdi($filterYear, $prodiFilter);

            case 'PGB.I.5.6': // Pertumbuhan tahunan
                return $this->calculateAnnualGrowthPercentageForProdi($filterYear, $prodiFilter);

            case 'PGB.I.1.1': // Persentase Realisasi Luaran Pengabdian
                return $this->calculateOutputRealizationPercentageForProdi($filterYear, $prodiFilter);

            case 'IKT.I.5.i': // Minimum prodi punya 1 HKI PkM
                return $this->calculateHkiCountForProdi($filterYear, $prodiFilter) >= 1 ? 1 : 0;

            default:
                // Gunakan monitoring jika ada (tanpa prodi spesifik)
                $monitoring = MonitoringKpi::where('id_kpi', $kpi->id_kpi)
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->where('tahun', $filterYear);
                    })
                    ->sum('nilai_capai');
                return $monitoring ?? 0;
        }
    }

    private function calculateKeywordPercentageForProdi(array $keywords, $filterYear, $prodiFilter)
    {
        $base = Pengabdian::query()
            ->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });

        if ($filterYear !== 'all') $base->whereYear('tanggal_pengabdian', $filterYear);
        $total = $base->count();
        if ($total == 0) return 0.0;

        $match = (clone $base)->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) $q->orWhere('judul_pengabdian', 'LIKE', "%{$kw}%");
        })->count();

        return round(($match / $total) * 100, 2);
    }

    private function calculateStudentInvolvementPercentageForProdi($filterYear, $prodiFilter)
    {
        $base = Pengabdian::query()
            ->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        if ($filterYear !== 'all') $base->whereYear('tanggal_pengabdian', $filterYear);
        $total = $base->count();
        if ($total == 0) return 0.0;
        $withMhs = (clone $base)->whereHas('mahasiswa')->count();
        return round(($withMhs / $total) * 100, 2);
    }

    private function calculateExternalFundingPercentageForProdi($filterYear, $prodiFilter)
    {
        $base = Pengabdian::query()
            ->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        if ($filterYear !== 'all') $base->whereYear('tanggal_pengabdian', $filterYear);
        $total = $base->count();
        if ($total == 0) return 0.0;
        $ext = (clone $base)->whereHas('sumberDana', function ($q) {
            $q->where('jenis', 'Eksternal');
        })->count();
        return round(($ext / $total) * 100, 2);
    }

    private function calculateThreeYearGrowthPercentageForProdi($filterYear, $prodiFilter)
    {
        $yearN = ($filterYear !== 'all') ? (int)$filterYear : (int)date('Y');
        $yearN3 = $yearN - 3;
        $countN = Pengabdian::whereYear('tanggal_pengabdian', $yearN)->whereExists(function ($sub) use ($prodiFilter) {
            $sub->select(DB::raw(1))
                ->from('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                ->where('dosen.prodi', $prodiFilter);
        })->count();
        $countN3 = Pengabdian::whereYear('tanggal_pengabdian', $yearN3)->whereExists(function ($sub) use ($prodiFilter) {
            $sub->select(DB::raw(1))
                ->from('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                ->where('dosen.prodi', $prodiFilter);
        })->count();
        if ($countN3 == 0) return $countN > 0 ? 100.0 : 0.0;
        return round((($countN - $countN3) / $countN3) * 100, 2);
    }

    private function calculateAnnualGrowthPercentageForProdi($filterYear, $prodiFilter)
    {
        $yearN = ($filterYear !== 'all') ? (int)$filterYear : (int)date('Y');
        $yearN1 = $yearN - 1;
        $n = Pengabdian::whereYear('tanggal_pengabdian', $yearN)->whereExists(function ($sub) use ($prodiFilter) {
            $sub->select(DB::raw(1))
                ->from('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                ->where('dosen.prodi', $prodiFilter);
        })->count();
        $n1 = Pengabdian::whereYear('tanggal_pengabdian', $yearN1)->whereExists(function ($sub) use ($prodiFilter) {
            $sub->select(DB::raw(1))
                ->from('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                ->where('dosen.prodi', $prodiFilter);
        })->count();
        if ($n1 == 0) return $n > 0 ? 100.0 : 0.0;
        return round((($n - $n1) / $n1) * 100, 2);
    }

    private function calculateOutputRealizationPercentageForProdi($filterYear, $prodiFilter)
    {
        $query = Pengabdian::query()->whereExists(function ($sub) use ($prodiFilter) {
            $sub->select(DB::raw(1))
                ->from('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                ->where('dosen.prodi', $prodiFilter);
        });
        if ($filterYear !== 'all') $query->whereYear('tanggal_pengabdian', $filterYear);

        $pengabdianData = $query->select('id_pengabdian', 'jumlah_luaran_direncanakan')->get();
        if ($pengabdianData->isEmpty()) return 0.0;

        $totalPkm = 0;
        $pkmMemenuhi = 0;
        foreach ($pengabdianData as $pengabdian) {
            $totalPkm++;
            $luaranDirencanakan = $pengabdian->jumlah_luaran_direncanakan;
            if (is_string($luaranDirencanakan)) {
                $luaranArray = json_decode($luaranDirencanakan, true);
            } else {
                $luaranArray = $luaranDirencanakan;
            }
            $nDirencanakan = is_array($luaranArray) ? count($luaranArray) : 0;
            $nTerealisasi = DB::table('luaran')->where('id_pengabdian', $pengabdian->id_pengabdian)->count();
            if ($nTerealisasi >= $nDirencanakan) $pkmMemenuhi++;
        }
        return round(($pkmMemenuhi / max(1, $totalPkm)) * 100, 2);
    }

    private function calculateHkiCountForProdi($filterYear, $prodiFilter)
    {
        $base = DB::table('luaran')
            ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
            ->where('jenis_luaran.nama_jenis_luaran', 'HKI');
        if ($filterYear !== 'all') $base->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
        return $base
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->where('dosen.prodi', $prodiFilter)
            ->distinct('luaran.id_luaran')
            ->count('luaran.id_luaran');
    }

    private function getKpiDetailForProdi($kpiCode, $realisasi, $target, $filterYear, $prodiFilter)
    {
        // Ringkas: tampilkan format realisasi/target dan konteks prodi
        $detail = [
            'realisasi_format' => number_format($realisasi, in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9', 'PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j']) ? 1 : 0) . (in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9', 'PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j']) ? '%' : ''),
            'target_format' => number_format($target, in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9', 'PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j']) ? 1 : 0) . (in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9', 'PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j']) ? '%' : ''),
            'context' => 'Prodi: ' . $prodiFilter
        ];

        if ($kpiCode === 'IKT.I.5.i') {
            $hki = $this->calculateHkiCountForProdi($filterYear, $prodiFilter);
            $detail['context'] .= " | HKI: {$hki}";
        }

        return $detail;
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
            ->leftJoin('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->leftJoin('dosen as member', 'pengabdian_dosen.nik', '=', 'member.nik')
            ->leftJoin('dosen as ketua', 'pengabdian.ketua_pengabdian', '=', 'ketua.nik')
            ->where(function($q) use ($prodi) {
                $q->where('member.prodi', $prodi)
                  ->orWhere('ketua.prodi', $prodi);
            })
            ->select('pengabdian.*', 'ketua.nama as nama_ketua')
            ->distinct('pengabdian.id_pengabdian');

        if ($request->has('year') && $request->year != 'all') {
            $query->whereYear('pengabdian.tanggal_pengabdian', $request->year);
        }

        $pengabdian = $query->orderBy('pengabdian.tanggal_pengabdian', 'desc')
            ->paginate(20);

        $availableYears = DB::table('pengabdian')
            ->leftJoin('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->leftJoin('dosen as member', 'pengabdian_dosen.nik', '=', 'member.nik')
            ->leftJoin('dosen as ketua', 'pengabdian.ketua_pengabdian', '=', 'ketua.nik')
            ->where(function($q) use ($prodi) {
                $q->where('member.prodi', $prodi)
                  ->orWhere('ketua.prodi', $prodi);
            })
            ->selectRaw('DISTINCT YEAR(pengabdian.tanggal_pengabdian) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('kaprodi.pengabdian', compact('pengabdian', 'prodi', 'availableYears'));
    }
}
