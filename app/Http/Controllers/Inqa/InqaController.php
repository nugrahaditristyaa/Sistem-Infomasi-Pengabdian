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
        if (!$request->has('year')) {
            // Redirect to current year if 'year' parameter is missing
            return redirect()->route('inqa.dashboard', ['year' => date('2024')]);
        }

        // Year filter logic (same as admin)
        $currentYear = date('Y');
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

        // Calculate total dosen terlibat (IT + SI) berdasarkan filter tahun
        if ($filterYear === 'all') {
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        } else {
            $totalDosenTerlibat = DB::table('pengabdian_dosen')
                ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                ->whereIn('dosen.prodi', ['Informatika', 'Sistem Informasi'])
                ->whereYear('pengabdian.tanggal_pengabdian', $filterYear)
                ->distinct('pengabdian_dosen.nik')
                ->count('pengabdian_dosen.nik');
        }

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

        // OPTIMIZED RADAR CHART ORDERING: Minimize visual jumps and enhance readability
        // Step 1: Separate positive and negative values for better grouping
        $positiveKpis = array_filter($radarData, function ($kpi) {
            return $kpi['realisasi'] >= 0;
        });

        $negativeKpis = array_filter($radarData, function ($kpi) {
            return $kpi['realisasi'] < 0;
        });

        // Step 2: Sort each group optimally
        // Positive values: High to low
        usort($positiveKpis, function ($a, $b) {
            if ($a['realisasi'] != $b['realisasi']) {
                return $b['realisasi'] <=> $a['realisasi'];
            }
            if ($a['persentase'] != $b['persentase']) {
                return $b['persentase'] <=> $a['persentase'];
            }
            return $a['kode'] <=> $b['kode'];
        });

        // Negative values: Less negative to more negative (closer to zero first)
        usort($negativeKpis, function ($a, $b) {
            if ($a['realisasi'] != $b['realisasi']) {
                return $b['realisasi'] <=> $a['realisasi']; // -10 comes before -50
            }
            if ($a['persentase'] != $b['persentase']) {
                return $b['persentase'] <=> $a['persentase'];
            }
            return $a['kode'] <=> $b['kode'];
        });

        // Step 3: Smart arrangement to minimize circular jumps
        $finalOrder = [];

        if (!empty($positiveKpis) && !empty($negativeKpis)) {
            // Mixed scenario: positive -> negative transition with buffer

            // Add positive KPIs
            $finalOrder = array_merge($finalOrder, $positiveKpis);

            // Add negative KPIs
            $finalOrder = array_merge($finalOrder, $negativeKpis);
        } elseif (!empty($positiveKpis)) {
            // Only positive values
            $finalOrder = $positiveKpis;
        } elseif (!empty($negativeKpis)) {
            // Only negative values
            $finalOrder = $negativeKpis;
        } else {
            // Fallback to original data
            $finalOrder = $radarData;
        }

        // Step 4: Final optimization for large datasets
        if (count($finalOrder) > 8) {
            // For very large datasets, apply tier grouping
            $tiers = [
                'excellent' => [], // >80
                'good' => [],      // 50-80
                'fair' => [],      // 20-50
                'poor' => [],      // 0-20
                'negative' => []   // <0
            ];

            foreach ($finalOrder as $kpi) {
                $value = $kpi['realisasi'];
                if ($value >= 80) {
                    $tiers['excellent'][] = $kpi;
                } elseif ($value >= 50) {
                    $tiers['good'][] = $kpi;
                } elseif ($value >= 20) {
                    $tiers['fair'][] = $kpi;
                } elseif ($value >= 0) {
                    $tiers['poor'][] = $kpi;
                } else {
                    $tiers['negative'][] = $kpi;
                }
            }

            // Rebuild array with tier ordering
            $finalOrder = array_merge(
                $tiers['excellent'],
                $tiers['good'],
                $tiers['fair'],
                $tiers['poor'],
                $tiers['negative']
            );
        }

        return $finalOrder;
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

            case 'IKT.I.5.j': // Persentase PkM yang melibatkan mahasiswa
                return $this->calculateStudentInvolvementPercentage($filterYear);

            case 'PGB.I.7.4': // Persentase PkM dengan sumber dana eksternal
                return $this->calculateExternalFundingPercentage($filterYear);

            case 'PGB.I.7.9': // Pertumbuhan PkM (3 tahun) - N vs N-3
                return $this->calculateThreeYearGrowthPercentage($filterYear);

            case 'PGB.I.5.6': // Pertumbuhan PkM (tahunan) - N vs N-1
                return $this->calculateAnnualGrowthPercentage($filterYear);

            case 'PGB.I.1.1': // Persentase Realisasi Luaran Pengabdian
                return $this->calculateOutputRealizationPercentage($filterYear);

            case 'IKT.I.5.i': // Minimum Prodi memiliki 1 HKI PkM setiap tahun
                $hkiData = $this->calculateHkiPerProdiCount($filterYear);
                // Return total HKI atau bisa return berapa prodi yang sudah tercapai
                return $hkiData['total']; // atau bisa return jumlah prodi yang tercapai


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

    /**
     * Hitung persentase PkM yang melibatkan minimal 1 mahasiswa
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateStudentInvolvementPercentage($filterYear)
    {
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

        // Hitung pengabdian yang melibatkan mahasiswa
        // Menggunakan relationship 'mahasiswa' dari model Pengabdian
        $studentInvolvementQuery = clone $baseQuery;
        $studentInvolvementQuery->whereHas('mahasiswa');

        $studentInvolvementCount = $studentInvolvementQuery->count();

        // Hitung persentase
        $percentage = ($studentInvolvementCount / $totalPengabdian) * 100;

        return round($percentage, 2);
    }

    /**
     * Hitung persentase PkM yang memiliki sumber dana eksternal
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateExternalFundingPercentage($filterYear)
    {
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

        // Hitung pengabdian yang memiliki sumber dana eksternal
        // Menggunakan relationship dengan SumberDana where jenis = 'Eksternal'
        $externalFundingQuery = clone $baseQuery;
        $externalFundingQuery->whereHas('sumberDana', function ($query) {
            $query->where('jenis', 'Eksternal');
        });

        $externalFundingCount = $externalFundingQuery->count();

        // Hitung persentase
        $percentage = ($externalFundingCount / $totalPengabdian) * 100;

        return round($percentage, 2);
    }

    /**
     * Hitung pertumbuhan PkM dalam 3 tahun (Tahun N vs Tahun N-3)
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateThreeYearGrowthPercentage($filterYear)
    {
        // Tentukan tahun N (tahun terakhir untuk perhitungan)
        $yearN = ($filterYear !== 'all') ? (int)$filterYear : (int)date('Y');

        // Tahun N-3 (tiga tahun sebelumnya)
        $yearN3 = $yearN - 3;

        // Hitung jumlah PkM di tahun N
        $pkmYearN = Pengabdian::whereYear('tanggal_pengabdian', $yearN)->count();

        // Hitung jumlah PkM di tahun N-3
        $pkmYearN3 = Pengabdian::whereYear('tanggal_pengabdian', $yearN3)->count();

        // Jika tidak ada PkM di tahun N-3, tidak bisa menghitung pertumbuhan
        if ($pkmYearN3 == 0) {
            // Jika ada PkM di tahun N tapi tidak ada di tahun N-3, anggap pertumbuhan 100%
            return $pkmYearN > 0 ? 100.00 : 0.00;
        }

        // Hitung persentase pertumbuhan
        // ((Tahun N - Tahun N-3) / Tahun N-3) * 100%
        $growthPercentage = (($pkmYearN - $pkmYearN3) / $pkmYearN3) * 100;

        return round($growthPercentage, 2);
    }

    /**
     * Hitung pertumbuhan PkM tahunan (Tahun N vs Tahun N-1)
     * 
     * @param string|int $filterYear
     * @return float
     */
    private function calculateAnnualGrowthPercentage($filterYear)
    {
        // Tentukan tahun N (tahun terakhir untuk perhitungan)
        $yearN = ($filterYear !== 'all') ? (int)$filterYear : (int)date('Y');

        // Tahun N-1 (satu tahun sebelumnya)
        $yearN1 = $yearN - 1;

        // Hitung jumlah PkM di tahun N
        $pkmYearN = Pengabdian::whereYear('tanggal_pengabdian', $yearN)->count();

        // Hitung jumlah PkM di tahun N-1
        $pkmYearN1 = Pengabdian::whereYear('tanggal_pengabdian', $yearN1)->count();

        // Jika tidak ada PkM di tahun N-1, tidak bisa menghitung pertumbuhan
        if ($pkmYearN1 == 0) {
            // Jika ada PkM di tahun N tapi tidak ada di tahun N-1, anggap pertumbuhan 100%
            return $pkmYearN > 0 ? 100.00 : 0.00;
        }

        // Hitung persentase pertumbuhan
        // ((Tahun N - Tahun N-1) / Tahun N-1) * 100%
        $growthPercentage = (($pkmYearN - $pkmYearN1) / $pkmYearN1) * 100;

        return round($growthPercentage, 2);
    }

    /**
     * Calculate KPI PGB.I.1.1: Persentase Realisasi Luaran Pengabdian
     * Target: ≥ 80%
     * 
     * Metode: Bandingkan jumlah luaran terealisasi dengan yang direncanakan
     * 
     * @param string $filterYear
     * @return float
     */
    private function calculateOutputRealizationPercentage($filterYear)
    {
        // Query pengabdian berdasarkan tahun
        $query = Pengabdian::query();

        if ($filterYear !== 'all') {
            $query->whereYear('tanggal_pengabdian', $filterYear);
        }

        $pengabdianData = $query->select('id_pengabdian', 'jumlah_luaran_direncanakan')->get();

        if ($pengabdianData->isEmpty()) {
            return 0.00;
        }

        $totalPkm = 0;
        $pkmMemenuhi = 0;

        foreach ($pengabdianData as $pengabdian) {
            $totalPkm++;

            // Hitung N_direncanakan: panjang array di jumlah_luaran_direncanakan
            $luaranDirencanakan = $pengabdian->jumlah_luaran_direncanakan;

            if (is_string($luaranDirencanakan)) {
                $luaranArray = json_decode($luaranDirencanakan, true);
            } else {
                $luaranArray = $luaranDirencanakan;
            }

            $nDirencanakan = is_array($luaranArray) ? count($luaranArray) : 0;

            // Hitung N_terealisasi: jumlah baris di tabel luaran untuk pengabdian ini
            $nTerealisasi = DB::table('luaran')
                ->where('id_pengabdian', $pengabdian->id_pengabdian)
                ->count();

            // Klasifikasi: Memenuhi jika N_terealisasi >= N_direncanakan
            if ($nTerealisasi >= $nDirencanakan) {
                $pkmMemenuhi++;
            }
        }

        // Rumus: (Jumlah PkM yang Memenuhi Luaran / Total Seluruh PkM) × 100%
        $percentage = $totalPkm > 0 ? ($pkmMemenuhi / $totalPkm) * 100 : 0;

        return round($percentage, 2);
    }

    /**
     * Calculate KPI IKT.I.5.i: Minimum Prodi memiliki 1 HKI PkM setiap tahun
     * Target: 1 buah (untuk masing-masing prodi)
     * 
     * Metode: Hitung jumlah HKI dari PkM per prodi (Informatika dan Sistem Informasi)
     * 
     * @param string $filterYear
     * @return array
     */
    private function calculateHkiPerProdiCount($filterYear)
    {
        // Query untuk mendapatkan HKI berdasarkan prodi
        $baseQuery = DB::table('luaran')
            ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
            ->where('jenis_luaran.nama_jenis_luaran', 'HKI');

        // Filter by year if not 'all'
        if ($filterYear !== 'all') {
            $baseQuery->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
        }

        // Hitung HKI untuk Informatika
        $hkiInformatika = (clone $baseQuery)
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->where('dosen.prodi', 'Informatika')
            ->distinct('luaran.id_luaran')
            ->count('luaran.id_luaran');

        // Hitung HKI untuk Sistem Informasi
        $hkiSistemInformasi = (clone $baseQuery)
            ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->where('dosen.prodi', 'Sistem Informasi')
            ->distinct('luaran.id_luaran')
            ->count('luaran.id_luaran');

        return [
            'informatika' => $hkiInformatika,
            'sistem_informasi' => $hkiSistemInformasi,
            'total' => $hkiInformatika + $hkiSistemInformasi,
            // Status: minimal 1 HKI per prodi
            'informatika_tercapai' => $hkiInformatika >= 1,
            'sistem_informasi_tercapai' => $hkiSistemInformasi >= 1,
            'kedua_prodi_tercapai' => ($hkiInformatika >= 1) && ($hkiSistemInformasi >= 1)
        ];
    }

    /**
     * Public method to get HKI per Prodi data for dashboard view
     * 
     * @param string $filterYear
     * @return array
     */
    public function getHkiPerProdiData($filterYear)
    {
        return $this->calculateHkiPerProdiCount($filterYear);
    }
}
