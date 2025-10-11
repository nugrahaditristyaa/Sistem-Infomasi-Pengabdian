@extends('inqa.layouts.main')

@section('title', 'Dashboard InQA')

@push('styles')
    <style>
        .chart-radar {
            position: relative;
            height: 350px;
            overflow: hidden;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.02) 0%, rgba(28, 200, 138, 0.02) 100%);
        }

        .kpi-legend {
            padding: 10px;
            background: rgba(248, 249, 252, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .legend-item {
            transition: all 0.3s ease;
            border-radius: 6px;
            background: #fff;
        }

        .legend-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .kpi-legend-items::-webkit-scrollbar {
            width: 4px;
        }

        .kpi-legend-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .kpi-legend-items::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .kpi-legend-items::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .tooltip-icon:hover {
            color: #4e73df !important;
            transform: scale(1.1);
            transition: all 0.2s ease;
        }

        .card-header .dropdown-toggle:hover {
            color: #4e73df !important;
        }

        /* Progress Bar Styling */
        .kpi-progress-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .kpi-progress-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .progress {
            border-radius: 10px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            font-size: 0.8rem;
            line-height: 25px;
            transition: width 0.8s ease-in-out;
        }

        .kpi-progress-item .badge {
            font-size: 0.7rem;
            padding: 3px 8px;
        }

        @media (max-width: 768px) {
            .chart-radar {
                height: 280px;
            }

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 20px;
            }

            .kpi-progress-item {
                padding: 10px;
                margin-bottom: 15px;
            }
        }

        /* Semua style dari admin/dashboard.blade.php disalin ke sini */
        .quick-access-btn .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        #statTotalPengabdian,
        #statDosenTerlibat,
        #statDenganMahasiswa {
            font-size: 20px !important;
        }

        .list-group-item-action {
            color: #5a5c69;
        }

        .icon-circle.bg-primary {
            background-color: #4e73df !important;
        }

        .icon-circle.bg-success {
            background-color: #1cc88a !important;
        }

        .icon-circle.bg-info {
            background-color: #36b9cc !important;
        }

        .statistics-card {
            transition: all 0.2s ease-in-out;
            border-left-width: 0.25rem !important;
        }

        .statistics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .statistics-card .card-body {
            padding: 1.2rem 1.5rem !important;
        }

        .statistics-card .text-xs {
            font-size: 0.8rem !important;
            line-height: 1.4 !important;
            font-weight: 600 !important;
        }

        .statistics-card .h5 {
            font-size: 1.6rem !important;
            margin-bottom: 0.5rem !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
        }

        .statistics-card .text-muted {
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
        }

        .statistics-card .badge {
            font-size: 0.75rem !important;
            padding: 0.3rem 0.6rem !important;
            font-weight: 600 !important;
        }

        .statistics-card .font-weight-bold {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
        }

        .statistics-card .fa-2x {
            font-size: 2.2em !important;
        }

        .tooltip-icon {
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .tooltip-icon:hover {
            opacity: 1;
        }

        .clickable-stat:hover {
            color: #4e73df !important;
        }

        .list-group-item-action {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .list-group-item-action:hover {
            border-left-color: #4e73df;
            background-color: #f8f9fc;
            transform: translateX(2px);
        }

        #dosenSortBtn {
            border: 1px solid #e3e6f0;
            transition: all 0.2s ease;
        }

        #dosenSortBtn:hover {
            background-color: #4e73df;
            color: white;
            border-color: #4e73df;
        }

        .chart-bar-scrollable {
            background: #f8f9fc;
        }

        .chart-bar-scrollable::-webkit-scrollbar {
            width: 8px;
        }

        .chart-bar-scrollable::-webkit-scrollbar-track {
            background: #e3e6f0;
            border-radius: 4px;
        }

        .chart-bar-scrollable::-webkit-scrollbar-thumb {
            background: #4e73df;
            border-radius: 4px;
        }

        .chart-bar-scrollable::-webkit-scrollbar-thumb:hover {
            background: #2e59d9;
        }

        .chart-container {
            transition: all 0.4s ease-in-out;
            opacity: 1;
        }

        .chart-container.d-none {
            opacity: 0;
            transform: translateY(-20px);
        }

        .btn-group .btn {
            transition: all 0.2s ease;
            border-radius: 0.375rem !important;
        }

        .btn-group .btn:first-child {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .btn-group .btn:last-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .btn-group .btn.active {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Pengabdian InQA</h1>
            <div class="d-flex align-items-center">
                <!-- Year Filter -->
                <form method="GET" action="{{ route('inqa.dashboard') }}" class="mr-3">
                    <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="all" {{ $filterYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Pengabdian Statistics Row -->
        <div class="row mb-4">
            <!-- Total Pengabdian Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengabdian
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Total pengabdian {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;">
                                    {{ $stats['total_pengabdian'] }}
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    @if ($stats['percentage_change_pengabdian'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_pengabdian'] > 0 ? 'success' : 'danger' }} mr-1"
                                            data-toggle="tooltip"
                                            title="Perubahan dari {{ $stats['previous_year'] }}: {{ $stats['percentage_change_pengabdian'] > 0 ? 'Peningkatan' : 'Penurunan' }} {{ abs($stats['percentage_change_pengabdian']) }}% pengabdian">
                                            {{ $stats['percentage_change_pengabdian'] > 0 ? '+' : '' }}{{ $stats['percentage_change_pengabdian'] }}%
                                        </span>
                                    @endif
                                    {{ $stats['year_label'] }}
                                    <div class="mt-2">
                                        <span class="text-success font-weight-bold" data-toggle="tooltip"
                                            title="Pengabdian kolaborasi antara kedua prodi">
                                            <i class="fas fa-handshake mr-1"></i>Kolaborasi TI & SI:
                                            {{ $stats['pengabdian_kolaborasi'] }}
                                        </span>
                                        <br>
                                        <span class="text-primary font-weight-bold" data-toggle="tooltip"
                                            title="Pengabdian khusus Informatika">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['pengabdian_khusus_informatika'] }}
                                        </span>
                                        •

                                        <span class="text-info font-weight-bold" data-toggle="tooltip"
                                            title="Pengabdian khusus Sistem Informasi">
                                            <i class="fas fa-database mr-1"></i>Sistem Informasi:
                                            {{ $stats['pengabdian_khusus_sistem_informasi'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Dosen Terlibat
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Total dosen yang terlibat dalam pengabdian {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;">
                                    {{ $stats['total_dosen'] }}
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    @if ($stats['percentage_change_dosen'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_dosen'] > 0 ? 'success' : 'danger' }} mr-1"
                                            data-toggle="tooltip"
                                            title="Perubahan dari {{ $stats['previous_year'] }}: {{ $stats['percentage_change_dosen'] > 0 ? 'Peningkatan' : 'Penurunan' }} {{ abs($stats['percentage_change_dosen']) }}% dosen terlibat">
                                            {{ $stats['percentage_change_dosen'] > 0 ? '+' : '' }}{{ $stats['percentage_change_dosen'] }}%
                                        </span>
                                    @endif
                                    {{ $stats['year_label'] }}
                                    <div class="mt-2">
                                        <span class="text-primary font-weight-bold">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['dosen_informatika'] }}
                                        </span>
                                        •
                                        <span class="text-info font-weight-bold">
                                            <i class="fas fa-database mr-1"></i>Sistem Informasi:
                                            {{ $stats['dosen_sistem_informasi'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengabdian dengan Mahasiswa Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pengabdian dengan Mahasiswa
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Persentase pengabdian yang melibatkan mahasiswa {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;">
                                    {{ $stats['persentase_pengabdian_dengan_mahasiswa'] }}%
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    <span data-toggle="tooltip"
                                        title="{{ $stats['total_mahasiswa'] }} dari {{ $stats['total_pengabdian'] }} pengabdian melibatkan mahasiswa">
                                        {{ $stats['total_mahasiswa'] }}/{{ $stats['total_pengabdian'] }}
                                    </span>
                                    • {{ $stats['year_label'] }}
                                    <div class="mt-2">
                                        <span class="text-primary font-weight-bold">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['mahasiswa_informatika'] }}
                                        </span>
                                        •
                                        <span class="text-info font-weight-bold">
                                            <i class="fas fa-database mr-1"></i>Sistem Informasi:
                                            {{ $stats['mahasiswa_sistem_informasi'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Progress Bar Row -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="card shadow modern-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        {{-- ... (Header card tidak berubah) ... --}}
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tasks mr-2"></i>Progress Capaian KPI
                            @if ($filterYear !== 'all')
                                <small class="text-muted">(Tahun {{ $filterYear }})</small>
                            @else
                                <small class="text-muted">(Semua Tahun)</small>
                            @endif
                        </h6>
                        <div class="d-flex align-items-center">
                            @php
                                $totalKpi = count($kpiRadarData);
                                $tercapai = collect($kpiRadarData)
                                    ->filter(function ($kpi) {
                                        $skorNormalisasi = $kpi['skor_normalisasi'] ?? $kpi['persentase'];
                                        return $skorNormalisasi >= 100;
                                    })
                                    ->count();
                            @endphp
                            <span class="badge badge-success mr-1">{{ $tercapai }} Tercapai</span>
                            <span class="badge badge-warning">{{ $totalKpi - $tercapai }} Belum Tercapai</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="kpi-progress-container">
                            @php
                                // Kamus untuk memetakan kode KPI ke teks singkat
                                $kpiLabels = [
                                    'PGB.I.1.1' => 'Memenuhi luaran (>=80%) sesuai proposal',
                                    'PGB.I.5.6' => 'Peningkatan jumlah (>= 10 %) per tahun',
                                    'PGB.I.7.4' => 'Mendapat pendanaan eksternal (>=30%)',
                                    'PGB.I.7.9' => 'Peningkatan jumlah proposal yang diterima (>=10%)',
                                    'IKT.I.5.g' => 'Digunakan dalam proses pembelajaran (>= 5 %)',
                                    'IKT.I.5.h' => 'Dilakukan dibidang INFOKOM (>= 70%)',
                                    'IKT.I.5.i' => 'Minimum Prodi memiliki 1 HKI setiap tahun',
                                    'IKT.I.5.j' => 'Minimum 70% PkM melibatkan minimal 1 mahasiswa',
                                ];

                                $kpiOrder = array_keys($kpiLabels);
                                $sortedKpiData = collect($kpiRadarData)->sortBy(function ($kpi) use ($kpiOrder) {
                                    $pos = array_search($kpi['kode'], $kpiOrder);
                                    return $pos === false ? PHP_INT_MAX : $pos;
                                });
                            @endphp

                            @foreach ($sortedKpiData as $index => $kpi)
                                @php
                                    $kpiCode = $kpi['kode'];
                                    $displayText = $kpiLabels[$kpiCode] ?? $kpiCode;
                                    $skorNormalisasi = $kpi['skor_normalisasi'] ?? $kpi['persentase'];
                                    $isNegative = $skorNormalisasi < 0;
                                    $targetValue = $kpi['target'];
                                    $satuan = $kpi['satuan'];
                                    $isDynamicBenchmark =
                                        $satuan === '%' &&
                                        $targetValue < 100 &&
                                        in_array($kpiCode, [
                                            'PGB.I.1.1',
                                            'IKT.I.5.g',
                                            'IKT.I.5.h',
                                            'IKT.I.5.j',
                                            'PGB.I.7.4',
                                        ]);

                                    $benchmarkPositionPercent = 0;
                                    $progressPercentage = 0;
                                    $isTercapai = false;

                                    // --- [PERUBAHAN 1] Tambahkan variabel untuk teks label benchmark ---
                                    $benchmarkLabelText = '';

                                    if ($isDynamicBenchmark && !$isNegative) {
                                        $benchmarkPositionPercent = $targetValue;
                                        $progressPercentage = max(0, min(100, $kpi['realisasi']));
                                        $isTercapai = $kpi['realisasi'] >= $targetValue;
                                        $benchmarkLabelText = $targetValue . '%'; // Teks untuk target dinamis
                                    } else {
                                        $benchmarkPositionPercent = 100;
                                        $progressPercentage = max(0, min(100, $skorNormalisasi));
                                        $isTercapai = $skorNormalisasi >= 100;
                                        $benchmarkLabelText = '100%'; // Teks untuk target statis (100%)
                                    }
                                @endphp

                                <div class="-{{ $isTercapai ? 'success' : ($isNegative ? 'danger' : 'warning') }} pl-3"
                                    data-toggle="tooltip" data-html="true" data-placement="top"
                                    title="<strong>{{ $kpi['indikator'] }}</strong><br> ...">

                                    {{-- ... (Bagian header dan deskripsi tidak berubah) ... --}}
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="mb-0 font-weight-bold text-gray-800">{{ $displayText }}</h6>
                                        <span
                                            class="ml-2 badge badge-{{ $isTercapai ? 'success' : ($isNegative ? 'danger' : 'warning') }} badge-sm">
                                            @if ($isNegative)
                                                Menurun
                                            @else
                                                {{ $isTercapai ? 'Tercapai' : 'Belum Tercapai' }}
                                            @endif
                                        </span>
                                        @if ($isDynamicBenchmark)
                                            <small class="ml-1 text-info"
                                                title="Benchmark dinamis: target {{ $targetValue }}%"><i
                                                    class="fas fa-chart-line"></i></small>
                                        @else
                                            <small class="ml-1 text-muted" title="Benchmark statis: target 100%"><i
                                                    class="fas fa-percentage"></i></small>
                                        @endif
                                        <span
                                            class="ml-auto font-weight-bold text-{{ $isTercapai ? 'success' : ($isNegative ? 'danger' : 'warning') }}">
                                            {{ number_format($skorNormalisasi, 1) }}%
                                        </span>
                                    </div>

                                    <div class="position-relative" style="margin-bottom: 2rem; margin-top: 1.5rem;">
                                        <div class="progress" style="height: 25px;">
                                            @if ($isNegative)
                                                <div class="progress-bar bg-light border" role="progressbar"
                                                    style="width: 100%;"></div>
                                            @else
                                                <div class="progress-bar bg-{{ $isTercapai ? 'success' : 'warning' }}"
                                                    role="progressbar" style="width: {{ $progressPercentage }}%;"></div>
                                            @endif
                                        </div>

                                        <div class="position-absolute"
                                            style="top: 0; left: {{ $benchmarkPositionPercent }}%; transform: translateX(-50%); height: 25px; width: 3px; background-color: #dc3545; z-index: 10;">
                                        </div>

                                        <span class="position-absolute"
                                            style="bottom: 28px; left: {{ $benchmarkPositionPercent }}%; transform: translateX(-50%);
                                             padding: 2px 5px; color: red;
                                             font-size: 0.7rem; font-weight: bold; border-radius: 4px; z-index: 11; white-space: nowrap;">
                                            {{ $benchmarkLabelText }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-bar mr-2"></i>Rekap Pengabdian per Dosen
                            <span id="currentViewBadge" class="badge badge-info ml-2">Top 5</span>
                        </h6>
                        @if ($filterYear !== 'all')
                            <small class="text-muted">
                                <i class="fas fa-filter mr-1"></i>Tahun: {{ $filterYear }}
                            </small>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group mr-2" role="group" aria-label="View Toggle">
                            <button id="viewTop5Btn" type="button" class="btn btn-sm btn-primary active"
                                title="Tampilkan 5 dosen teratas">
                                <i class="fas fa-trophy mr-1"></i>Top 5
                            </button>
                            <button id="viewAllBtn" type="button" class="btn btn-sm btn-outline-primary"
                                title="Tampilkan semua dosen">
                                <i class="fas fa-list mr-1"></i>Semua
                            </button>
                        </div>
                        <button id="dosenSortBtn" type="button" class="btn btn-sm btn-outline-secondary"
                            data-order="desc" title="Urutkan jumlah (tertinggi)">
                            <i class="fas fa-sort-amount-down"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <small id="chartInfoText" class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Menampilkan 5 dosen dengan pengabdian terbanyak
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $allDosenCount = count($namaDosen ?? []);
                    $top5DosenCount = min($allDosenCount, 5);
                    $maxCanvasHeight = max(600, $allDosenCount * 60);
                @endphp

                <!-- Top 5 Chart Container -->
                <div id="top5ChartContainer" class="chart-container">
                    <div class="chart-bar" style="height: {{ max(400, $top5DosenCount * 80) }}px;">
                        <canvas id="dosenChart" width="100%" height="{{ max(400, $top5DosenCount * 80) }}"></canvas>
                    </div>
                </div>

                <!-- All Data Chart Container -->
                <div id="allChartContainer" class="chart-container d-none">
                    <div class="chart-bar-scrollable"
                        style="max-height: 600px; overflow-y: auto; overflow-x: hidden; border: 1px solid #e3e6f0; border-radius: 8px; padding: 20px; background-color: #f8f9fc;">
                        <div style="height: {{ $maxCanvasHeight }}px; min-width: 900px;">
                            <canvas id="dosenAllChart" width="100%" height="{{ $maxCanvasHeight }}"></canvas>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-scroll mr-1"></i>
                            <strong>Tips:</strong> Gunakan scroll untuk navigasi. Total: <span
                                class="font-weight-bold text-primary">{{ $allDosenCount }}</span> dosen
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize tooltips (Biarkan kode ini)
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                    delay: {
                        "show": 500,
                        "hide": 100
                    }
                });
            });

            // --- GANTI SEMUA KODE JAVASCRIPT LAMA ANDA DENGAN KODE INI ---

            // Daftarkan plugin datalabels secara global
            Chart.register(ChartDataLabels);

            // Set default font
            Chart.defaults.font.family =
                'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            Chart.defaults.color = '#858796';

            // 1. Ambil data dari Controller
            const allNamaDosen = @json($namaDosen);
            const allJumlahPengabdian = @json($jumlahPengabdianDosen);

            // 2. Gabungkan data menjadi satu array objek
            let originalData = allNamaDosen.map((nama, index) => ({
                nama: nama,
                jumlah: allJumlahPengabdian[index]
            }));

            // 3. Fungsi untuk membuat atau mengupdate chart
            function createDosenChart(canvasId, labels, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');

                const existingChart = Chart.getChart(canvasId);
                if (existingChart) {
                    existingChart.destroy();
                }

                // --- INI BAGIAN UTAMA UNTUK MEMBUAT GRADASI ANTAR BAR ---
                const backgroundColors = data.map(value => {
                    const baseColor = [78, 115, 223]; // RGB untuk warna biru dasar #4e73df
                    const maxValue = Math.max(...data);

                    if (maxValue === 0) return `rgb(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]})`;

                    // Hitung opacity berdasarkan nilai. Nilai tertinggi = opacity 1 (solid), terendah = opacity 0.3 (pudar)
                    const minOpacity = 0.3;
                    const maxOpacity = 1.0;
                    const opacity = minOpacity + (maxOpacity - minOpacity) * (value / maxValue);

                    return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${opacity.toFixed(2)})`;
                });
                // --- AKHIR BAGIAN UTAMA ---

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: data,
                            backgroundColor: backgroundColors, // Gunakan array warna yang sudah dibuat
                            borderRadius: 4,
                            borderSkipped: false,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Jumlah: ${context.parsed.x} pengabdian`;
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: '#2d3e50',
                                anchor: 'end',
                                align: 'right',
                                offset: 8,
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                formatter: function(value) {
                                    return value > 0 ? value : '';
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 50,
                                top: 10,
                                bottom: 10,
                                left: 10
                            }
                        }
                    }
                });
            }

            // 4. Fungsi untuk update dan render ulang kedua chart
            function updateCharts(dataToShow) {
                const labels = dataToShow.map(d => d.nama);
                const counts = dataToShow.map(d => d.jumlah);

                // Sesuaikan kembali ke Top 5
                createDosenChart('dosenChart', labels.slice(0, 5), counts.slice(0, 5));
                createDosenChart('dosenAllChart', labels, counts);
            }

            // 5. Logika untuk tombol-tombol
            document.addEventListener('DOMContentLoaded', function() {
                updateCharts(originalData);

                const viewTop5Btn = document.getElementById('viewTop5Btn');
                const viewAllBtn = document.getElementById('viewAllBtn');
                const dosenSortBtn = document.getElementById('dosenSortBtn');
                const top5Container = document.getElementById('top5ChartContainer');
                const allContainer = document.getElementById('allChartContainer');
                const badge = document.getElementById('currentViewBadge');
                const infoText = document.getElementById('chartInfoText');

                viewTop5Btn.addEventListener('click', function() {
                    this.classList.add('active', 'btn-primary');
                    this.classList.remove('btn-outline-primary');
                    viewAllBtn.classList.remove('active', 'btn-primary');
                    viewAllBtn.classList.add('btn-outline-primary');

                    top5Container.classList.remove('d-none');
                    allContainer.classList.add('d-none');

                    // Sesuaikan kembali ke Top 5
                    badge.textContent = 'Top 5';
                    infoText.innerHTML =
                        '<i class="fas fa-info-circle mr-1"></i>Menampilkan 5 dosen dengan pengabdian terbanyak';
                });

                viewAllBtn.addEventListener('click', function() {
                    this.classList.add('active', 'btn-primary');
                    this.classList.remove('btn-outline-primary');
                    viewTop5Btn.classList.remove('active', 'btn-primary');
                    viewTop5Btn.classList.add('btn-outline-primary');

                    top5Container.classList.add('d-none');
                    allContainer.classList.remove('d-none');

                    badge.textContent = 'Semua';
                    infoText.innerHTML =
                        '<i class="fas fa-info-circle mr-1"></i>Menampilkan semua dosen yang tercatat';
                });

                dosenSortBtn.addEventListener('click', function() {
                    const currentOrder = this.dataset.order;
                    const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';

                    originalData.sort((a, b) => (newOrder === 'asc' ? a.jumlah - b.jumlah : b.jumlah - a
                        .jumlah));

                    this.dataset.order = newOrder;
                    this.innerHTML = newOrder === 'asc' ? '<i class="fas fa-sort-amount-up"></i>' :
                        '<i class="fas fa-sort-amount-down"></i>';
                    this.title = newOrder === 'asc' ? 'Urutkan jumlah (terendah)' :
                    'Urutkan jumlah (tertinggi)';

                    updateCharts(originalData);
                });
            });
        </script>
    @endpush
@endsection
