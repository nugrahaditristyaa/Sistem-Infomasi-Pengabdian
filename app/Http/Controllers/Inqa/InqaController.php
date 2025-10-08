<?php

namespace App\Http\Controllers\InQA;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\MonitoringKpi;
use App\Models\Pengabdian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InQaController extends Controller
{
    /**
     * Display InQA Dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        // Year filter logic (same as admin)
        $currentYear = date('Y');
        $totalDosenTerlibat = DB::table('pengabdian_dosen')->distinct('nik')->count('nik');
        $filterYear = $request->get('year', $currentYear);

        // Basic KPI statistics
        $totalKpi = Kpi::count();
        $totalMonitoring = MonitoringKpi::count();

        // Pengabdian statistics with year filtering
        if ($filterYear === 'all') {
            $previousYear = $currentYear - 1;
            $totalPengabdian = Pengabdian::count();
            $pengabdianDenganMahasiswa = Pengabdian::whereHas('mahasiswa')->count();

            // Comparison with previous year
            $totalPengabdianComparison = Pengabdian::whereYear('tanggal_pengabdian', $currentYear)->count();
            $totalPengabdianPrevious = Pengabdian::whereYear('tanggal_pengabdian', $currentYear - 1)->count();
            $yearLabel = "vs " . ($currentYear - 1);

            // Pengabdian Kolaborasi: memiliki minimal 1 dosen dari Informatika DAN minimal 1 dosen dari Sistem Informasi (all years)
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

            // Pengabdian Khusus Informatika: hanya memiliki dosen dari prodi Informatika (all years)
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

            // Pengabdian Khusus Sistem Informasi: hanya memiliki dosen dari prodi Sistem Informasi (all years)
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

            // Count unique dosen per prodi (all years)
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

            // Count unique mahasiswa per prodi (all years)
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

            $dosenTerlibatComparison = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->whereYear('pengabdian.tanggal_pengabdian', $currentYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->whereYear('pengabdian.tanggal_pengabdian', $previousYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        } else {
            $totalPengabdian = Pengabdian::whereYear('tanggal_pengabdian', $filterYear)->count();
            $pengabdianDenganMahasiswa = Pengabdian::whereYear('tanggal_pengabdian', $filterYear)
                ->whereHas('mahasiswa')->count();

            // Comparison with previous year from filtered year
            $previousFilterYear = $filterYear - 1;

            // Pengabdian Kolaborasi: memiliki minimal 1 dosen dari Informatika DAN minimal 1 dosen dari Sistem Informasi (filtered year)
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

            // Pengabdian Khusus Informatika: hanya memiliki dosen dari prodi Informatika (filtered year)
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

            // Pengabdian Khusus Sistem Informasi: hanya memiliki dosen dari prodi Sistem Informasi (filtered year)
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

            $dosenTerlibatComparison = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $dosenTerlibatPrevious = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->whereYear('pengabdian.tanggal_pengabdian', $previousFilterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');

            $totalPengabdianComparison = $totalPengabdian;
            $totalPengabdianPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousFilterYear)->count();
            $yearLabel = "vs $previousFilterYear";
        }

        // Calculate percentage change
        $percentageChangePengabdian = $totalPengabdianPrevious > 0 ?
            round((($totalPengabdianComparison - $totalPengabdianPrevious) / $totalPengabdianPrevious) * 100, 1) : ($totalPengabdianComparison > 0 ? 100 : 0);

        $percentageChangeDosen = $dosenTerlibatPrevious > 0 ?
            round((($dosenTerlibatComparison - $dosenTerlibatPrevious) / $dosenTerlibatPrevious) * 100, 1) : ($dosenTerlibatComparison > 0 ? 100 : 0);

        // Calculate percentage of pengabdian with mahasiswa
        $persentasePengabdianDenganMahasiswa = $totalPengabdian > 0 ?
            round(($pengabdianDenganMahasiswa / $totalPengabdian) * 100, 1) : 0;

        // Calculate average achievement for KPI
        $avgAchievement = MonitoringKpi::whereNotNull('nilai_capai')
            ->whereHas('kpi', function ($query) {
                $query->where('target', '>', 0);
            })
            ->get()
            ->map(function ($monitoring) {
                return ($monitoring->nilai_capai / $monitoring->kpi->target) * 100;
            })
            ->average() ?? 0;

        // This month monitoring
        $thisMonthMonitoring = MonitoringKpi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Recent monitoring (last 5)
        $recentMonitoring = MonitoringKpi::with(['kpi', 'pengabdian'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Available years for filter dropdown
        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Stats array similar to admin dashboard
        $stats = [
            'total_pengabdian' => $totalPengabdian,
            'total_dosen' => $totalDosenTerlibat,
            'total_mahasiswa' => $pengabdianDenganMahasiswa,
            'percentage_change_pengabdian' => $percentageChangePengabdian,
            'persentase_pengabdian_dengan_mahasiswa' => $persentasePengabdianDenganMahasiswa,
            'percentage_change_dosen' => $percentageChangeDosen,
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

        // KPI Radar Chart Data
        $kpiRadarData = $this->getKpiRadarData($filterYear);

        return view('inqa.dashboard', compact(
            'totalKpi',
            'totalMonitoring',
            'avgAchievement',
            'thisMonthMonitoring',
            'recentMonitoring',
            'stats',
            'filterYear',
            'availableYears',
            'kpiRadarData'
        ));
    }

    /**
     * Display a listing of the KPI.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $kpi = Kpi::orderBy('kode')->paginate(10);
        return view('inqa.kpi.index', compact('kpi'));
    }

    /**
     * Show the form for creating a new KPI.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('inqa.kpi.create');
    }

    /**
     * Store a newly created KPI in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:kpi,kode|max:50',
            'indikator' => 'required|string|max:255',
            'target' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        try {
            Kpi::create($request->all());
            return redirect()->route('inqa.kpi.index')
                ->with('success', 'Data KPI berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified KPI.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $kpi = Kpi::with(['monitoringKpi.pengabdian'])->findOrFail($id);
        return view('inqa.kpi.show', compact('kpi'));
    }

    /**
     * Show the form for editing the specified KPI.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $kpi = Kpi::findOrFail($id);
        return view('inqa.kpi.edit', compact('kpi'));
    }

    /**
     * Update the specified KPI in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $kpi = Kpi::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:kpi,kode,' . $kpi->id . ',id|max:50',
            'nama_indikator' => 'required|string|max:255',
            'target' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        try {
            $kpi->update($request->all());
            return redirect()->route('inqa.kpi.index')
                ->with('success', 'Data KPI berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified KPI from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kpi = Kpi::findOrFail($id);
            $kpi->delete();
            return redirect()->route('inqa.kpi.index')
                ->with('success', 'Data KPI berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the KPI monitoring page.
     *
     * @return \Illuminate\View\View
     */
    public function monitoring()
    {
        $kpi = Kpi::orderBy('kode')->get();
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $monitoring = MonitoringKpi::with(['kpi', 'pengabdian'])
            ->orderBy('tahun', 'desc')
            ->paginate(15);

        return view('inqa.kpi.monitoring', compact('kpi', 'pengabdian', 'monitoring'));
    }

    /**
     * Store monitoring KPI data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMonitoring(Request $request)
    {
        $request->validate([
            'id_kpi' => 'required|exists:kpi,id',
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'nilai_capai' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        try {
            MonitoringKpi::create($request->all());
            return redirect()->route('inqa.kpi.monitoring')
                ->with('success', 'Data monitoring KPI berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get KPI Radar Chart Data
     *
     * @param string|int $filterYear
     * @return array
     */
    private function getKpiRadarData($filterYear)
    {
        // Ambil semua KPI
        $kpis = Kpi::orderBy('kode')->get();

        $radarData = [];

        foreach ($kpis as $kpi) {
            // Hitung capaian berdasarkan data pengabdian
            $capaian = $this->calculateKpiAchievement($kpi, $filterYear);

            // Hitung persentase capaian (capaian/target * 100)
            $persentaseCapaian = $kpi->target > 0 ? ($capaian / $kpi->target * 100) : 0;

            // Batasi maksimal 100%
            $persentaseCapaian = min($persentaseCapaian, 100);

            $radarData[] = [
                'kode' => $kpi->kode,
                'indikator' => $kpi->indikator,
                'target' => $kpi->target,
                'realisasi' => $capaian,
                'persentase' => round($persentaseCapaian, 1),
                'satuan' => $kpi->satuan,
                'status' => $persentaseCapaian >= 100 ? 'Tercapai' : 'Belum Tercapai'
            ];
        }

        return $radarData;
    }

    /**
     * Calculate KPI Achievement based on Pengabdian data
     *
     * @param Kpi $kpi
     * @param string|int $filterYear
     * @return float
     */
    private function calculateKpiAchievement($kpi, $filterYear)
    {
        $baseQuery = Pengabdian::query();

        // Filter by year if not 'all'
        if ($filterYear !== 'all') {
            $baseQuery->whereYear('tanggal_pengabdian', $filterYear);
        }

        // Simulasi perhitungan capaian berdasarkan kode KPI
        // Ini bisa disesuaikan dengan logika bisnis yang sesungguhnya
        switch ($kpi->kode) {
            case 'KPI001': // Jumlah Pengabdian
                return $baseQuery->count();

            case 'KPI002': // Jumlah Dosen Terlibat
                return DB::table('pengabdian_dosen')
                    ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                    })
                    ->distinct('pengabdian_dosen.nik')
                    ->count();

            case 'KPI003': // Jumlah Mahasiswa Terlibat
                return DB::table('pengabdian_mahasiswa')
                    ->join('pengabdian', 'pengabdian_mahasiswa.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                    })
                    ->distinct('pengabdian_mahasiswa.nim')
                    ->count();

            case 'KPI004': // Jumlah Mitra
                return DB::table('pengabdian')
                    ->join('mitra', 'pengabdian.id_pengabdian', '=', 'mitra.id_pengabdian')
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                    })
                    ->count();

            case 'KPI005': // Jumlah Luaran
                return DB::table('luaran')
                    ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                    })
                    ->count();

            case 'IKT.I.5.g': // Persentase Pengabdian Pendidikan/Pelatihan
                return $this->calculateEducationalServicePercentage($filterYear);

            case 'IKT.I.5.h': // Persentase Pengabdian INFOKOM
                return $this->calculateInfokomServicePercentage($filterYear);

            default:
                // Untuk KPI lain, gunakan data monitoring jika ada
                $monitoring = MonitoringKpi::where('id_kpi', $kpi->id_kpi)
                    ->when($filterYear !== 'all', function ($query) use ($filterYear) {
                        return $query->where('tahun', $filterYear);
                    })
                    ->sum('nilai_capai');

                return $monitoring ?? 0;
        }
    }

    /**
     * Hitung persentase pengabdian yang judulnya mengandung kata kunci pendidikan/pelatihan
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateEducationalServicePercentage($filterYear)
    {
        // Kata kunci untuk pengabdian pendidikan/pelatihan
        $keywords = [
            'siswa',
            'sma',
            'pembelajaran',
            'pelatihan',
            'latihan',
            'pembekalan',
            'pendampingan',
            'sd',
            'pengenalan',
            'penulisan',
            'pemanfaatan',
            'peningkatan',
            'uji',
            'kompetisi',
            'sekolah'
        ];

        $baseQuery = Pengabdian::query();

        // Filter by year if not 'all'
        if ($filterYear !== 'all') {
            $baseQuery->whereYear('tanggal_pengabdian', $filterYear);
        }

        // Hitung total pengabdian
        $totalPengabdian = $baseQuery->count();

        if ($totalPengabdian == 0) {
            return 0;
        }

        // Hitung pengabdian yang mengandung kata kunci
        $educationalQuery = clone $baseQuery;
        $educationalQuery->where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhere('judul_pengabdian', 'LIKE', "%{$keyword}%");
            }
        });

        $educationalCount = $educationalQuery->count();

        // Hitung persentase
        $percentage = ($educationalCount / $totalPengabdian) * 100;

        return round($percentage, 2);
    }

    /**
     * Hitung persentase pengabdian yang judulnya mengandung kata kunci INFOKOM
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateInfokomServicePercentage($filterYear)
    {
        // Kata kunci untuk pengabdian INFOKOM
        $keywords = [
            'AI',
            'Algoritma',
            'Berbasis Komputer',
            'Computational Thinking',
            'Digital',
            'ICT',
            'Informatika',
            'Komputer',
            'Komputerisasi',
            'Logika',
            'Online',
            'Teknologi',
            'Teknologi Informasi',
            'TI',
            'Online Business',
            'Web',
            'Website',
            'Web Profil',
            'Web Service',
            'Webinar',
            'Wikipedia',
            'WordPress',
            'Aplikasi',
            'Aplikasi Registrasi',
            'Aplikasi SLiMS',
            'Google Apps',
            'Google Form',
            'Google Meet',
            'Google Suite',
            'Google Workspace',
            'Moodle',
            'Program Aplikasi',
            'Big Data',
            'Data Elektronik',
            'Sistem',
            'Sistem Database',
            'Sistem Informasi',
            'Sistem Informasi Administrasi',
            'Sistem Informasi Manajemen',
            'Competitive Programming',
            'Construct 3',
            'Game Development',
            'Kodular',
            'Logika Pemrograman',
            'Pemrograman Aplikasi',
            'Pemrograman C++',
            'Programming',
            'Unity',
            'Infrastruktur Teknologi Informasi',
            'Jaringan Komputer',
            'Media Sosial',
            'Multimedia',
            'Social Media',
            'Sony Vegas Pro',
            'Video',
            'Video Pembelajaran',
            'Video Tutorial',
            'Android',
            'e-Learning',
            'Internet of Things',
            'IoT',
            'Robotika',
            'Smartphone'
        ];

        $baseQuery = Pengabdian::query();

        // Filter by year if not 'all'
        if ($filterYear !== 'all') {
            $baseQuery->whereYear('tanggal_pengabdian', $filterYear);
        }

        // Hitung total pengabdian
        $totalPengabdian = $baseQuery->count();

        if ($totalPengabdian == 0) {
            return 0;
        }

        // Hitung pengabdian yang mengandung kata kunci INFOKOM
        $infokomQuery = clone $baseQuery;
        $infokomQuery->where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhere('judul_pengabdian', 'LIKE', "%{$keyword}%");
            }
        });

        $infokomCount = $infokomQuery->count();

        // Hitung persentase
        $percentage = ($infokomCount / $totalPengabdian) * 100;

        return round($percentage, 2);
    }
}
