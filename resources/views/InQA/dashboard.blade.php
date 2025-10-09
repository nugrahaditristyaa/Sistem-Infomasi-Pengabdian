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

        @media (max-width: 768px) {
            .chart-radar {
                height: 280px;
            }

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 20px;
            }
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
                            <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '2024' }}>
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

        <!-- KPI PGB.I.1.1 Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-info shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    KPI PGB.I.1.1 - Realisasi Luaran Pengabdian
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    $outputKpi = collect($kpiRadarData)->firstWhere('kode', 'PGB.I.1.1');
                                    $realisasiPercentage = $outputKpi ? $outputKpi['realisasi'] : 0;
                                    $targetPercentage = $outputKpi ? $outputKpi['target'] : 80; // Default target 80%
                                    $achievement =
                                        $targetPercentage > 0 ? ($realisasiPercentage / $targetPercentage) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi: {{ number_format($realisasiPercentage, 2) }}%
                                    <small class="text-muted">(Target: ≥ {{ $targetPercentage }}%)</small>
                                    @if ($realisasiPercentage >= $targetPercentage)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($realisasiPercentage >= $targetPercentage) bg-success
                                        @else bg-warning @endif
                                    "
                                        style="width: {{ min($achievement, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Persentase PkM yang memenuhi luaran sesuai target proposal
                                    <br>
                                    <strong>Rumus:</strong> (Jumlah PkM yang Memenuhi Luaran / Total PkM) × 100%
                                    <br>
                                    <strong>Kriteria:</strong> Jumlah luaran terealisasi ≥ jumlah luaran yang direncanakan
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI PGB.I.7.9 Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-primary shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    KPI PGB.I.7.9 - Pertumbuhan PkM (3 Tahun)
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }} vs
                                            {{ $filterYear - 3 }})</small>
                                    @else
                                        <small class="text-lowercase">({{ date('Y') }} vs
                                            {{ date('Y') - 3 }})</small>
                                    @endif
                                </div>
                                @php
                                    $growthKpi = collect($kpiRadarData)->firstWhere('kode', 'PGB.I.7.9');
                                    $realisasiGrowth = $growthKpi ? $growthKpi['realisasi'] : 0;
                                    $targetGrowth = $growthKpi ? $growthKpi['target'] : 10; // Default target 10%
                                    $achievementGrowth =
                                        $realisasiGrowth >= $targetGrowth
                                            ? 100
                                            : ($realisasiGrowth / max($targetGrowth, 1)) * 100;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi:
                                    <span class="{{ $realisasiGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $realisasiGrowth >= 0 ? '+' : '' }}{{ number_format($realisasiGrowth, 2) }}%
                                    </span>
                                    <small class="text-muted">(Target: ≥{{ $targetGrowth }}%)</small>
                                    @if ($realisasiGrowth >= $targetGrowth)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($realisasiGrowth >= $targetGrowth) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ $realisasiGrowth >= 0 ? min(max($achievementGrowth, 0), 100) : 0 }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Pertumbuhan PkM dari tahun N-3 ke tahun N (3 tahun)
                                    <br>
                                    <strong>Rumus:</strong> ((PkM Tahun N - PkM Tahun N-3) / PkM Tahun N-3) × 100%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-radar-chart mr-2"></i>Capaian KPI
                            @if ($filterYear !== 'all')
                                <small class="text-muted">(Tahun {{ $filterYear }})</small>
                            @endif
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <span class="badge badge-info mr-2">
                                    <i class="fas fa-sync-alt mr-1"></i>Target Dinamis
                                </span>
                                <small class="text-muted">
                                    Target dapat diubah secara real-time
                                </small>
                            </div>
                            <a href="{{ route('inqa.kpi.index') }}" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                title="Klik untuk mengubah target KPI">
                                <i class="fas fa-edit mr-1"></i>Edit Target KPI
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Radar Chart -->
                            <div class="col-lg-8">
                                <div class="chart-area">
                                    <canvas id="kpiRadarChart" width="100%" height="50"></canvas>
                                </div>
                            </div>
                            <!-- KPI Legend -->
                            <div class="col-lg-4">
                                <h6 class="font-weight-bold text-primary mb-3">Detail Realisasi KPI</h6>
                                <div class="kpi-legend" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($kpiRadarData as $index => $kpi)
                                        <div class="kpi-item mb-3 p-3 border rounded"
                                            style="background-color: {{ $kpi['persentase'] >= 100 ? '#d4edda' : '#f8d7da' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1 font-weight-bold text-dark">{{ $kpi['kode'] }}</h6>
                                                <span
                                                    class="badge badge-{{ $kpi['persentase'] >= 100 ? 'success' : 'danger' }}">
                                                    {{ $kpi['persentase'] }}%
                                                </span>
                                            </div>
                                            <p class="mb-2 text-dark small">{{ $kpi['indikator'] }}</p>
                                            <div class="row text-sm">
                                                <div class="col-6">
                                                    <strong>Target:</strong><br>
                                                    <span class="text-primary">{{ number_format($kpi['target']) }}
                                                        {{ $kpi['satuan'] }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Realisasi:</strong><br>
                                                    <span class="text-success">{{ number_format($kpi['realisasi']) }}
                                                        {{ $kpi['satuan'] }}</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $kpi['persentase'] >= 100 ? 'success' : 'danger' }}"
                                                    style="width: {{ min($kpi['persentase'], 100) }}%"></div>
                                            </div>
                                            <small class="text-muted">Status: {{ $kpi['status'] }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h5 class="mb-1">{{ count($kpiRadarData) }}</h5>
                                            <small>Total KPI</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="mb-1 text-success">
                                                {{ collect($kpiRadarData)->where('persentase', '>=', 100)->count() }}</h5>
                                            <small>Tercapai (≥100%)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="mb-1 text-danger">
                                                {{ collect($kpiRadarData)->where('persentase', '<', 100)->count() }}</h5>
                                            <small>Belum Tercapai (<100%)< /small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI IKT.I.5.g Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-success shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    KPI IKT.I.5.g - Pengabdian Pendidikan/Pelatihan
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    $educationalKpi = collect($kpiRadarData)->firstWhere('kode', 'IKT.I.5.g');
                                    $realisasiPercentage = $educationalKpi ? $educationalKpi['realisasi'] : 0;
                                    $targetPercentage = $educationalKpi ? $educationalKpi['target'] : 5; // Default target 5%
                                    $achievement =
                                        $targetPercentage > 0 ? ($realisasiPercentage / $targetPercentage) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi: {{ number_format($realisasiPercentage, 2) }}%
                                    <small class="text-muted">(Target: {{ $targetPercentage }}%)</small>
                                    @if ($achievement >= 100)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($achievement >= 100) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ min($achievement, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Persentase pengabdian dengan kata kunci pendidikan (siswa, sma,
                                    pembelajaran, pelatihan, dll.)
                                    <br>
                                    <strong>Rumus:</strong> (Jumlah PkM dengan keyword / Total PkM) × 100%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI IKT.I.5.h Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-info shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    KPI IKT.I.5.h - Pengabdian INFOKOM
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    $infokomKpi = collect($kpiRadarData)->firstWhere('kode', 'IKT.I.5.h');
                                    $realisasiInfokom = $infokomKpi ? $infokomKpi['realisasi'] : 0;
                                    $targetInfokom = $infokomKpi ? $infokomKpi['target'] : 70; // Default target 70%
                                    $achievementInfokom =
                                        $targetInfokom > 0 ? ($realisasiInfokom / $targetInfokom) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi: {{ number_format($realisasiInfokom, 2) }}%
                                    <small class="text-muted">(Target: {{ $targetInfokom }}%)</small>
                                    @if ($achievementInfokom >= 100)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($achievementInfokom >= 100) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ min($achievementInfokom, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Persentase pengabdian dengan kata kunci INFOKOM (AI, Algoritma,
                                    Aplikasi, Big Data, Digital, ICT, Informatika, Komputer, dll.)
                                    <br>
                                    <strong>Rumus:</strong> (Jumlah PkM INFOKOM / Total PkM) × 100%
                                    <br>
                                    <strong>Total Keywords:</strong> 57 kata kunci teknologi informasi dan komputer
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-microchip fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI IKT.I.5.i Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    KPI IKT.I.5.i - Minimum Prodi memiliki 1 HKI PkM
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    // Mencari data HKI dari kpiRadarData atau hitung manual
                                    $hkiKpi = collect($kpiRadarData)->firstWhere('kode', 'IKT.I.5.i');

                                    // Simulasi data HKI per prodi (nanti bisa diganti dengan data real dari controller)
                                    use Illuminate\Support\Facades\DB;

                                    // Hitung HKI Informatika
                                    $hkiInformatika = DB::table('luaran')
                                        ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                                        ->join(
                                            'jenis_luaran',
                                            'luaran.id_jenis_luaran',
                                            '=',
                                            'jenis_luaran.id_jenis_luaran',
                                        )
                                        ->join(
                                            'pengabdian_dosen',
                                            'pengabdian.id_pengabdian',
                                            '=',
                                            'pengabdian_dosen.id_pengabdian',
                                        )
                                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                                        ->where('jenis_luaran.nama_jenis_luaran', 'HKI')
                                        ->where('dosen.prodi', 'Informatika')
                                        ->when($filterYear !== 'all', function ($q) use ($filterYear) {
                                            return $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                                        })
                                        ->distinct('luaran.id_luaran')
                                        ->count('luaran.id_luaran');

                                    // Hitung HKI Sistem Informasi
                                    $hkiSistemInformasi = DB::table('luaran')
                                        ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
                                        ->join(
                                            'jenis_luaran',
                                            'luaran.id_jenis_luaran',
                                            '=',
                                            'jenis_luaran.id_jenis_luaran',
                                        )
                                        ->join(
                                            'pengabdian_dosen',
                                            'pengabdian.id_pengabdian',
                                            '=',
                                            'pengabdian_dosen.id_pengabdian',
                                        )
                                        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
                                        ->where('jenis_luaran.nama_jenis_luaran', 'HKI')
                                        ->where('dosen.prodi', 'Sistem Informasi')
                                        ->when($filterYear !== 'all', function ($q) use ($filterYear) {
                                            return $q->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                                        })
                                        ->distinct('luaran.id_luaran')
                                        ->count('luaran.id_luaran');

                                    $hkiPerProdi = [
                                        'informatika' => $hkiInformatika,
                                        'sistem_informasi' => $hkiSistemInformasi,
                                        'total' => $hkiInformatika + $hkiSistemInformasi,
                                        'informatika_tercapai' => $hkiInformatika >= 1,
                                        'sistem_informasi_tercapai' => $hkiSistemInformasi >= 1,
                                        'kedua_prodi_tercapai' => $hkiInformatika >= 1 && $hkiSistemInformasi >= 1,
                                    ];
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Informatika:</small>
                                            <span class="h6">{{ $hkiPerProdi['informatika'] }} HKI</span>
                                            @if ($hkiPerProdi['informatika_tercapai'])
                                                <span class="badge badge-success ml-1">Tercapai</span>
                                            @else
                                                <span class="badge badge-danger ml-1">Belum Tercapai</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Sistem Informasi:</small>
                                            <span class="h6">{{ $hkiPerProdi['sistem_informasi'] }} HKI</span>
                                            @if ($hkiPerProdi['sistem_informasi_tercapai'])
                                                <span class="badge badge-success ml-1">Tercapai</span>
                                            @else
                                                <span class="badge badge-danger ml-1">Belum Tercapai</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-info"
                                        style="width: {{ $hkiPerProdi['informatika_tercapai'] ? 50 : 0 }}%"></div>
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $hkiPerProdi['sistem_informasi_tercapai'] ? 50 : 0 }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Target:</strong> Minimal 1 HKI per prodi per tahun
                                    <br>
                                    <strong>Status Keseluruhan:</strong>
                                    @if ($hkiPerProdi['kedua_prodi_tercapai'])
                                        <span class="text-success font-weight-bold">Tercapai (Kedua prodi ≥ 1 HKI)</span>
                                    @else
                                        <span class="text-danger font-weight-bold">Belum Tercapai</span>
                                    @endif
                                    <br>
                                    <strong>Total HKI:</strong> {{ $hkiPerProdi['total'] }} HKI
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-certificate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI IKT.I.5.j Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    KPI IKT.I.5.j - Keterlibatan Mahasiswa dalam PkM
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    $studentKpi = collect($kpiRadarData)->firstWhere('kode', 'IKT.I.5.j');
                                    $realisasiStudent = $studentKpi ? $studentKpi['realisasi'] : 0;
                                    $targetStudent = $studentKpi ? $studentKpi['target'] : 70; // Default target 70%
                                    $achievementStudent =
                                        $targetStudent > 0 ? ($realisasiStudent / $targetStudent) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi: {{ number_format($realisasiStudent, 2) }}%
                                    <small class="text-muted">(Target: {{ $targetStudent }}%)</small>
                                    @if ($achievementStudent >= 100)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($achievementStudent >= 100) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ min($achievementStudent, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Persentase PkM yang dalam tim pelaksananya melibatkan minimal 1
                                    mahasiswa
                                    <br>
                                    <strong>Rumus:</strong> (Jumlah PkM yang melibatkan mahasiswa / Total PkM) × 100%
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

        <!-- KPI PGB.I.7.4 Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-secondary shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    KPI PGB.I.7.4 - PkM dengan Dana Eksternal
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }})</small>
                                    @endif
                                </div>
                                @php
                                    $externalKpi = collect($kpiRadarData)->firstWhere('kode', 'PGB.I.7.4');
                                    $realisasiExternal = $externalKpi ? $externalKpi['realisasi'] : 0;
                                    $targetExternal = $externalKpi ? $externalKpi['target'] : 30; // Default target 30%
                                    $achievementExternal =
                                        $targetExternal > 0 ? ($realisasiExternal / $targetExternal) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi: {{ number_format($realisasiExternal, 2) }}%
                                    <small class="text-muted">(Target: ≥{{ $targetExternal }}%)</small>
                                    @if ($achievementExternal >= 100)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($achievementExternal >= 100) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ min($achievementExternal, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Persentase PkM yang jenis sumber dananya adalah "Eksternal"
                                    <br>
                                    <strong>Rumus:</strong> (Jumlah PkM dana eksternal / Total PkM) × 100%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI PGB.I.7.9 Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-primary shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    KPI PGB.I.7.9 - Pertumbuhan Proposal (3 Tahun)
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }} vs
                                            {{ $filterYear - 3 }})</small>
                                    @else
                                        <small class="text-lowercase">({{ date('Y') }} vs
                                            {{ date('Y') - 3 }})</small>
                                    @endif
                                </div>
                                @php
                                    $growthKpi = collect($kpiRadarData)->firstWhere('kode', 'PGB.I.7.9');
                                    $realisasiGrowth = $growthKpi ? $growthKpi['realisasi'] : 0;
                                    $targetGrowth = $growthKpi ? $growthKpi['target'] : 10; // Default target 10%
                                    $achievementGrowth =
                                        $targetGrowth > 0 ? ($realisasiGrowth / $targetGrowth) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi:
                                    <span class="{{ $realisasiGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $realisasiGrowth >= 0 ? '+' : '' }}{{ number_format($realisasiGrowth, 2) }}%
                                    </span>
                                    <small class="text-muted">(Target: ≥{{ $targetGrowth }}%)</small>
                                    @if ($realisasiGrowth >= $targetGrowth)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($realisasiGrowth >= $targetGrowth) bg-success
                                        @else bg-danger @endif
                                    "
                                        style="width: {{ $realisasiGrowth >= 0 ? min(($realisasiGrowth / max($targetGrowth, 1)) * 100, 100) : 0 }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Pertumbuhan proposal diterima dari tahun N-3 ke tahun N
                                    <br>
                                    <strong>Rumus:</strong> ((Proposal Tahun N - Proposal Tahun N-3) / Proposal Tahun N-3) ×
                                    100%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI PGB.I.5.6 Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    KPI PGB.I.5.6 - Pertumbuhan PkM Tahunan
                                    @if ($filterYear !== 'all')
                                        <small class="text-lowercase">(Tahun {{ $filterYear }} vs
                                            {{ $filterYear - 1 }})</small>
                                    @else
                                        <small class="text-lowercase">({{ date('Y') }} vs
                                            {{ date('Y') - 1 }})</small>
                                    @endif
                                </div>
                                @php
                                    $annualGrowthKpi = collect($kpiRadarData)->firstWhere('kode', 'PGB.I.5.6');
                                    $realisasiAnnualGrowth = $annualGrowthKpi ? $annualGrowthKpi['realisasi'] : 0;
                                    $targetAnnualGrowth = $annualGrowthKpi ? $annualGrowthKpi['target'] : 10; // Default target 10%
                                    $achievementAnnualGrowth =
                                        $targetAnnualGrowth > 0
                                            ? ($realisasiAnnualGrowth / $targetAnnualGrowth) * 100
                                            : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    Realisasi:
                                    <span class="{{ $realisasiAnnualGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $realisasiAnnualGrowth >= 0 ? '+' : '' }}{{ number_format($realisasiAnnualGrowth, 2) }}%
                                    </span>
                                    <small class="text-muted">(Target: ≥{{ $targetAnnualGrowth }}%)</small>
                                    @if ($realisasiAnnualGrowth >= $targetAnnualGrowth)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($realisasiAnnualGrowth >= $targetAnnualGrowth) bg-success
                                        @else bg-warning @endif
                                    "
                                        style="width: {{ $realisasiAnnualGrowth >= 0 ? min(($realisasiAnnualGrowth / max($targetAnnualGrowth, 1)) * 100, 100) : 0 }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-muted">
                                    <strong>Metode:</strong> Pertumbuhan PkM dari tahun N-1 ke tahun N
                                    <br>
                                    <strong>Rumus:</strong> ((PkM Tahun N - PkM Tahun N-1) / PkM Tahun N-1) × 100%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-seedling fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Radar Chart Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-area mr-2"></i>KPI Radar Chart - Capaian Target
                            @if ($filterYear !== 'all')
                                <small class="text-muted">(Tahun {{ $filterYear }})</small>
                            @else
                                <small class="text-muted">(Semua Tahun)</small>
                            @endif
                        </h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Aksi Radar Chart:</div>
                                <a class="dropdown-item" href="#" onclick="downloadRadarChart()">
                                    <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Download Chart
                                </a>
                                <a class="dropdown-item" href="#" onclick="toggleRadarChartType()">
                                    <i class="fas fa-exchange-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Ubah Tampilan
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="chart-radar">
                                    <canvas id="kpiRadarChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="kpi-legend">
                                    <h6 class="font-weight-bold text-gray-800 mb-3">
                                        <i class="fas fa-list-alt mr-2"></i>Detail KPI
                                    </h6>
                                    <div class="kpi-legend-items" style="max-height: 350px; overflow-y: auto;">
                                        @foreach ($kpiRadarData as $index => $kpi)
                                            <div
                                                class="legend-item mb-3 p-2 border-left-{{ $kpi['persentase'] >= 100 ? 'success' : 'warning' }} shadow-sm">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                                                            {{ $kpi['kode'] }}
                                                        </div>
                                                        <div class="text-sm text-gray-800" style="font-size: 0.75rem;">
                                                            {{ Str::limit($kpi['indikator'], 50) }}
                                                        </div>
                                                        <div class="text-xs text-muted mt-1">
                                                            Target: <span
                                                                class="font-weight-bold">{{ number_format($kpi['target']) }}
                                                                {{ $kpi['satuan'] }}</span>
                                                            <br>
                                                            Realisasi: <span
                                                                class="font-weight-bold text-{{ $kpi['persentase'] >= 100 ? 'success' : 'warning' }}">
                                                                {{ number_format($kpi['realisasi'], 2) }}
                                                                {{ $kpi['satuan'] }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div
                                                            class="h6 mb-0 font-weight-bold text-{{ $kpi['persentase'] >= 100 ? 'success' : 'warning' }}">
                                                            {{ $kpi['persentase'] }}%
                                                        </div>
                                                        <span
                                                            class="badge badge-{{ $kpi['persentase'] >= 100 ? 'success' : 'warning' }}">
                                                            {{ $kpi['status'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Keterangan:</strong>
                                    KPI diurutkan berdasarkan nilai realisasi dari tertinggi ke terendah untuk menciptakan
                                    aliran visual yang lebih mulus.
                                    Garis biru menunjukkan target KPI, sedangkan garis hijau menunjukkan realisasi
                                    pencapaian.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Set Chart.js defaults for better styling
            Chart.defaults.font.family =
                'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            Chart.defaults.color = '#858796';

            // KPI Radar Chart
            document.addEventListener('DOMContentLoaded', function() {
                const kpiData = @json($kpiRadarData);

                // Prepare data for radar chart
                const labels = kpiData.map(item => item.kode);

                // Normalize data for radar chart (max value determines 100%)
                const maxTarget = Math.max(...kpiData.map(item => item.target));
                const maxRealisasi = Math.max(...kpiData.map(item => item.realisasi));
                const chartMax = Math.max(maxTarget, maxRealisasi);

                // Calculate percentage for chart display
                const targetData = kpiData.map(item => (item.target / chartMax) * 100);
                const realisasiData = kpiData.map(item => (item.realisasi / chartMax) * 100);

                // Radar Chart Configuration
                const ctx = document.getElementById('kpiRadarChart').getContext('2d');
                const radarChart = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Target KPI',
                            data: targetData,
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 0.8)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.1,
                            fill: true
                        }, {
                            label: 'Realisasi',
                            data: realisasiData,
                            backgroundColor: 'rgba(28, 200, 138, 0.2)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutCubic'
                        },
                        elements: {
                            line: {
                                borderJoinStyle: 'round',
                                borderCapStyle: 'round'
                            },
                            point: {
                                borderWidth: 2,
                                hoverBorderWidth: 3
                            }
                        },
                        scales: {
                            r: {
                                min: 0,
                                max: 100,
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 20,
                                    callback: function(value) {
                                        // Convert percentage back to actual value for display
                                        const actualValue = Math.round((value / 100) * chartMax);
                                        return actualValue.toLocaleString();
                                    }
                                },
                                pointLabels: {
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const kpi = kpiData[context.dataIndex];
                                        const actualValue = Math.round((context.parsed.r / 100) * chartMax);

                                        if (context.datasetIndex === 0) {
                                            return [
                                                `Target KPI: ${kpi.target.toLocaleString()} ${kpi.satuan}`,
                                                `Dapat diubah melalui halaman KPI`
                                            ];
                                        } else {
                                            const persentase = kpi.target > 0 ? Math.round((kpi.realisasi /
                                                kpi.target) * 100) : 0;
                                            return [
                                                `${context.dataset.label}: ${kpi.realisasi.toLocaleString()} ${kpi.satuan}`,
                                                `Target: ${kpi.target.toLocaleString()} ${kpi.satuan}`,
                                                `Persentase Capaian: ${persentase}%`,
                                                `Status: ${kpi.status}`
                                            ];
                                        }
                                    },
                                    title: function(context) {
                                        const kpi = kpiData[context[0].dataIndex];
                                        return `${kpi.kode}: ${kpi.indikator}`;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false
                        }
                    }
                });

                // Auto refresh chart when year filter changes
                const yearSelect = document.querySelector('select[name="year"]');
                if (yearSelect) {
                    yearSelect.addEventListener('change', function() {
                        // Chart will refresh with page reload
                    });
                }

                // Function to update chart with new KPI data
                window.updateKpiChart = function(newKpiData) {
                    // Recalculate max values
                    const newMaxTarget = Math.max(...newKpiData.map(item => item.target));
                    const newMaxRealisasi = Math.max(...newKpiData.map(item => item.realisasi));
                    const newChartMax = Math.max(newMaxTarget, newMaxRealisasi);

                    // Update data
                    const newTargetData = newKpiData.map(item => (item.target / newChartMax) * 100);
                    const newRealisasiData = newKpiData.map(item => (item.realisasi / newChartMax) * 100);

                    // Update chart datasets
                    radarChart.data.datasets[0].data = newTargetData;
                    radarChart.data.datasets[1].data = newRealisasiData;

                    // Update scale max
                    radarChart.options.scales.r.ticks.callback = function(value) {
                        const actualValue = Math.round((value / 100) * newChartMax);
                        return actualValue.toLocaleString();
                    };

                    // Refresh chart
                    radarChart.update();
                };

                // Listen for storage events (when KPI is updated from another tab/window)
                window.addEventListener('storage', function(e) {
                    if (e.key === 'kpiUpdated') {
                        location.reload(); // Reload to get fresh data
                    }
                });

                // Function to download radar chart as image
                window.downloadRadarChart = function() {
                    const link = document.createElement('a');
                    link.download = `KPI_Radar_Chart_${new Date().toISOString().split('T')[0]}.png`;
                    link.href = document.getElementById('kpiRadarChart').toDataURL();
                    link.click();
                };

                // Function to toggle chart type between radar and polar area
                window.toggleRadarChartType = function() {
                    const currentType = radarChart.config.type;
                    const newType = currentType === 'radar' ? 'polarArea' : 'radar';

                    // Update chart type
                    radarChart.config.type = newType;

                    if (newType === 'polarArea') {
                        // Configure for polar area chart
                        radarChart.data.datasets = [{
                            label: 'Capaian KPI (%)',
                            data: kpiData.map(item => item.persentase),
                            backgroundColor: kpiData.map(item =>
                                item.persentase >= 100 ? 'rgba(28, 200, 138, 0.7)' :
                                'rgba(255, 193, 7, 0.7)'
                            ),
                            borderColor: kpiData.map(item =>
                                item.persentase >= 100 ? 'rgba(28, 200, 138, 1)' :
                                'rgba(255, 193, 7, 1)'
                            ),
                            borderWidth: 2
                        }];
                    } else {
                        // Configure for radar chart
                        radarChart.data.datasets = [{
                            label: 'Target KPI',
                            data: targetData,
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 0.8)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                            fill: true
                        }, {
                            label: 'Realisasi',
                            data: realisasiData,
                            backgroundColor: 'rgba(28, 200, 138, 0.2)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                            fill: true
                        }];
                    }

                    radarChart.update();
                };
            });
        </script>
    @endpush

@endsection
