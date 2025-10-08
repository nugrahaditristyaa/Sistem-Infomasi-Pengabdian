@extends('inqa.layouts.main')

@section('title', 'Dashboard InQA')

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
                                    $actualPercentage = $educationalKpi ? $educationalKpi['capaian'] : 0;
                                    $targetPercentage = 5; // Target 5%
                                    $achievement =
                                        $targetPercentage > 0 ? ($actualPercentage / $targetPercentage) * 100 : 0;
                                @endphp
                                <div class="h5 mb-2 font-weight-bold text-gray-800">
                                    {{ number_format($actualPercentage, 2) }}%
                                    <small class="text-muted">(Target: {{ $targetPercentage }}%)</small>
                                    @if ($achievement >= 100)
                                        <span class="badge badge-success ml-2">Tercapai</span>
                                    @elseif ($achievement >= 75)
                                        <span class="badge badge-warning ml-2">Mendekati Target</span>
                                    @else
                                        <span class="badge badge-danger ml-2">Belum Tercapai</span>
                                    @endif
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar 
                                        @if ($achievement >= 100) bg-success
                                        @elseif ($achievement >= 75) bg-warning
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

        <!-- KPI Radar Chart Row -->
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
                                <h6 class="font-weight-bold text-primary mb-3">Detail Capaian KPI</h6>
                                <div class="kpi-legend" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($kpiRadarData as $index => $kpi)
                                        <div class="kpi-item mb-3 p-3 border rounded"
                                            style="background-color: {{ $kpi['persentase'] >= 100 ? '#d4edda' : ($kpi['persentase'] >= 75 ? '#fff3cd' : '#f8d7da') }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1 font-weight-bold text-dark">{{ $kpi['kode'] }}</h6>
                                                <span
                                                    class="badge badge-{{ $kpi['persentase'] >= 100 ? 'success' : ($kpi['persentase'] >= 75 ? 'warning' : 'danger') }}">
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
                                                    <strong>Capaian:</strong><br>
                                                    <span class="text-success">{{ number_format($kpi['capaian']) }}
                                                        {{ $kpi['satuan'] }}</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $kpi['persentase'] >= 100 ? 'success' : ($kpi['persentase'] >= 75 ? 'warning' : 'danger') }}"
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
                                        <div class="col-md-3">
                                            <h5 class="mb-1">{{ count($kpiRadarData) }}</h5>
                                            <small>Total KPI</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="mb-1 text-success">
                                                {{ collect($kpiRadarData)->where('persentase', '>=', 100)->count() }}</h5>
                                            <small>Tercapai (≥100%)</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="mb-1 text-warning">
                                                {{ collect($kpiRadarData)->whereBetween('persentase', [75, 99.9])->count() }}
                                            </h5>
                                            <small>Hampir Tercapai (75-99%)</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="mb-1 text-danger">
                                                {{ collect($kpiRadarData)->where('persentase', '<', 75)->count() }}</h5>
                                            <small>Belum Tercapai (<75%)< /small>
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
            // KPI Radar Chart
            document.addEventListener('DOMContentLoaded', function() {
                const kpiData = @json($kpiRadarData);

                // Prepare data for radar chart
                const labels = kpiData.map(item => item.kode);

                // Normalize data for radar chart (max value determines 100%)
                const maxTarget = Math.max(...kpiData.map(item => item.target));
                const maxCapaian = Math.max(...kpiData.map(item => item.capaian));
                const chartMax = Math.max(maxTarget, maxCapaian);

                // Calculate percentage for chart display
                const targetData = kpiData.map(item => (item.target / chartMax) * 100);
                const capaianData = kpiData.map(item => (item.capaian / chartMax) * 100);

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
                            fill: true
                        }, {
                            label: 'Capaian Aktual',
                            data: capaianData,
                            backgroundColor: 'rgba(28, 200, 138, 0.2)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                                            const persentase = kpi.target > 0 ? Math.round((kpi.capaian /
                                                kpi.target) * 100) : 0;
                                            return [
                                                `${context.dataset.label}: ${kpi.capaian.toLocaleString()} ${kpi.satuan}`,
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
                    const newMaxCapaian = Math.max(...newKpiData.map(item => item.capaian));
                    const newChartMax = Math.max(newMaxTarget, newMaxCapaian);

                    // Update data
                    const newTargetData = newKpiData.map(item => (item.target / newChartMax) * 100);
                    const newCapaianData = newKpiData.map(item => (item.capaian / newChartMax) * 100);

                    // Update chart datasets
                    radarChart.data.datasets[0].data = newTargetData;
                    radarChart.data.datasets[1].data = newCapaianData;

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
            });
        </script>
    @endpush

@endsection
