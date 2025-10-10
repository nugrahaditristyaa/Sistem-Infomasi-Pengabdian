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

        <!-- KPI Progress Bar Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
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
                        <!-- Progress Bars Container -->
                        <div id="kpi-progress-container">
                            @foreach ($kpiRadarData as $index => $kpi)
                                @php
                                    $skorNormalisasi = $kpi['skor_normalisasi'] ?? $kpi['persentase'];
                                    $isNegative = $skorNormalisasi < 0;

                                    // Determine KPI type and benchmark strategy
                                    $kpiCode = $kpi['kode'];
                                    $targetValue = $kpi['target'];
                                    $satuan = $kpi['satuan'];

                                    // Determine benchmark type based on KPI characteristics
                                    // Dynamic benchmark: direct percentage targets (target represents the actual percentage needed)
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

                                    // Static benchmark: growth rates, absolute numbers, or normalized achievements
                                    $isStaticBenchmark =
                                        // Growth KPIs (target is growth percentage, but 100% represents achieving that growth)
                                        in_array($kpiCode, ['PGB.I.5.6', 'PGB.I.7.9']) ||
                                        // Absolute number KPIs (1 HKI, etc.)
                                        ($satuan !== '%' && $targetValue <= 10) ||
                                        // Any other KPI not explicitly identified as dynamic
                                        (!$isDynamicBenchmark && $satuan === '%');

                                    if ($isDynamicBenchmark) {
                                        // For dynamic benchmark:
                                        // - Benchmark line position = target value percentage on 0-100% scale
                                        // - Progress bar always proportional to 0-100% scale
                                        $benchmarkPositionPercent = $targetValue; // Position benchmark at target value
                                        $progressPercentage = max(0, min(100, $kpi['realisasi'])); // Always 0-100% scale
                                        $isTercapai = $kpi['realisasi'] >= $targetValue;
                                    } else {
                                        // For static benchmark: benchmark always at 100%, use normalized score
                                        $benchmarkPositionPercent = 100;
                                        $progressPercentage = max(0, min(100, $skorNormalisasi));
                                        $isTercapai = $skorNormalisasi >= 100;
                                    }
                                @endphp

                                <div class="kpi-progress-item mb-4 border-left-{{ $isTercapai ? 'success' : 'warning' }} pl-3"
                                    data-toggle="tooltip" data-html="true" data-placement="top"
                                    title="<strong>{{ $kpi['kode'] }}:</strong> {{ $kpi['indikator'] }}<br>
                                            <strong>Target:</strong> {{ number_format($kpi['target']) }} {{ $kpi['satuan'] }}<br>
                                            <strong>Realisasi:</strong> {{ number_format($kpi['realisasi'], 2) }} {{ $kpi['satuan'] }}<br>
                                            <strong>Capaian:</strong> {{ number_format($skorNormalisasi, 1) }}%">

                                    <!-- KPI Header -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 font-weight-bold text-gray-800">{{ $kpi['kode'] }}</h6>
                                            <span
                                                class="ml-2 badge badge-{{ $isTercapai ? 'success' : 'warning' }} badge-sm">
                                                {{ $isTercapai ? 'Tercapai' : 'Belum Tercapai' }}
                                            </span>
                                            @if ($isDynamicBenchmark)
                                                <small class="ml-1 text-info"
                                                    title="Benchmark dinamis: skala 0-{{ $targetValue }}%">
                                                    <i class="fas fa-chart-line"></i>
                                                </small>
                                            @else
                                                <small class="ml-1 text-muted" title="Benchmark statis: skala 0-100%">
                                                    <i class="fas fa-percentage"></i>
                                                </small>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @if ($isNegative)
                                                <span class="text-danger font-weight-bold">Menurun
                                                    {{ number_format(abs($skorNormalisasi), 1) }}%</span>
                                            @else
                                                <span
                                                    class="font-weight-bold text-{{ $isTercapai ? 'success' : 'warning' }}">
                                                    {{ number_format($skorNormalisasi, 1) }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- KPI Description -->
                                    <div class="text-muted mb-2" style="font-size: 0.85rem;">
                                        {{ Str::limit($kpi['indikator'], 100) }}
                                    </div>

                                    <!-- Progress Bar Container with Target Benchmark -->
                                    <div class="position-relative mb-2">
                                        <!-- Progress Bar -->
                                        <div class="progress" style="height: 25px;">
                                            @if ($isNegative)
                                                <!-- Empty progress bar for negative values -->
                                                <div class="progress-bar bg-light border" role="progressbar"
                                                    style="width: 100%; color: #dc3545;" aria-valuenow="0"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    <span class="font-weight-bold">Menurun
                                                        {{ number_format(abs($skorNormalisasi), 1) }}%</span>
                                                </div>
                                            @else
                                                <!-- Normal progress bar -->
                                                <div class="progress-bar bg-{{ $isTercapai ? 'success' : 'warning' }}"
                                                    role="progressbar" style="width: {{ $progressPercentage }}%;"
                                                    aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    @if ($progressPercentage > 15)
                                                        <span class="font-weight-bold">
                                                            @if ($isDynamicBenchmark)
                                                                {{ number_format($kpi['realisasi'], 1) }}%
                                                            @else
                                                                {{ number_format($skorNormalisasi, 1) }}%
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Target Benchmark Line -->
                                        @if (!$isNegative)
                                            <div class="position-absolute"
                                                style="top: 0; left: {{ $benchmarkPositionPercent }}%; transform: translateX(-50%); height: 25px; width: 2px; background-color: #dc3545; z-index: 10; box-shadow: 0 0 3px rgba(220, 53, 69, 0.5);">
                                            </div>
                                            <div class="position-absolute text-danger"
                                                style="top: -18px; left: {{ $benchmarkPositionPercent }}%; transform: translateX(-50%); font-size: 0.65rem; font-weight: bold; white-space: nowrap;">
                                                @if ($isDynamicBenchmark)
                                                    {{ $targetValue }}%
                                                @else
                                                    Target
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Progress Labels -->
                                    <div class="d-flex justify-content-between mb-2"
                                        style="font-size: 0.75rem; position: relative;">
                                        <span class="text-muted">0%</span>
                                        <span class="text-muted">50%</span>
                                        <span class="text-muted">100%</span>
                                        @if ($isDynamicBenchmark)
                                            <!-- Target marker at exact position -->
                                            <span class="text-danger font-weight-bold position-absolute"
                                                style="left: {{ $benchmarkPositionPercent }}%; transform: translateX(-50%); top: -3px;">
                                                ↑{{ $targetValue }}%
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Detail Info -->
                                    <div class="row" style="font-size: 0.8rem;">
                                        <div class="col-6">
                                            <span class="text-muted">Target:</span>
                                            <span class="font-weight-bold">{{ number_format($kpi['target']) }}
                                                {{ $kpi['satuan'] }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Realisasi:</span>
                                            <span
                                                class="font-weight-bold text-{{ $isTercapai ? 'success' : ($isNegative ? 'danger' : 'warning') }}">
                                                {{ number_format($kpi['realisasi'], 2) }} {{ $kpi['satuan'] }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($index < count($kpiRadarData) - 1)
                                        <hr class="mt-3">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <!-- Summary Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="alert-heading"><i class="fas fa-info-circle mr-2"></i>Cara Membaca
                                                Progress</h6>
                                            <ul class="mb-0" style="font-size: 0.9rem;">
                                                <li><strong>Skala Batang:</strong> Selalu 0-100% proporsional</li>
                                                <li><strong>Garis Merah:</strong> Posisi target dinamis</li>
                                                <li><strong>KPI Persentase:</strong> Target di posisi sesuai %</li>
                                                <li><strong>KPI Pertumbuhan:</strong> Target di posisi 100%</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="alert-heading"><i class="fas fa-chart-line mr-2"></i>Interpretasi
                                                Hasil</h6>
                                            <ul class="mb-0" style="font-size: 0.9rem;">
                                                <li><strong>Hijau:</strong> Target tercapai/terlampaui</li>
                                                <li><strong>Kuning:</strong> Belum mencapai target</li>
                                                <li><strong>Batang vs Garis:</strong> Proporsi vs target</li>
                                                <li><strong>Icon:</strong> <i class="fas fa-chart-line text-info"></i> =
                                                    dinamis, <i class="fas fa-percentage text-muted"></i> = statis</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            // Initialize tooltips for progress bars
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                    delay: {
                        "show": 500,
                        "hide": 100
                    }
                });
            });
        </script>
    @endpush

@endsection
