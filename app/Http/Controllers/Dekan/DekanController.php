<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\MonitoringKpi;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DekanController extends Controller
{
    /**
     * Display Dekan Dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        if (!$request->has('year')) {
            // Redirect to current year if 'year' parameter is missing
            return redirect()->route('dekan.dashboard', ['year' => '2024']);
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

        $totalDosenKeseluruhan = Dosen::count();

        // Calculate percentage change
        $percentageChangePengabdian = $totalPengabdianPrevious > 0 ?
            round((($totalPengabdianComparison - $totalPengabdianPrevious) / $totalPengabdianPrevious) * 100, 1) : ($totalPengabdianComparison > 0 ? 100 : 0);

        $percentageChangeDosen = $dosenTerlibatPrevious > 0 ?
            round((($dosenTerlibatComparison - $dosenTerlibatPrevious) / $dosenTerlibatPrevious) * 100, 1) : ($dosenTerlibatComparison > 0 ? 100 : 0);

        // Calculate percentage of pengabdian with mahasiswa
        $persentasePengabdianDenganMahasiswa = $totalPengabdian > 0 ?
            round(($pengabdianDenganMahasiswa / $totalPengabdian) * 100, 1) : 0;

        // Calculate previous year percentage of pengabdian with mahasiswa for comparison
        $pengabdianDenganMahasiswaPrevious = 0;
        $totalPengabdianPreviousForMahasiswa = 0;
        $persentasePengabdianDenganMahasiswaPrevious = 0;
        $percentageChangeMahasiswa = 0;

        if ($filterYear !== 'all') {
            // Count pengabdian with mahasiswa in previous year
            $pengabdianDenganMahasiswaPrevious = Pengabdian::whereYear('tanggal_pengabdian', $previousFilterYear)
                ->whereExists(function ($query) use ($previousFilterYear) {
                    $query->select(DB::raw(1))
                        ->from('pengabdian_mahasiswa')
                        ->whereColumn('pengabdian_mahasiswa.id_pengabdian', 'pengabdian.id_pengabdian');
                })
                ->count();

            $totalPengabdianPreviousForMahasiswa = Pengabdian::whereYear('tanggal_pengabdian', $previousFilterYear)->count();

            $persentasePengabdianDenganMahasiswaPrevious = $totalPengabdianPreviousForMahasiswa > 0 ?
                round(($pengabdianDenganMahasiswaPrevious / $totalPengabdianPreviousForMahasiswa) * 100, 1) : 0;

            // Calculate percentage change for mahasiswa involvement (based on COUNT)
            if ($pengabdianDenganMahasiswaPrevious > 0) {
                $percentageChangeMahasiswa = round((($pengabdianDenganMahasiswa - $pengabdianDenganMahasiswaPrevious) / $pengabdianDenganMahasiswaPrevious) * 100, 1);
            } else {
                $percentageChangeMahasiswa = $pengabdianDenganMahasiswa > 0 ? 100 : 0;
            }
        }

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

        // Hitung total pengabdian (sebagai ketua + anggota) untuk setiap dosen dengan filter tahun
        $dosenQuery = Dosen::withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
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
        //     // Untuk "all", tampilkan hanya dosen yang pernah memiliki pengabdian
        //     $dosenQuery->whereHas('pengabdian');
        // }

        $dosenCounts = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')
            ->get();

        // Pisahkan data nama dosen dan jumlahnya untuk digunakan di chart
        $namaDosen = $dosenCounts->pluck('nama');
        $jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');


        // KPI Radar Chart Data
        $kpiRadarData = $this->getKpiRadarData($filterYear);

        // Get data for treemap chart (jenis luaran)
        $jenisLuaranData = $this->getJenisLuaranTreemapData($filterYear);

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
            'jenisLuaranData'
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
        return view('dekan.kpi.index', compact('kpi'));
    }

    /**
     * Show the form for creating a new KPI.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('dekan.kpi.create');
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
        return view('dekan.kpi.show', compact('kpi'));
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
        return view('dekan.kpi.edit', compact('kpi'));
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

        return view('dekan.kpi.monitoring', compact('kpi', 'pengabdian', 'monitoring'));
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
     * Get KPI Radar Chart Data with enhanced normalization
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
            $realisasi = $this->calculateKpiAchievement($kpi, $filterYear);

            // Tentukan tipe KPI berdasarkan kode atau karakteristik
            $kpiType = $this->determineKpiType($kpi->kode);

            // Gunakan target dari database
            $targetValue = $kpi->target;

            // Hitung skor normalisasi berdasarkan tipe KPI
            $skorNormalisasi = $this->calculateNormalizedScore($realisasi, $targetValue, $kpiType, $kpi->kode, $filterYear);

            // Pastikan skor selalu antara 0-100
            $skorNormalisasi = max(0, min(100, $skorNormalisasi));

            $radarData[] = [
                'kode' => $kpi->kode,
                'indikator' => $kpi->indikator,
                'target' => $targetValue, // Gunakan target dari database
                'realisasi' => round($realisasi, 2),
                'skor_normalisasi' => round($skorNormalisasi, 1),
                'satuan' => $kpi->satuan,
                'tipe' => $kpiType,
                'status' => $skorNormalisasi >= 100 ? 'Tercapai' : 'Belum Tercapai',
                // Data tambahan untuk tooltip
                'detail' => $this->getKpiDetail($kpi->kode, $realisasi, $kpi->target, $filterYear)
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
            if ($a['skor_normalisasi'] != $b['skor_normalisasi']) {
                return $b['skor_normalisasi'] <=> $a['skor_normalisasi'];
            }
            return $a['kode'] <=> $b['kode'];
        });

        // Negative values: Less negative to more negative (closer to zero first)
        usort($negativeKpis, function ($a, $b) {
            if ($a['realisasi'] != $b['realisasi']) {
                return $b['realisasi'] <=> $a['realisasi']; // -10 comes before -50
            }
            if ($a['skor_normalisasi'] != $b['skor_normalisasi']) {
                return $b['skor_normalisasi'] <=> $a['skor_normalisasi'];
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
     * Determine KPI type based on code and characteristics
     * 
     * @param string $kpiCode
     * @return string
     */
    private function determineKpiType($kpiCode)
    {
        // Growth-based KPIs (persentase pertumbuhan)
        if (in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9'])) {
            return 'growth';
        }

        // Binary/Achievement KPIs (ya/tidak, tercapai/tidak) - sekarang kosong
        // IKT.I.5.i sekarang menggunakan standard dengan target 2.0
        if (in_array($kpiCode, [])) {
            return 'binary';
        }

        // Standard percentage KPIs (target dalam persen)
        if (in_array($kpiCode, ['PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j'])) {
            return 'percentage';
        }

        // Default: standard target-based (termasuk IKT.I.5.i)
        return 'standard';
    }

    /**
     * Calculate normalized score (0-100) based on KPI type
     * 
     * @param float $realisasi
     * @param float $target
     * @param string $tipe
     * @param string $kpiCode
     * @param string|int $filterYear
     * @return float
     */
    private function calculateNormalizedScore($realisasi, $target, $tipe, $kpiCode, $filterYear)
    {
        $skor = 0;

        switch ($tipe) {
            case 'standard':
                // Standard KPIs: Skor = (realisasi / target) * 100
                if ($target > 0) {
                    $skor = ($realisasi / $target) * 100;
                }
                break;

            case 'percentage':
                // Percentage KPIs: Skor = (realisasi / target) * 100
                if ($target > 0) {
                    $skor = ($realisasi / $target) * 100;
                }
                break;

            case 'growth':
                // Growth KPIs: Menggunakan batas bawah untuk menangani nilai negatif
                if ($target > 0) {
                    // Batas bawah untuk growth adalah -100% (penurunan maksimal)
                    $batasBawah = -100;

                    // Normalisasi dengan batas bawah
                    if ($realisasi >= $target) {
                        // Jika mencapai atau melebihi target = 100
                        $skor = 100;
                    } else {
                        // Linear scaling dari batas bawah ke target
                        // Range: -100% sampai target% --> 0 sampai 100 poin
                        $range = $target - $batasBawah;
                        $posisiRelatif = $realisasi - $batasBawah;
                        $skor = ($posisiRelatif / $range) * 100;
                    }
                }
                break;

            case 'binary':
                // Binary KPIs: 100 jika tercapai, 0 jika tidak
                // IKT.I.5.i sekarang menggunakan standard, jadi tidak ada di sini
                $skor = ($realisasi >= $target) ? 100 : 0;
                break;

            default:
                // Fallback ke standard
                if ($target > 0) {
                    $skor = ($realisasi / $target) * 100;
                }
                break;
        }

        // WAJIB: Batasi skor dalam rentang 0-100
        return max(0, min($skor, 100));
    }

    /**
     * Get detailed information for KPI tooltip
     * 
     * @param string $kpiCode
     * @param float $realisasi
     * @param float $target
     * @param string|int $filterYear
     * @return array
     */
    private function getKpiDetail($kpiCode, $realisasi, $target, $filterYear)
    {
        $detail = [
            'realisasi_format' => $this->formatKpiValue($realisasi, $kpiCode),
            'target_format' => $this->formatKpiValue($target, $kpiCode),
            'context' => ''
        ];

        // Add specific context based on KPI code
        switch ($kpiCode) {
            case 'PGB.I.5.6':
            case 'PGB.I.7.9':
                $detail['context'] = 'Pertumbuhan dibandingkan tahun sebelumnya';
                break;
            case 'IKT.I.5.i':
                $hkiData = $this->calculateHkiPerProdiCount($filterYear);
                $prodiTercapai = 0;
                $statusProdi = [];

                foreach ($hkiData['per_prodi'] as $prodi => $count) {
                    if ($count >= 1) {
                        $prodiTercapai++;
                        $statusProdi[] = "{$prodi}: ✓ ({$count} HKI)";
                    } else {
                        $statusProdi[] = "{$prodi}: ✗ ({$count} HKI)";
                    }
                }

                $detail['context'] = sprintf(
                    '%d dari 2 prodi tercapai. %s',
                    $prodiTercapai,
                    implode(', ', $statusProdi)
                );
                break;
        }

        return $detail;
    }

    /**
     * Format KPI value based on type
     * 
     * @param float $value
     * @param string $kpiCode
     * @return string
     */
    private function formatKpiValue($value, $kpiCode)
    {
        // Growth KPIs show as percentage
        if (in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9'])) {
            return number_format($value, 1) . '%';
        }

        // Percentage KPIs
        if (in_array($kpiCode, ['PGB.I.1.1', 'PGB.I.7.4', 'IKT.I.5.g', 'IKT.I.5.h', 'IKT.I.5.j'])) {
            return number_format($value, 1) . '%';
        }

        // HKI KPI: tampilkan sebagai prodi count
        if ($kpiCode === 'IKT.I.5.i') {
            return number_format($value, 0) . ' prodi';
        }

        // Default: integer format
        return number_format($value, 0);
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
                // Return jumlah prodi yang tercapai (0, 1, atau 2)
                $prodiTercapai = 0;
                foreach ($hkiData['per_prodi'] as $prodi => $count) {
                    if ($count >= 1) {
                        $prodiTercapai++;
                    }
                }
                return $prodiTercapai;


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
        // Helper untuk menghitung jumlah PkM yang:
        // 1. Memiliki Luaran HKI
        // 2. Melibatkan dosen dari prodi tertentu
        // 3. Sesuai filter tahun
        
        $countPkmWithHkiByProdi = function($prodiName) use ($filterYear) {
            $query = DB::table('pengabdian')
                // 1. Cek existence HKI di luaran
                ->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('luaran')
                        ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
                        ->whereColumn('luaran.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('jenis_luaran.nama_jenis_luaran', 'HKI');
                })
                // 2. Cek existence Dosen Prodi (Ketua atau Anggota)
                ->where(function($q) use ($prodiName) {
                    // Cek di tabel anggota (pengabdian_dosen)
                    $q->whereExists(function ($sub) use ($prodiName) {
                        $sub->select(DB::raw(1))
                            ->from('pengabdian_dosen')
                            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                            ->where('dosen.prodi', $prodiName);
                    });
                    
                    // Opsi tambahan: Jika ketua tidak masuk pengabdian_dosen, cek via kolom ketua_pengabdian
                    // Namun asumsi best practice adalah ketua juga masuk di pengabdian_dosen.
                    // Untuk robust-ness kita bisa tambahkan OR cek ketua jika diperlukan,
                    // tapi query exists pengabdian_dosen usually covers involvement.
                    // Jika database menyimpan ketua terpisah:
                    /*
                    $q->orWhereExists(function ($sub) use ($prodiName) {
                        $sub->select(DB::raw(1))
                            ->from('dosen')
                            ->whereColumn('dosen.nik', 'pengabdian.ketua_pengabdian')
                            ->where('dosen.prodi', $prodiName);
                    });
                    */
                });

            // 3. Filter tahun
            if ($filterYear !== 'all') {
                $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
            }

            return $query->count();
        };

        $hkiInformatika = $countPkmWithHkiByProdi('Informatika');
        $hkiSistemInformasi = $countPkmWithHkiByProdi('Sistem Informasi');

        return [
            'informatika' => $hkiInformatika,
            'sistem_informasi' => $hkiSistemInformasi,
            'total' => $hkiInformatika + $hkiSistemInformasi,
            // Status: minimal 1 PkM ber-HKI per prodi
            'informatika_tercapai' => $hkiInformatika >= 1,
            'sistem_informasi_tercapai' => $hkiSistemInformasi >= 1,
            'kedua_prodi_tercapai' => ($hkiInformatika >= 1) && ($hkiSistemInformasi >= 1),
            // Add per_prodi array (Counts of fulfilled PkM, not HKI items)
            'per_prodi' => [
                'Informatika' => $hkiInformatika,
                'Sistem Informasi' => $hkiSistemInformasi
            ]
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

    /**
     * Get funding sources data for stacked bar chart
     * Compares current year vs previous year with breakdown by source
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFundingSourcesData(Request $request)
    {
        $filterYear = $request->get('year', date('Y'));

        // Determine prodi filter for Kaprodi users (InQA sees all)
        $prodiFilter = $this->getProdiFilterForCurrentUser();

        // Handle 'all' filter - show overall comparison between two most recent years
        if ($filterYear === 'all') {
            $currentYear = date('Y');
            $previousYear = $currentYear - 1;
        } else {
            $currentYear = $filterYear;
            $previousYear = $filterYear - 1;
        }

        // Get funding data for current year
        $currentYearData = DB::table('sumber_dana')
            ->join('pengabdian', 'sumber_dana.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->whereYear('pengabdian.tanggal_pengabdian', $currentYear)
            ->when($prodiFilter, function ($q) use ($prodiFilter) {
                $q->whereExists(function ($sub) use ($prodiFilter) {
                    $sub->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', $prodiFilter);
                });
            })
            ->select('sumber_dana.nama_sumber', DB::raw('SUM(sumber_dana.jumlah_dana) as total_dana'))
            ->groupBy('sumber_dana.nama_sumber')
            ->get()
            ->keyBy('nama_sumber');

        // Get funding data for previous year
        $previousYearData = DB::table('sumber_dana')
            ->join('pengabdian', 'sumber_dana.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->whereYear('pengabdian.tanggal_pengabdian', $previousYear)
            ->when($prodiFilter, function ($q) use ($prodiFilter) {
                $q->whereExists(function ($sub) use ($prodiFilter) {
                    $sub->select(DB::raw(1))
                        ->from('pengabdian_dosen')
                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                        ->where('dosen.prodi', $prodiFilter);
                });
            })
            ->select('sumber_dana.nama_sumber', DB::raw('SUM(sumber_dana.jumlah_dana) as total_dana'))
            ->groupBy('sumber_dana.nama_sumber')
            ->get()
            ->keyBy('nama_sumber');

        // Get all unique funding sources from both years
        $allSources = $currentYearData->keys()->merge($previousYearData->keys())->unique()->values();

        // Prepare data for Chart.js stacked bar chart
        $datasets = [];
        $colors = [
            '#4e73df', // Blue for LPPM
            '#1cc88a', // Green for Fakultas
            '#36b9cc', // Cyan for Universitas
            '#f6c23e', // Yellow for Eksternal
            '#e74a3b', // Red for additional sources
            '#6f42c1', // Purple for additional sources
            '#fd7e14', // Orange for additional sources
            '#20c997'  // Teal for additional sources
        ];

        foreach ($allSources as $index => $source) {
            $currentAmount = $currentYearData->get($source)->total_dana ?? 0;
            $previousAmount = $previousYearData->get($source)->total_dana ?? 0;

            $datasets[] = [
                'label' => $source,
                'data' => [$previousAmount, $currentAmount], // [Previous Year, Current Year]
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 1
            ];
        }

        // Calculate totals for each year
        $currentYearTotal = $currentYearData->sum('total_dana');
        $previousYearTotal = $previousYearData->sum('total_dana');

        // Check if there's no data for both years
        if ($allSources->isEmpty()) {
            return response()->json([
                'labels' => [$previousYear, $currentYear],
                'datasets' => [
                    [
                        'label' => 'Tidak Ada Data',
                        'data' => [0, 0],
                        'backgroundColor' => '#e3e6f0',
                        'borderColor' => '#d1d3e2',
                        'borderWidth' => 1
                    ]
                ],
                'totals' => [
                    'previous_year' => 0,
                    'current_year' => 0
                ],
                'years' => [
                    'previous' => $previousYear,
                    'current' => $currentYear
                ],
                'no_data' => true,
                'message' => 'Tidak ada data sumber dana untuk periode ini'
            ]);
        }

        return response()->json([
            'labels' => [$previousYear, $currentYear],
            'datasets' => $datasets,
            'totals' => [
                'previous_year' => $previousYearTotal,
                'current_year' => $currentYearTotal
            ],
            'years' => [
                'previous' => $previousYear,
                'current' => $currentYear
            ],
            'no_data' => false
        ]);
    }

    /**
     * Get data for Jenis Luaran Treemap Chart
     * 
     * @param string|int $filterYear
     * @return array
     */
    private function getJenisLuaranTreemapData($filterYear)
    {
        $query = DB::table('luaran')
            ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
            ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian');

        // Filter by year if not 'all'
        if ($filterYear !== 'all') {
            $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
        }

        $jenisLuaranData = $query
            ->select(
                'jenis_luaran.nama_jenis_luaran',
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('jenis_luaran.id_jenis_luaran', 'jenis_luaran.nama_jenis_luaran')
            ->orderBy('jumlah', 'desc')
            ->get();

        // Prepare data for treemap
        $treemapData = [];
        $colors = [
            '#4e73df', // Blue
            '#1cc88a', // Green  
            '#36b9cc', // Cyan
            '#f6c23e', // Yellow
            '#e74a3b', // Red
            '#6f42c1', // Purple
            '#fd7e14', // Orange
            '#20c997', // Teal
            '#6610f2', // Indigo
            '#e83e8c', // Pink
            '#17a2b8', // Info
            '#28a745'  // Success
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
     * Display dosen recap page with pengabdian activities
     */
    public function dosenRekap(Request $request)
    {
        // Year filter logic
        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);
        $filterProdi = $request->get('prodi', 'all');

        // Get available years from pengabdian data
        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Get dosen data with pengabdian count and details
        $dosenQuery = Dosen::with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
            }
            $query->select('pengabdian.id_pengabdian', 'pengabdian.judul_pengabdian', 'pengabdian.tanggal_pengabdian')
                ->orderBy('pengabdian.tanggal_pengabdian', 'desc');
        }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                }
            }]);

        // Apply prodi filter
        if ($filterProdi !== 'all') {
            $dosenQuery->where('prodi', $filterProdi);
        }

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')
            ->paginate(20);

        // Get prodi options for filter
        $prodiOptions = Dosen::select('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        $routeBase = 'dekan';
        $userRole = auth('admin')->user()->role ?? 'Dekan';

        return view('dekan.dosen.rekap', compact(
            'dosenData',
            'filterYear',
            'filterProdi',
            'availableYears',
            'prodiOptions',
            'routeBase',
            'userRole'
        ));
    }

    /**
     * Export dosen rekap to CSV
     */
    public function exportDosenRekap(Request $request)
    {
        $filterYear = $request->get('year', date('Y'));
        $filterProdi = $request->get('prodi', 'all');

        // Get dosen data with pengabdian count and details
        $dosenQuery = Dosen::with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
            }
            $query->select('pengabdian.id_pengabdian', 'pengabdian.judul_pengabdian', 'pengabdian.tanggal_pengabdian')
                ->orderBy('pengabdian.tanggal_pengabdian', 'desc');
        }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                }
            }]);

        // Apply prodi filter
        if ($filterProdi !== 'all') {
            $dosenQuery->where('prodi', $filterProdi);
        }

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

        // Generate CSV
        $filename = 'rekap_pengabdian_dosen_' . ($filterYear !== 'all' ? $filterYear : 'semua_tahun') . '_' . date('YmdHis') . '.csv';

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
                // Get all unique pengabdian titles
                $judulTerlibat = $dosen->pengabdian->pluck('judul_pengabdian')->unique()->implode('; ');

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
     * Get detailed pengabdian data for a specific dosen (for modal/detail view)
     */
    public function dosenDetail(Request $request, $nik)
    {
        $filterYear = $request->get('year', date('Y'));

        $dosen = Dosen::with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('tanggal_pengabdian', $filterYear);
            }
            $query->orderBy('tanggal_pengabdian', 'desc');
        }])->findOrFail($nik);

        // If it's an AJAX request, return JSON
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
     * Get detailed statistics data for modal (API endpoint)
     */
    public function getStatisticsDetail(Request $request)
    {
        $type = $request->get('type');
        $filterYear = $request->get('year', date('Y'));

        switch ($type) {
            case 'pengabdian':
                return $this->getPengabdianDetail($filterYear);
            case 'dosen':
                return $this->getDosenDetail($filterYear);
            case 'mahasiswa':
                return $this->getMahasiswaDetail($filterYear);
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    /**
     * Get detailed pengabdian data
     */
    private function getPengabdianDetail($filterYear)
    {
        $query = Pengabdian::with(['sumberDana', 'pengabdianDosen.dosen', 'mahasiswa']);

        // Apply prodi filter for Kaprodi users
        $prodiFilter = $this->getProdiFilterForCurrentUser();
        if ($prodiFilter) {
            $query->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        }

        if ($filterYear !== 'all') {
            $query->whereYear('tanggal_pengabdian', $filterYear);
        }

        $pengabdianList = $query->orderBy('tanggal_pengabdian', 'desc')->get();

        $details = $pengabdianList->map(function ($pengabdian) {
            // Get ketua (first dosen with status_anggota = 'Ketua' or first dosen)
            $ketua = $pengabdian->pengabdianDosen->where('status_anggota', 'Ketua')->first();
            if (!$ketua) {
                $ketua = $pengabdian->pengabdianDosen->first();
            }

            // Determine category based on prodi of involved dosen
            $prodiList = $pengabdian->pengabdianDosen->pluck('dosen.prodi')->unique();
            $hasInformatika = $prodiList->contains('Informatika');
            $hasSistemInformasi = $prodiList->contains('Sistem Informasi');

            if ($hasInformatika && $hasSistemInformasi) {
                $kategoriProdi = 'Kolaborasi TI & SI';
            } elseif ($hasInformatika) {
                $kategoriProdi = 'Informatika';
            } elseif ($hasSistemInformasi) {
                $kategoriProdi = 'Sistem Informasi';
            } else {
                $kategoriProdi = 'Lainnya';
            }

            return [
                'id_pengabdian' => $pengabdian->id_pengabdian,
                'judul_pengabdian' => $pengabdian->judul_pengabdian,
                'tanggal_pengabdian' => $pengabdian->tanggal_pengabdian,
                'ketua' => $ketua ? $ketua->dosen->nama : 'N/A',
                'sumber_dana' => $pengabdian->sumberDana->first()->nama_sumber ?? 'N/A',
                'kategori_prodi' => $kategoriProdi,
                'dengan_mahasiswa' => $pengabdian->mahasiswa && $pengabdian->mahasiswa->count() > 0,
                'mahasiswa_list' => $pengabdian->mahasiswa->map(function ($m) {
                    return [
                        'nim' => $m->nim,
                        'nama' => $m->nama,
                        'prodi' => $m->prodi ?? null,
                    ];
                })->values()
            ];
        });

        // Calculate summary statistics
        $total = $details->count();
        $kolaborasi = $details->where('kategori_prodi', 'Kolaborasi TI & SI')->count();
        $informatika = $details->where('kategori_prodi', 'Informatika')->count();
        $sistemInformasi = $details->where('kategori_prodi', 'Sistem Informasi')->count();

        return response()->json([
            'total' => $total,
            'kolaborasi' => $kolaborasi,
            'informatika' => $informatika,
            'sistem_informasi' => $sistemInformasi,
            'details' => $details->values()
        ]);
    }

    /**
     * Get detailed dosen data
     */
    private function getDosenDetail($filterYear)
    {
        $prodiFilter = $this->getProdiFilterForCurrentUser();

        $query = Dosen::when($prodiFilter, function ($q) use ($prodiFilter) {
            $q->where('prodi', $prodiFilter);
        })
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('tanggal_pengabdian', $filterYear);
                }
            }]);

        // Only include dosen who have pengabdian activities in the specified year
        $query->whereHas('pengabdian', function ($q) use ($filterYear) {
            if ($filterYear !== 'all') {
                $q->whereYear('tanggal_pengabdian', $filterYear);
            }
        });

        $dosenList = $query->orderBy('jumlah_pengabdian', 'desc')->get();

        $details = $dosenList->map(function ($dosen) {
            return [
                'nik' => $dosen->nik,
                'nama' => $dosen->nama,
                'nidn' => $dosen->nidn,
                'prodi' => $dosen->prodi,
                'jabatan' => $dosen->jabatan,
                'email' => $dosen->email,
                'jumlah_pengabdian' => $dosen->jumlah_pengabdian
            ];
        });

        // Calculate summary statistics
        $total = $details->count();
        $informatika = $details->where('prodi', 'Informatika')->count();
        $sistemInformasi = $details->where('prodi', 'Sistem Informasi')->count();

        return response()->json([
            'total' => $total,
            'informatika' => $informatika,
            'sistem_informasi' => $sistemInformasi,
            'details' => $details->values()
        ]);
    }

    /**
     * Get detailed mahasiswa pengabdian data
     */
    private function getMahasiswaDetail($filterYear)
    {
        // Use pivot table pengabdian_mahasiswa via relation `mahasiswa`
        $query = Pengabdian::with(['sumberDana', 'pengabdianDosen.dosen', 'mahasiswa'])
            ->whereHas('mahasiswa');

        $prodiFilter = $this->getProdiFilterForCurrentUser();
        if ($prodiFilter) {
            $query->whereExists(function ($sub) use ($prodiFilter) {
                $sub->select(DB::raw(1))
                    ->from('pengabdian_dosen')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                    ->where('dosen.prodi', $prodiFilter);
            });
        }

        if ($filterYear !== 'all') {
            $query->whereYear('tanggal_pengabdian', $filterYear);
        }

        $pengabdianList = $query->orderBy('tanggal_pengabdian', 'desc')->get();

        $details = $pengabdianList->map(function ($pengabdian) {
            // Get ketua
            $ketua = $pengabdian->pengabdianDosen->where('status_anggota', 'Ketua')->first();
            if (!$ketua) {
                $ketua = $pengabdian->pengabdianDosen->first();
            }

            // Count mahasiswa by prodi
            $mahasiswaInformatika = $pengabdian->mahasiswa->where('prodi', 'Informatika')->count();
            $mahasiswaSistemInformasi = $pengabdian->mahasiswa->where('prodi', 'Sistem Informasi')->count();

            return [
                'id_pengabdian' => $pengabdian->id_pengabdian,
                'judul_pengabdian' => $pengabdian->judul_pengabdian,
                'tanggal_pengabdian' => $pengabdian->tanggal_pengabdian,
                'ketua' => $ketua ? $ketua->dosen->nama : 'N/A',
                // sumberDana is hasMany; take first available name for display
                'sumber_dana' => optional($pengabdian->sumberDana->first())->nama_sumber ?? 'N/A',
                'jumlah_mahasiswa' => $pengabdian->mahasiswa->count(),
                'mahasiswa_informatika' => $mahasiswaInformatika,
                'mahasiswa_sistem_informasi' => $mahasiswaSistemInformasi,
                // Provide explicit mahasiswa list for UI (nim, nama, prodi)
                'mahasiswa_list' => $pengabdian->mahasiswa->map(function ($m) {
                    return [
                        'nim' => $m->nim,
                        'nama' => $m->nama,
                        'prodi' => $m->prodi ?? null,
                    ];
                })->values(),
            ];
        });

        // Calculate summary statistics
        $total = $details->count();
        $totalMahasiswaInformatika = $details->sum('mahasiswa_informatika');
        $totalMahasiswaSistemInformasi = $details->sum('mahasiswa_sistem_informasi');

        return response()->json([
            'total' => $total,
            'informatika' => $totalMahasiswaInformatika,
            'sistem_informasi' => $totalMahasiswaSistemInformasi,
            'details' => $details->values()
        ]);
    }

    /**
     * Get sparkline data showing yearly trends from earliest to current year
     */
    public function getSparklineData(Request $request)
    {
        try {
            // Get all available years from pengabdian data
            $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
                ->distinct()
                ->orderBy('year', 'asc')
                ->pluck('year')
                ->toArray();

            // If no data, create a range from current year - 6 to current year
            if (empty($availableYears)) {
                $currentYear = date('Y');
                $availableYears = range($currentYear - 6, $currentYear);
            } else {
                // Ensure we have at least 7 years of data for better trend visualization
                $currentYear = date('Y');
                $minYear = min($availableYears);
                $maxYear = max($availableYears);

                // Extend range if needed to show trends better
                if ($maxYear - $minYear < 6) {
                    $startYear = max($minYear, $currentYear - 6); // Show at least last 7 years
                    $endYear = $currentYear;
                    $yearRange = range($startYear, $endYear);
                } else {
                    $yearRange = $availableYears;
                }
            }

            $sparklineData = [];

            // Determine prodi filter for Kaprodi users
            $prodiFilter = $this->getProdiFilterForCurrentUser();

            foreach ($availableYears as $year) {
                // Total pengabdian per year
                $pengabdianCount = Pengabdian::whereYear('tanggal_pengabdian', $year)
                    ->when($prodiFilter, function ($q) use ($prodiFilter) {
                        $q->whereExists(function ($sub) use ($prodiFilter) {
                            $sub->select(DB::raw(1))
                                ->from('pengabdian_dosen')
                                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                                ->where('dosen.prodi', $prodiFilter);
                        });
                    })
                    ->count();

                // Unique dosen per year
                $dosenCount = DB::table('pengabdian_dosen')
                    ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                    ->whereYear('pengabdian.tanggal_pengabdian', $year)
                    ->when($prodiFilter, function ($q) use ($prodiFilter) {
                        $q->where('dosen.prodi', $prodiFilter);
                    })
                    ->distinct('pengabdian_dosen.nik')
                    ->count('pengabdian_dosen.nik');

                // Percentage of pengabdian with mahasiswa per year
                $totalPengabdianYear = Pengabdian::whereYear('tanggal_pengabdian', $year)
                    ->when($prodiFilter, function ($q) use ($prodiFilter) {
                        $q->whereExists(function ($sub) use ($prodiFilter) {
                            $sub->select(DB::raw(1))
                                ->from('pengabdian_dosen')
                                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                                ->where('dosen.prodi', $prodiFilter);
                        });
                    })
                    ->count();

                $pengabdianWithMahasiswaYear = Pengabdian::whereYear('tanggal_pengabdian', $year)
                    ->when($prodiFilter, function ($q) use ($prodiFilter) {
                        $q->whereExists(function ($sub) use ($prodiFilter) {
                            $sub->select(DB::raw(1))
                                ->from('pengabdian_dosen')
                                ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                                ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
                                ->where('dosen.prodi', $prodiFilter);
                        });
                    })
                    ->whereHas('mahasiswa')
                    ->count();

                $mahasiswaPercentage = $totalPengabdianYear > 0
                    ? round(($pengabdianWithMahasiswaYear / $totalPengabdianYear) * 100, 1)
                    : 0;

                $sparklineData[] = [
                    'year' => $year,
                    'pengabdian' => $pengabdianCount,
                    'dosen' => $dosenCount,
                    'mahasiswa' => $mahasiswaPercentage
                ];
            }

            // Extract only the values for each metric
            $pengabdianData = array_column($sparklineData, 'pengabdian');
            $dosenData = array_column($sparklineData, 'dosen');
            $mahasiswaData = array_column($sparklineData, 'mahasiswa');
            $years = array_column($sparklineData, 'year');

            return response()->json([
                'success' => true,
                'pengabdian' => $pengabdianData,
                'dosen' => $dosenData,
                'mahasiswa' => $mahasiswaData,
                'years' => $years,
                'period' => 'yearly',
                'count' => count($sparklineData)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSparklineData: ' . $e->getMessage());

            // Return dummy data if there's an error (show last 5 years)
            $currentYear = date('Y');
            $dummyYears = range($currentYear - 4, $currentYear);
            $dummyData = array_fill(0, 5, 0);

            return response()->json([
                'success' => false,
                'pengabdian' => $dummyData,
                'dosen' => $dummyData,
                'mahasiswa' => $dummyData,
                'years' => $dummyYears,
                'period' => 'yearly',
                'count' => 5,
                'message' => 'Error loading sparkline data'
            ]);
        }
    }

    /**
     * Determine prodi filter for current authenticated admin user (Kaprodi TI/SI)
     * Returns 'Informatika' | 'Sistem Informasi' | null
     */
    private function getProdiFilterForCurrentUser(): ?string
    {
        try {
            $user = Auth::guard('admin')->user();
            if (!$user) return null;
            if ($user->role === 'Kaprodi TI') return 'Informatika';
            if ($user->role === 'Kaprodi SI') return 'Sistem Informasi';
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
