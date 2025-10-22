@extends('inqa.layouts.main')

@section('title', 'Dashboard InQA')

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        /* Force CSS application with higher specificity */
        .container-fluid .card.modern-card,
        .container-fluid .card.statistics-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        }

        .container-fluid .card.modern-card:hover,
        .container-fluid .card.statistics-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
        }

        .chart-radar {
            position: relative;
            height: 350px;
            overflow: hidden;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.02) 0%, rgba(28, 200, 138, 0.02) 100%);
        }

        #statTotalPengabdian,
        #statDosenTerlibat,
        #statDenganMahasiswa {
            font-size: 20px !important;
            /* Ganti ukuran sesuai keinginan */
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

        .progress {
            height: 15px;
        }

        .kpi-progress-item .d-flex {
            margin-bottom: 0.25rem !important;
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

        /* Responsive adjustments */
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

            .statistics-card,
            .modern-card {
                margin-bottom: 0.75rem;
            }

            .statistics-card .card-body,
            .modern-card .card-body {
                padding: 0.75rem 1rem !important;
            }

            .statistics-card .text-xs,
            .modern-card .text-xs {
                font-size: 0.7rem !important;
            }

            .statistics-card .h5,
            .modern-card .h5 {
                font-size: 1.3rem !important;
            }

            .statistics-card .text-muted,
            .modern-card .text-muted {
                font-size: 0.65rem !important;
            }

            .statistics-card .font-weight-bold,
            .modern-card .font-weight-bold {
                font-size: 0.7rem !important;
            }

            .statistics-card .fa-2x,
            .modern-card .fa-2x {
                font-size: 1.8em !important;
            }
        }

        /* Extra small screens */
        @media (max-width: 576px) {

            .statistics-card .card-body,
            .modern-card .card-body {
                padding: 0.5rem 0.75rem !important;
            }

            .statistics-card .text-xs,
            .modern-card .text-xs {
                font-size: 0.65rem !important;
            }

            .statistics-card .h5,
            .modern-card .h5 {
                font-size: 1.1rem !important;
            }

            .statistics-card .text-muted,
            .modern-card .text-muted,
            .statistics-card .font-weight-bold,
            .modern-card .font-weight-bold {
                font-size: 0.65rem !important;
            }

            .statistics-card .fa-2x,
            .modern-card .fa-2x {
                font-size: 1.5em !important;
            }

            .statistics-card .badge,
            .modern-card .badge {
                font-size: 0.6rem !important;
                padding: 0.2rem 0.4rem !important;
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

        /* Modern Card Styling - Applies to both statistics-card and modern-card */
        .statistics-card,
        .modern-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left-width: 0.25rem !important;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .statistics-card:hover,
        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
        }

        /* Ensure consistent card heights across all layouts */
        .row .col-xl-4 .card.h-100,
        .row .col-lg-6 .card.h-100,
        .row .col-md-6 .card.h-100,
        .row .col-md-12 .card.h-100 {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .row .col-xl-4 .card.h-100 .card-body,
        .row .col-lg-6 .card.h-100 .card-body,
        .row .col-md-6 .card.h-100 .card-body,
        .row .col-md-12 .card.h-100 .card-body {
            flex: 1 !important;
        }

        /* Statistics row specific height consistency */
        .statistics-row .card.h-100 {
            min-height: 140px !important;
        }

        /* Main content cards height consistency */
        .main-content-row .card.h-100 {
            min-height: 450px !important;
        }

        .statistics-card .card-body,
        .modern-card .card-body {
            padding: 1.2rem 1.5rem !important;
        }

        /* Enhanced Font Sizes for Statistics Cards */
        .statistics-card .text-xs,
        .modern-card .text-xs {
            font-size: 0.8rem !important;
            line-height: 1.4 !important;
            font-weight: 600 !important;
        }

        .statistics-card .h5,
        .modern-card .h5 {
            font-size: 1.6rem !important;
            margin-bottom: 0.5rem !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
        }

        .statistics-card .text-muted,
        .modern-card .text-muted {
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
        }

        .statistics-card .badge,
        .modern-card .badge {
            font-size: 0.75rem !important;
            padding: 0.3rem 0.6rem !important;
            font-weight: 600 !important;
        }

        .statistics-card .font-weight-bold,
        .modern-card .font-weight-bold {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
        }

        .statistics-card .fa-2x,
        .modern-card .fa-2x {
            font-size: 2.2em !important;
        }

        .tooltip-icon {
            opacity: 0.7;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tooltip-icon:hover {
            opacity: 1;
            color: #4e73df !important;
            transform: scale(1.15);
            filter: drop-shadow(0 2px 4px rgba(78, 115, 223, 0.3));
        }

        .clickable-stat {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .clickable-stat:hover {
            color: #4e73df !important;
            text-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
        }

        .clickable-stat-number {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .clickable-stat-number:hover {
            color: #4e73df !important;
            text-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
        }

        /* Modal Fix - Prevent interference with card animations */
        .modal {
            pointer-events: auto !important;
        }

        .modal-backdrop {
            pointer-events: auto !important;
        }

        .modal-dialog {
            pointer-events: auto !important;
            transition: transform 0.3s ease-out !important;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out !important;
            transform: translate(0, -50px) !important;
        }

        .modal.show .modal-dialog {
            transform: none !important;
        }

        /* Prevent card hover effects when modal is open */
        body.modal-open .modern-card:hover,
        body.modal-open .statistics-card:hover {
            transform: none !important;
            box-shadow: none !important;
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
            transform: scale(1.05) translateY(-1px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.25);
        }

        .btn-group .btn:hover:not(.active) {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Card Enhancement - Force application */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06) !important;
        }

        .card:hover:not(.no-hover) {
            transform: translateY(-3px) !important;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
        }

        /* Interactive Statistics Cards */
        .clickable-stat {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            margin: -8px -12px;
        }

        .clickable-stat:hover {
            background: rgba(78, 115, 223, 0.1);
            color: #4e73df !important;
            text-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
            transform: scale(1.05);
        }

        .clickable-stat:active {
            transform: scale(0.98);
        }

        /* Modal enhancements */
        .modal-xl {
            max-width: 95%;
        }

        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            border-bottom: none;
        }

        .table th {
            font-weight: 600;
            color: #4e73df;
            border-bottom-width: 2px;
            background-color: #f8f9fc;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            padding: 0.25rem 0.5rem;
        }

        .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border: none;
            color: #4e73df;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            border: none;
        }

        /* Pulse animation for clickable stats */
        .clickable-stat::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(78, 115, 223, 0.3);
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }

        .clickable-stat:hover::before {
            width: 100%;
            height: 100%;
        }

        .statistics-card .clickable-stat {
            position: relative;
            overflow: hidden;
        }

        /* Treemap Styles */
        #jenisLuaranTreemap {
            border-radius: 8px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        }

        .treemap-tooltip {
            font-family: 'Nunito', sans-serif !important;
            font-size: 12px !important;
            line-height: 1.4 !important;
        }

        #jenisLuaranTreemap svg {
            display: block;
            margin: 0 auto;
        }

        #jenisLuaranTreemap rect {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #jenisLuaranTreemap text {
            pointer-events: none;
            user-select: none;
        }

        /* Sparkline Chart Styles */
        .sparkline-container {
            height: 40px;
            margin-top: 8px;
            margin-bottom: 8px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .sparkline-container:hover {
            opacity: 1;
        }

        .sparkline-chart {
            height: 100%;
            width: 100%;
        }

        .sparkline-chart canvas {
            display: block !important;
        }

        .statistics-card .sparkline-container {
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px;
        }

        .border-left-primary .sparkline-container {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
        }

        .border-left-warning .sparkline-container {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
        }

        .border-left-info .sparkline-container {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
        }

        /* Fix sidebar dan footer untuk zoom out */
        #wrapper {
            min-height: 100vh !important;
        }

        /* Sidebar harus meregang penuh ke bawah */
        .sidebar {
            min-height: 100vh !important;
            position: fixed !important;
            height: 100% !important;
        }

        /* Content wrapper mengakomodasi sidebar */
        #content-wrapper {
            min-height: 100vh !important;
            margin-left: 224px !important;
            /* Lebar sidebar default */
        }

        /* Responsive sidebar untuk mobile */
        @media (max-width: 768px) {
            #content-wrapper {
                margin-left: 0 !important;
            }
        }

        /* Footer styling - match default SB Admin 2 height */
        footer.sticky-footer {
            background-color: #fff !important;
            padding: 1.25rem 0 !important;
            border-top: 1px solid #e3e6f0 !important;
        }

        footer.sticky-footer .container {
            padding: 0 1.5rem !important;
        }

        footer.sticky-footer .copyright {
            font-size: 0.8rem !important;
            color: #5a5c69 !important;
        }

        /* Fix overflow */
        html,
        body {
            overflow-x: hidden !important;
        }

        #content-wrapper {
            min-height: unset !important;
        }

        #content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* 2. Ini adalah perintah kuncinya: perintahkan area konten untuk tumbuh */
        #content {
            flex-grow: 1;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Pengabdian</h1>
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
        <div class="row mb-4 statistics-row">
            <!-- Total Pengabdian Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                {{-- JUDUL KARTU --}}
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengabdian
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                </div>

                                {{-- ANGKA UTAMA --}}
                                <div id="statTotalPengabdian" class="h5 mb-2 font-weight-bold text-gray-800 clickable-stat"
                                    style="cursor: pointer;">
                                    {{ $stats['total_pengabdian'] }}
                                </div>

                                {{-- SPARKLINE CHART --}}
                                <div class="sparkline-container">
                                    <canvas id="sparklinePengabdian" class="sparkline-chart"></canvas>
                                </div>

                                {{-- INFORMASI TREN (PERBANDINGAN TAHUN) --}}
                                <div class="d-flex align-items-center mb-2">
                                    @if ($stats['percentage_change_pengabdian'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_pengabdian'] > 0 ? 'success' : 'danger' }} mr-2">
                                            <i
                                                class="fas {{ $stats['percentage_change_pengabdian'] > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                            {{ $stats['percentage_change_pengabdian'] > 0 ? '+' : '' }}{{ $stats['percentage_change_pengabdian'] }}%
                                        </span>
                                    @endif
                                    <small class="text-muted">{{ $stats['year_label'] }}</small>
                                </div>

                                {{-- RINCIAN SEKUNDER (PER PRODI) --}}
                                <div class="text-xs text-muted">
                                    <span>Kolaborasi:
                                        <strong>{{ $stats['pengabdian_kolaborasi'] }}</strong></span>
                                    <span class="mx-2">•</span>
                                    <span>IT: <strong>{{ $stats['pengabdian_khusus_informatika'] }}</strong></span>
                                    <span class="mx-2">•</span>
                                    <span>SI: <strong>{{ $stats['pengabdian_khusus_sistem_informasi'] }}</strong></span>
                                </div>
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
                                {{-- 1. JUDUL KARTU --}}
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Dosen Terlibat
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Jumlah dosen yang terlibat dalam pengabdian {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"></i>
                                </div>

                                {{-- 2. ANGKA UTAMA (HERO) --}}
                                <div id="statDosenTerlibat" class="h5 mb-2 font-weight-bold text-gray-800 clickable-stat"
                                    style="cursor: pointer;">
                                    {{ $stats['total_dosen'] }}
                                </div>

                                {{-- SPARKLINE CHART --}}
                                <div class="sparkline-container">
                                    <canvas id="sparklineDosen" class="sparkline-chart"></canvas>
                                </div>

                                {{-- 3. INFORMASI TREN (SUB-JUDUL UTAMA) --}}
                                <div class="d-flex align-items-center mb-3">
                                    @if ($stats['percentage_change_dosen'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_dosen'] > 0 ? 'success' : 'danger' }} mr-2">
                                            <i
                                                class="fas {{ $stats['percentage_change_dosen'] > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                                            {{ $stats['percentage_change_dosen'] > 0 ? '+' : '' }}{{ $stats['percentage_change_dosen'] }}%
                                        </span>
                                    @endif
                                    <small class="text-muted">{{ $stats['year_label'] }}</small>
                                </div>

                                {{-- 4. DETAIL SEKUNDER (Rincian) --}}
                                <div class="text-xs text-muted">
                                    {{-- Rincian dari Total Dosen FTI --}}
                                    @if (isset($stats['total_dosen_keseluruhan']) && $stats['total_dosen_keseluruhan'] > 0)
                                        @php
                                            $participationRate = round(
                                                ($stats['total_dosen'] / $stats['total_dosen_keseluruhan']) * 100,
                                                1,
                                            );
                                        @endphp
                                        <div class="mb-2">
                                            <span class="font-weight-bold">{{ $stats['total_dosen'] }}</span> dari
                                            {{ $stats['total_dosen_keseluruhan'] }} Dosen FTI ({{ $participationRate }}%)
                                        </div>
                                    @endif

                                    {{-- Rincian Per Prodi --}}
                                    <div>
                                        <span> IT:
                                            <strong>{{ $stats['dosen_informatika'] }}</strong></span>
                                        <span class="mx-2">•</span>
                                        <span> SI:
                                            <strong>{{ $stats['dosen_sistem_informasi'] }}</strong></span>
                                    </div>
                                </div>
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
                                {{-- 1. JUDUL KARTU (Warna disesuaikan menjadi text-info) --}}
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    PkM DENGAN MAHASISWA
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Persentase pengabdian yang melibatkan mahasiswa {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"></i>
                                </div>

                                {{-- 2. ANGKA UTAMA (HERO) --}}
                                <div id="statDenganMahasiswa" class="h5 mb-2 font-weight-bold text-gray-800 clickable-stat"
                                    style="cursor: pointer;">
                                    {{ $stats['persentase_pengabdian_dengan_mahasiswa'] }}%
                                </div>

                                {{-- SPARKLINE CHART --}}
                                <div class="sparkline-container">
                                    <canvas id="sparklineMahasiswa" class="sparkline-chart"></canvas>
                                </div>

                                {{-- 3. INFORMASI TREN (SUB-JUDUL UTAMA) --}}
                                <div class="d-flex align-items-center mb-3">
                                    @if (isset($stats['percentage_change_mahasiswa']) && $stats['percentage_change_mahasiswa'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_mahasiswa'] > 0 ? 'success' : 'danger' }} mr-2"
                                            data-toggle="tooltip"
                                            title="Perubahan persentase keterlibatan mahasiswa dari {{ $stats['previous_year'] }}: {{ $stats['percentage_change_mahasiswa'] > 0 ? 'Peningkatan' : 'Penurunan' }} {{ abs($stats['percentage_change_mahasiswa']) }}%">
                                            <i
                                                class="fas {{ $stats['percentage_change_mahasiswa'] > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                                            {{ $stats['percentage_change_mahasiswa'] > 0 ? '+' : '' }}{{ $stats['percentage_change_mahasiswa'] }}%
                                        </span>
                                    @elseif (isset($stats['percentage_change_mahasiswa']) && $stats['percentage_change_mahasiswa'] == 0)
                                        <span class="badge badge-secondary mr-2" data-toggle="tooltip"
                                            title="Tidak ada perubahan persentase keterlibatan mahasiswa dari tahun sebelumnya">
                                            <i class="fas fa-minus mr-1"></i>
                                            0%
                                        </span>
                                    @elseif ($filterYear == 'all')
                                        <span class="badge badge-info mr-2" data-toggle="tooltip"
                                            title="Menampilkan data keseluruhan tahun">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Semua Tahun
                                        </span>
                                    @else
                                        <span class="badge badge-warning mr-2" data-toggle="tooltip"
                                            title="Data tahun sebelumnya tidak tersedia untuk perbandingan">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Data Baru
                                        </span>
                                    @endif
                                    <small class="text-muted">{{ $stats['year_label'] ?? 'vs tahun sebelumnya' }}</small>
                                </div>

                                {{-- 4. DETAIL SEKUNDER (Rincian) --}}
                                <div class="text-xs text-muted">
                                    {{-- Rincian Jumlah Pengabdian --}}
                                    <div class="mb-2" data-toggle="tooltip"
                                        title="{{ $stats['total_mahasiswa'] }} dari {{ $stats['total_pengabdian'] }} pengabdian melibatkan mahasiswa">
                                        <span class="font-weight-bold">{{ $stats['total_mahasiswa'] }} dari
                                            {{ $stats['total_pengabdian'] }}</span>
                                        pengabdian melibatkan mahasiswa
                                    </div>

                                    {{-- Rincian Per Prodi --}}
                                    <div>
                                        <span> IT:
                                            <strong>{{ $stats['mahasiswa_informatika'] }}</strong></span>
                                        <span class="mx-2">•</span>
                                        <span> SI:
                                            <strong>{{ $stats['mahasiswa_sistem_informasi'] }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jenis Luaran Treemap & KPI Progress Bar Row -->
        <div class="row mb-4 main-content-row">

            <!-- KPI Radar Chart -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow modern-card h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <i class="fas fa-chart-radar mr-2"></i>Capaian KPI Komparatif
                            @if ($filterYear !== 'all')
                                <span class="text-primary">({{ $filterYear }})</span>
                            @else
                                <small class="text-muted">(Semua Tahun)</small>
                            @endif
                        </h6>
                        <div class="d-flex align-items-center">
                            @php
                                $totalKpi = count($kpiRadarData);
                                $tercapai = collect($kpiRadarData)
                                    ->filter(function ($kpi) {
                                        return $kpi['skor_normalisasi'] >= 100;
                                    })
                                    ->count();
                            @endphp
                            <span class="badge badge-success mr-1">{{ $tercapai }} Tercapai</span>
                            <span class="badge badge-warning">{{ $totalKpi - $tercapai }} Belum Tercapai</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Radar Chart Canvas -->
                        <div class="chart-container mb-3" style="height: 350px;">
                            <canvas id="kpiRadarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sumber Dana Chart -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow modern-card h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Sumber Dana Pengabdian
                            @if ($filterYear != 'all')
                                <span class="text-primary">({{ $filterYear - 1 }} vs {{ $filterYear }})</span>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar" style="height: 400px;">
                            <canvas id="fundingSourcesChart" width="100%" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funding Sources Chart Section -->
        <div class="row mb-4 main-content-row">

            <div class="col-lg-6 col-md-12 mb-4">
                @php
                    $allDosenCount = count($namaDosen ?? []);
                    $maxCanvasHeight = max(100, $allDosenCount * 35); // Reduced height per item
                @endphp
                <div class="card shadow mb-4 modern-card h-100">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <i class="fas fa-chart-bar mr-2"></i>Rekap Pengabdian per Dosen
                                    <span class="text-primary">{{ $allDosenCount }} Dosen
                                        @if ($filterYear !== 'all')
                                            ({{ $filterYear }})
                                        @endif
                                    </span>
                                </h6>
                            </div>
                            <div class="col-md-6 text-right">
                                <button id="dosenSortBtn" type="button" class="btn btn-sm btn-outline-secondary"
                                    data-order="desc" title="Urutkan jumlah (tertinggi ke terendah)">
                                    <i class="fas fa-sort-amount-down mr-1"></i>Urutkan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Dosen Chart Container - 8 Teratas + Lainnya -->
                        <div id="dosenChartContainer" class="chart-container">
                            <div class="chart-bar-scrollable"
                                style="max-height: 310px; overflow-y: auto; overflow-x: hidden; border-radius: 8px; padding: 15px; background-color: transparent;">

                                <div style="height: {{ $maxCanvasHeight }}px; min-width: 700px;">
                                    <canvas id="dosenChart" width="100%" height="{{ $maxCanvasHeight }}"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow modern-card h-100">
                    <div class="card-header py-3">
                        <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Distribusi Jenis Luaran
                            @if ($filterYear !== 'all')
                                <span class="text-primary">({{ $filterYear }})</span>
                            @else
                                <small class="text-muted">(Semua Tahun)</small>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (count($jenisLuaranData) > 0)
                            <div id="jenisLuaranTreemap" style="height: 350px; width: 100%;"></div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Total:</strong> {{ array_sum(array_column($jenisLuaranData, 'value')) }} luaran
                                </small>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-chart-area fa-3x mb-3"></i>
                                    <div class="h6">Belum ada data luaran</div>
                                    <p class="text-muted small">Data jenis luaran akan muncul di sini</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Statistics Detail Modal -->
    <div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog" aria-labelledby="statisticsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-ligt">
                    <h5 class="modal-title text-white" id="statisticsModalLabel">
                        <i class="fas fa-chart-line mr-2"></i>Detail Statistik
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- D3.js for Treemap -->
        <script src="https://d3js.org/d3.v7.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

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

                // Load sparkline charts
                loadSparklineCharts();

                // Initialize KPI Radar Chart
                initKpiRadarChart();

                // Add click handlers for statistics cards
                $('#statTotalPengabdian').click(function() {
                    showStatisticsModal('pengabdian', 'Total Pengabdian');
                });

                $('#statDosenTerlibat').click(function() {
                    showStatisticsModal('dosen', 'Dosen Terlibat');
                });

                $('#statDenganMahasiswa').click(function() {
                    showStatisticsModal('mahasiswa', 'Pengabdian dengan Mahasiswa');
                });
            });


            // === SPARKLINE CHARTS ===
            function loadSparklineCharts() {
                // Load sparkline data from API
                fetch('{{ route('inqa.api.sparkline-data') }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Sparkline data received:', data); // Debug log
                        createSparkline('sparklinePengabdian', data.pengabdian, '#4e73df', data.years);
                        createSparkline('sparklineDosen', data.dosen, '#4e73df', data.years);
                        createSparkline('sparklineMahasiswa', data.mahasiswa, '#4e73df', data.years);
                    })
                    .catch(error => {
                        console.error('Error loading sparkline data:', error);
                        // Create dummy data if API fails (use yearly data instead of monthly)
                        const currentYear = new Date().getFullYear();
                        const dummyYears = Array.from({
                            length: 5
                        }, (_, i) => currentYear - 4 + i);
                        const dummyData = Array.from({
                            length: 5
                        }, () => Math.floor(Math.random() * 20) + 5);
                        createSparkline('sparklinePengabdian', dummyData, '#4e73df', dummyYears);
                        createSparkline('sparklineDosen', dummyData, '#4e73df', dummyYears);
                        createSparkline('sparklineMahasiswa', dummyData, '#4e73df', dummyYears);
                    });
            }

            function createSparkline(canvasId, data, color, years) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;

                // Destroy existing chart if it exists
                const existingChart = Chart.getChart(canvasId);
                if (existingChart) {
                    existingChart.destroy();
                }

                const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 40);
                gradient.addColorStop(0, color + '40');
                gradient.addColorStop(1, color + '10');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map((_, i) => ''),
                        datasets: [{
                            data: data,
                            borderColor: color,
                            backgroundColor: gradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 0,
                            pointBackgroundColor: 'transparent',
                            pointBorderColor: 'transparent',
                            pointBorderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'none'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            },
                            datalabels: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false,
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // === KPI RADAR CHART ===
            function initKpiRadarChart() {
                const kpiData = @json($kpiRadarData);

                if (!kpiData || kpiData.length === 0) {
                    console.warn('No KPI data available for radar chart');
                    return;
                }

                const ctx = document.getElementById('kpiRadarChart');
                if (!ctx) {
                    console.warn('KPI Radar Chart canvas not found');
                    return;
                }

                const existingChart = Chart.getChart('kpiRadarChart');
                if (existingChart) {
                    existingChart.destroy();
                }

                // --- PERUBAHAN 1: Buat mapping untuk label yang lebih mudah dibaca ---
                const kpiLabelsMap = {
                    'PGB.I.1.1': 'Luaran Sesuai',
                    'PGB.I.5.6': 'Peningkatan PkM',
                    'PGB.I.7.4': 'Dana Eksternal',
                    'PGB.I.7.9': 'Proposal Diterima',
                    'IKT.I.5.g': 'Digunakan di PBM',
                    'IKT.I.5.h': 'Bidang INFOKOM',
                    'IKT.I.5.i': 'HKI per Prodi',
                    'IKT.I.5.j': 'Keterlibatan Mhs.'
                };

                // Gunakan mapping untuk membuat label
                const labels = kpiData.map(kpi => kpiLabelsMap[kpi.kode] || kpi.kode);
                const actualScores = kpiData.map(kpi => kpi.skor_normalisasi);
                const benchmarkScores = kpiData.map(() => 100);

                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Capaian Aktual',
                                data: actualScores,
                                backgroundColor: 'rgba(28, 200, 138, 0.2)', // Hijau
                                borderColor: 'rgba(28, 200, 138, 1)',
                                pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                                pointBorderColor: '#fff',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: true,
                                datalabels: {
                                    display: false // Ini akan menyembunyikan angka capaian
                                }

                            },
                            {
                                label: 'Target',
                                data: benchmarkScores, // Kuning // Tidak akan ada warna isian
                                borderColor: 'rgba(246, 194, 62)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                // --- PERUBAHAN 2: Sederhanakan garis target ---
                                fill: false,
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                datalabels: {
                                    display: false // Ini akan menyembunyikan angka 100
                                }

                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: false
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return kpiData[context[0].dataIndex].indikator;
                                    },
                                    label: function(context) {
                                        // --- PERUBAHAN 3: Perbaiki akses data di tooltip ---
                                        if (context.datasetIndex === 1) {
                                            return 'Target Universal: 100%';
                                        }

                                        const kpi = kpiData[context.dataIndex];
                                        let persentaseCapaian = 0;
                                        if (kpi.target > 0) {
                                            persentaseCapaian = (kpi.realisasi / kpi.target) * 100;
                                        }

                                        let statusLabel = `(${persentaseCapaian.toFixed(1)}% dari Target)`;
                                        if (kpi.skor_normalisasi >= 100 && persentaseCapaian > 100) {
                                            statusLabel = `(Terlampaui: ${persentaseCapaian.toFixed(1)}%)`;
                                        }

                                        return [
                                            `Skor Capaian: ${kpi.skor_normalisasi.toFixed(1)}%`,
                                            `Realisasi: ${kpi.realisasi}${kpi.satuan} ${statusLabel}`,
                                            `Target: ${kpi.target}${kpi.satuan}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                min: 0,
                                // --- PERUBAHAN 4: Kembalikan max ke 100 untuk kejelasan ---
                                max: 100,
                                ticks: {
                                    stepSize: 20,
                                    callback: value => value + '%'
                                },
                                pointLabels: {
                                    font: {
                                        size: 12
                                    },
                                    padding: 10

                                }
                            }
                        }
                    }
                });
            }

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

            // Function to truncate long names
            function truncateName(name, maxLength = 15) {
                if (name.length <= maxLength) return name;

                // Split by spaces and take first word + initial of second word
                const parts = name.split(' ');
                if (parts.length > 1) {
                    return parts[0] + ' ' + parts[1].charAt(0) + '.';
                }

                // If single word, truncate with ellipsis
                return name.substring(0, maxLength - 3) + '...';
            }

            // 3. Fungsi untuk membuat chart dengan 8 teratas + lainnya
            function createDosenChart(labels, data) {
                const ctx = document.getElementById('dosenChart').getContext('2d');

                const existingChart = Chart.getChart('dosenChart');
                if (existingChart) {
                    existingChart.destroy();
                }

                // Truncate labels for display
                const truncatedLabels = labels.map(label => truncateName(label));

                // Buat warna dengan pembedaan untuk 8 teratas
                const backgroundColors = data.map((value, index) => {
                    const baseColor = [78, 115, 223]; // RGB untuk warna biru dasar #4e73df
                    const maxValue = Math.max(...data);

                    if (maxValue === 0) return `rgb(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]})`;

                    // 8 teratas mendapat opacity lebih tinggi
                    let minOpacity, maxOpacity;
                    if (index < 8) {
                        // Top 8: opacity lebih tinggi (lebih solid)
                        minOpacity = 0.7;
                        maxOpacity = 1.0;
                    } else {
                        // Lainnya: opacity lebih rendah (lebih transparan)
                        minOpacity = 0.3;
                        maxOpacity = 0.5;
                    }

                    const opacity = minOpacity + (maxOpacity - minOpacity) * (value / maxValue);
                    return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${opacity.toFixed(2)})`;
                });

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels.map(label => truncateName(label)),
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: data,
                            backgroundColor: backgroundColors,
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
                                        size: 11
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
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        return labels[index];
                                    },
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

            // 4. Fungsi untuk membuat All chart
            function createAllChart(labels, data) {
                const ctx = document.getElementById('allDosenChart').getContext('2d');

                const existingChart = Chart.getChart('allDosenChart');
                if (existingChart) {
                    existingChart.destroy();
                }

                const backgroundColors = data.map(value => {
                    const baseColor = [78, 115, 223];
                    const maxValue = Math.max(...data);
                    if (maxValue === 0) return `rgb(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]})`;

                    const minOpacity = 0.4;
                    const maxOpacity = 1.0;
                    const opacity = minOpacity + (maxOpacity - minOpacity) * (value / maxValue);
                    return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${opacity.toFixed(2)})`;
                });

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels.map(label => truncateName(label)),
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: data,
                            backgroundColor: backgroundColors,
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
                                        size: 11
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
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        return labels[index];
                                    },
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

            // 5. Update charts function
            function updateCharts() {
                const labels = originalData.map(d => d.nama);
                const counts = originalData.map(d => d.jumlah);

                if (currentView === 'top8') {
                    const top8Labels = labels.slice(0, 8);
                    const top8Counts = counts.slice(0, 8);
                    createTop8Chart(top8Labels, top8Counts);
                } else {
                    createAllChart(labels, counts);
                }
            }

            // 6. Toggle view function
            function toggleView(view) {
                currentView = view;

                if (view === 'top8') {
                    document.getElementById('top8ChartContainer').style.display = 'block';
                    document.getElementById('allChartContainer').style.display = 'none';
                    document.getElementById('showTop8Btn').classList.remove('btn-outline-primary');
                    document.getElementById('showTop8Btn').classList.add('btn-primary');
                    document.getElementById('showAllBtn').classList.remove('btn-primary');
                    document.getElementById('showAllBtn').classList.add('btn-outline-primary');
                } else {
                    document.getElementById('top8ChartContainer').style.display = 'none';
                    document.getElementById('allChartContainer').style.display = 'block';
                    document.getElementById('showAllBtn').classList.remove('btn-outline-primary');
                    document.getElementById('showAllBtn').classList.add('btn-primary');
                    document.getElementById('showTop8Btn').classList.remove('btn-primary');
                    document.getElementById('showTop8Btn').classList.add('btn-outline-primary');
                }

                updateCharts();
            }

            // 4. Update chart function
            function updateChart() {
                const labels = originalData.map(d => d.nama);
                const counts = originalData.map(d => d.jumlah);
                createDosenChart(labels, counts);
            }

            // 5. Event listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Initial sort and display
                originalData.sort((a, b) => b.jumlah - a.jumlah);
                updateChart();

                // Sort button
                const dosenSortBtn = document.getElementById('dosenSortBtn');
                dosenSortBtn.addEventListener('click', function() {
                    const currentOrder = this.dataset.order;
                    const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';

                    originalData.sort((a, b) => (newOrder === 'asc' ? a.jumlah - b.jumlah : b.jumlah - a
                        .jumlah));

                    this.dataset.order = newOrder;
                    this.innerHTML = newOrder === 'asc' ?
                        '<i class="fas fa-sort-amount-up mr-1"></i>Urutkan' :
                        '<i class="fas fa-sort-amount-down mr-1"></i>Urutkan';
                    this.title = newOrder === 'asc' ? 'Urutkan jumlah (terendah ke tertinggi)' :
                        'Urutkan jumlah (tertinggi ke terendah)';

                    updateChart();
                });
            });

            // === FUNDING SOURCES STACKED BAR CHART ===
            let fundingChart = null;

            function loadFundingSourcesChart() {
                // Get current year from the filter
                const currentYear = '{{ $filterYear }}';
                const url = '{{ route('inqa.api.funding-sources') }}' + '?year=' + currentYear;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        createFundingChart(data);
                    })
                    .catch(error => {
                        console.error('Error loading funding sources data:', error);

                        // Show error message on chart
                        const ctx = document.getElementById('fundingSourcesChart').getContext('2d');
                        if (fundingChart) {
                            fundingChart.destroy();
                        }
                        // Create empty chart with error message
                        fundingChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['No Data'],
                                datasets: [{
                                    label: 'Error Loading Data',
                                    data: [0],
                                    backgroundColor: '#e74a3b'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Error Loading Funding Data - Silakan refresh halaman',
                                        color: '#e74a3b'
                                    }
                                }
                            }
                        });
                    });
            }

            function createFundingChart(data) {
                const ctx = document.getElementById('fundingSourcesChart').getContext('2d');

                // Check if there's no data
                if (data.no_data) {
                    // Destroy existing chart if it exists
                    if (fundingChart) {
                        fundingChart.destroy();
                    }

                    // Create chart showing no data message
                    fundingChart = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: data.message || 'Tidak ada data sumber dana untuk periode ini',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    color: '#858796'
                                },
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(234, 236, 244, 0.5)'
                                    }
                                }
                            }
                        }
                    });
                    return;
                }

                // Destroy existing chart if it exists
                if (fundingChart) {
                    fundingChart.destroy();
                }

                // Format currency for labels
                function formatCurrency(value) {
                    if (value >= 1000000) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + ' Juta';
                    } else if (value >= 1000) {
                        return 'Rp ' + (value / 1000).toFixed(0) + ' Ribu';
                    } else {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }

                // Sort datasets by total contribution (sum of both years) for consistent ordering
                const sortedDatasets = data.datasets.sort((a, b) => {
                    const totalA = a.data.reduce((sum, val) => sum + (val || 0), 0);
                    const totalB = b.data.reduce((sum, val) => sum + (val || 0), 0);
                    return totalB - totalA; // Largest contributors first (at bottom of stack)
                });

                // Enhanced color palette with distinct colors for better differentiation
                const enhancedColors = [
                    '#1f77b4', // Strong Blue - Primary funding source
                    '#ff7f0e', // Orange - Secondary source
                    '#2ca02c', // Green - Third source  
                    '#d62728', // Red - Fourth source
                    '#9467bd', // Purple - Fifth source
                    '#8c564b', // Brown - Sixth source
                    '#e377c2', // Pink - Seventh source
                    '#7f7f7f', // Gray - Eighth source
                    '#bcbd22', // Olive - Ninth source
                    '#17becf' // Cyan - Tenth source
                ];

                // Apply consistent colors based on source priority
                sortedDatasets.forEach((dataset, index) => {
                    dataset.backgroundColor = enhancedColors[index % enhancedColors.length];
                    dataset.borderColor = enhancedColors[index % enhancedColors.length];
                    dataset.borderWidth = 1;

                    if (index === dataset.length - 1) {

                        dataset.borderRadius = {
                            topLeft: 10,
                            topRight: 10,
                            bottomLeft: 0,
                            bottomRight: 0
                        };
                    } else {

                        dataset.borderRadius = 0;
                    }
                });

                fundingChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels, // [Previous Year, Current Year]
                        datasets: sortedDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'point',
                            intersect: true,
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    color: '#5a5c69'
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(234, 236, 244, 0.3)',
                                    lineWidth: 1
                                },
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value);
                                    },
                                    font: {
                                        size: 11
                                    },
                                    color: '#858796'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: false,
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                color: '#2d3e50',
                                padding: {
                                    top: 15,
                                    bottom: 25
                                }
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    boxWidth: 15,
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: '#5a5c69',
                                    usePointStyle: false,
                                    generateLabels: function(chart) {
                                        const datasets = chart.data.datasets;
                                        return datasets.map((dataset, index) => {
                                            return {
                                                text: dataset.label,
                                                fillStyle: dataset.backgroundColor,
                                                strokeStyle: dataset.borderColor,
                                                lineWidth: dataset.borderWidth,
                                                hidden: false,
                                                index: index
                                            };
                                        });
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                                titleColor: '#2d3e50',
                                bodyColor: '#5a5c69',
                                footerColor: '#2d3e50',
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 2,
                                cornerRadius: 12,
                                displayColors: true,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12,
                                    weight: 'normal'
                                },
                                footerFont: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 12,
                                callbacks: {
                                    title: function(context) {
                                        return '📊 Tahun ' + context[0].label;
                                    },
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = formatCurrency(context.raw);
                                        const total = context.chart.data.datasets.reduce((sum, dataset) => {
                                            return sum + (dataset.data[context.dataIndex] || 0);
                                        }, 0);
                                        const percentage = ((context.raw / total) * 100).toFixed(1);
                                        return `💰 ${label}: ${value} (${percentage}%)`;
                                    },
                                    footer: function(tooltipItems) {
                                        let total = 0;
                                        tooltipItems.forEach(function(tooltipItem) {
                                            total += tooltipItem.raw;
                                        });
                                        return `📈 Total Dana: ${formatCurrency(total)}`;
                                    }
                                }
                            },
                            // Disable data labels on bars for clean "at a glance" view
                            datalabels: {
                                display: false
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }

            function refreshFundingChart() {
                console.log('Refreshing funding chart...');
                loadFundingSourcesChart();
            }

            // === JENIS LUARAN TREEMAP ===
            function createJenisLuaranChart() {
                const jenisLuaranData = @json($jenisLuaranData);

                if (!jenisLuaranData || jenisLuaranData.length === 0) {
                    return;
                }

                // Clear previous content
                d3.select("#jenisLuaranTreemap").selectAll("*").remove();

                // Set dimensions and margins
                const container = document.getElementById('jenisLuaranTreemap');
                const margin = {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                };
                const width = container.clientWidth - margin.left - margin.right;
                const height = 350 - margin.top - margin.bottom;

                // Modern color palette with subtle tones
                const colorScale = d3.scaleOrdinal([
                    '#6366f1', // Indigo
                    '#8b5cf6', // Purple
                    '#06b6d4', // Cyan
                    '#10b981', // Emerald
                    '#f59e0b', // Amber
                    '#ef4444', // Red
                    '#ec4899', // Pink
                    '#84cc16', // Lime
                    '#f97316', // Orange
                    '#3b82f6', // Blue
                    '#14b8a6', // Teal
                    '#a855f7' // Violet
                ]);

                // Prepare data for D3 treemap
                const data = {
                    name: "Jenis Luaran",
                    children: jenisLuaranData.map(item => ({
                        name: item.label,
                        value: item.value
                    }))
                };

                // Create SVG
                const svg = d3.select("#jenisLuaranTreemap")
                    .append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .style("font-family", "Nunito, sans-serif");

                const g = svg.append("g")
                    .attr("transform", `translate(${margin.left},${margin.top})`);

                // Create treemap layout
                const root = d3.hierarchy(data)
                    .sum(d => d.value)
                    .sort((a, b) => b.value - a.value);

                const treemap = d3.treemap()
                    .size([width, height])
                    .padding(2)
                    .round(true);

                treemap(root);

                // Calculate total for percentages
                const total = d3.sum(jenisLuaranData, d => d.value);

                // Create tooltip div
                const tooltip = d3.select("body").append("div")
                    .attr("class", "treemap-tooltip")
                    .style("opacity", 0)
                    .style("position", "absolute")
                    .style("text-align", "center")
                    .style("padding", "12px")
                    .style("font", "12px Nunito, sans-serif")
                    .style("background", "rgba(255, 255, 255, 0.95)")
                    .style("border", "1px solid #e5e7eb")
                    .style("border-radius", "8px")
                    .style("box-shadow", "0 4px 12px rgba(0, 0, 0, 0.15)")
                    .style("pointer-events", "none")
                    .style("z-index", "1000");

                // Create cells
                const leaf = g.selectAll("g")
                    .data(root.leaves())
                    .enter().append("g")
                    .attr("transform", d => `translate(${d.x0},${d.y0})`);

                // Add rectangles
                leaf.append("rect")
                    .attr("width", d => Math.max(0, d.x1 - d.x0))
                    .attr("height", d => Math.max(0, d.y1 - d.y0))
                    .attr("fill", (d, i) => colorScale(i))
                    .attr("stroke", "#ffffff")
                    .attr("stroke-width", 2)
                    .attr("rx", 6)
                    .style("cursor", "pointer")
                    .style("transition", "all 0.3s ease")
                    .on("mouseover", function(event, d) {
                        // Highlight effect
                        d3.select(this)
                            .attr("stroke-width", 3)
                            .style("filter", "brightness(1.1)");

                        const percentage = ((d.value / total) * 100).toFixed(1);

                        tooltip.transition()
                            .duration(200)
                            .style("opacity", .95);

                        tooltip.html(`
                            <div style="font-weight: bold; color: #1f2937; margin-bottom: 4px;">${d.data.name}</div>
                            <div style="color: #374151;">Jumlah: ${d.value} luaran</div>
                            <div style="color: #6b7280;">Persentase: ${percentage}%</div>
                        `)
                            .style("left", (event.pageX + 10) + "px")
                            .style("top", (event.pageY - 28) + "px");
                    })
                    .on("mouseout", function(d) {
                        // Remove highlight effect
                        d3.select(this)
                            .attr("stroke-width", 2)
                            .style("filter", "brightness(1)");

                        tooltip.transition()
                            .duration(500)
                            .style("opacity", 0);
                    });

                // Add text labels
                leaf.append("text")
                    .selectAll("tspan")
                    .data(d => {
                        const words = d.data.name.split(/\s+/);
                        const percentage = ((d.value / total) * 100).toFixed(1);
                        return words.concat([`${d.value}`, `(${percentage}%)`]);
                    })
                    .enter().append("tspan")
                    .attr("x", 4)
                    .attr("y", (d, i, nodes) => {
                        const parentRect = d3.select(nodes[0].parentNode.previousSibling);
                        const rectHeight = +parentRect.attr("height");
                        const lineHeight = 14;
                        const totalLines = nodes.length;
                        const startY = (rectHeight - totalLines * lineHeight) / 2 + lineHeight;
                        return startY + i * lineHeight;
                    })
                    .style("font-size", (d, i, nodes) => {
                        const parentRect = d3.select(nodes[0].parentNode.previousSibling);
                        const rectWidth = +parentRect.attr("width");
                        const rectHeight = +parentRect.attr("height");

                        // Dynamic font size based on rectangle size
                        if (rectWidth < 80 || rectHeight < 60) return "10px";
                        if (rectWidth < 120 || rectHeight < 80) return "11px";
                        return "12px";
                    })
                    .style("font-weight", (d, i, nodes) => {
                        // First lines are category name (bold), last two lines are value and percentage
                        const totalLines = nodes.length;
                        return i >= totalLines - 2 ? "bold" : "600";
                    })
                    .style("fill", "white")
                    .style("text-shadow", "0 1px 2px rgba(0,0,0,0.3)")
                    .text(d => d)
                    .each(function(d, i, nodes) {
                        // Hide text if rectangle is too small
                        const parentRect = d3.select(nodes[0].parentNode.previousSibling);
                        const rectWidth = +parentRect.attr("width");
                        const rectHeight = +parentRect.attr("height");

                        if (rectWidth < 60 || rectHeight < 40) {
                            d3.select(this).style("display", "none");
                        }
                    });

                // Add animation
                leaf.selectAll("rect")
                    .attr("width", 0)
                    .attr("height", 0)
                    .transition()
                    .duration(800)
                    .ease(d3.easeBackOut.overshoot(1.1))
                    .attr("width", d => Math.max(0, d.x1 - d.x0))
                    .attr("height", d => Math.max(0, d.y1 - d.y0));

                leaf.selectAll("text")
                    .style("opacity", 0)
                    .transition()
                    .delay(400)
                    .duration(600)
                    .style("opacity", 1);
            }

            // Load the chart when document is ready
            $(document).ready(function() {
                loadFundingSourcesChart();
                createJenisLuaranChart();

                // Handle window resize for treemap
                let resizeTimeout;
                $(window).resize(function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(function() {
                        createJenisLuaranChart(); // Recreate treemap on resize
                    }, 250);
                });
            });

            // Statistics Modal Functions
            function showStatisticsModal(type, title) {
                const currentYear = '{{ $filterYear }}';

                // Update modal title
                $('#statisticsModalLabel').html('<i class="fas fa-chart-line mr-2"></i>Detail ' + title +
                    (currentYear !== 'all' ? ' - Tahun ' + currentYear : ' - Semua Tahun'));

                // Show modal with enhanced loading state
                $('#statisticsModal').modal('show');
                $('#modalBody').html(`
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <h5 class="text-primary">Memuat Data ${title}</h5>
                        <p class="text-muted">Sedang mengumpulkan informasi detail...</p>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 style="width: 100%; background: linear-gradient(90deg, #4e73df, #36b9cc);"></div>
                        </div>
                    </div>
                `);
                $('#exportBtn').hide();

                // Make AJAX request to get detailed data
                $.ajax({
                    url: '{{ route('inqa.api.statistics-detail') }}',
                    method: 'GET',
                    data: {
                        type: type,
                        year: currentYear
                    },
                    timeout: 30000, // 30 second timeout
                    success: function(response) {
                        renderModalContent(type, response, title);
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Terjadi kesalahan saat mengambil data detail.';

                        if (status === 'timeout') {
                            errorMessage = 'Permintaan timeout. Server membutuhkan waktu terlalu lama.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Kesalahan server internal. Silakan coba lagi nanti.';
                        }

                        $('#modalBody').html(`
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                                <h5 class="text-warning mb-3">Gagal Memuat Data</h5>
                                <p class="text-muted mb-4">${errorMessage}</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-primary" onclick="showStatisticsModal('${type}', '${title}')">
                                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                                    </button>
                                    <button class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times mr-2"></i>Tutup
                                    </button>
                                </div>
                            </div>
                        `);
                    }
                });
            }

            function renderModalContent(type, data, title) {
                let html = '';

                // Summary statistics
                html += `<div class="row mb-4">`;
                html += `<div class="col-md-12">`;
                html += `<div class="card border-left-primary">`;
                html += `<div class="card-body">`;
                html += `<div class="row">`;

                if (type === 'pengabdian') {
                    html += `
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary font-weight-bold">${data.total}</h4>
                            <small class="text-muted">Total Pengabdian</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success font-weight-bold">${data.kolaborasi}</h4>
                            <small class="text-muted">Kolaborasi</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info font-weight-bold">${data.informatika}</h4>
                            <small class="text-muted">Informatika</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning font-weight-bold">${data.sistem_informasi}</h4>
                            <small class="text-muted">Sistem Informasi</small>
                        </div>
                    `;
                } else if (type === 'dosen') {
                    html += `
                        <div class="col-md-4 text-center">
                            <h4 class="text-primary font-weight-bold">${data.total}</h4>
                            <small class="text-muted">Total Dosen</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="text-info font-weight-bold">${data.informatika}</h4>
                            <small class="text-muted">Informatika</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="text-warning font-weight-bold">${data.sistem_informasi}</h4>
                            <small class="text-muted">Sistem Informasi</small>
                        </div>
                    `;
                } else if (type === 'mahasiswa') {
                    html += `
                        <div class="col-md-4 text-center">
                            <h4 class="text-primary font-weight-bold">${data.total}</h4>
                            <small class="text-muted">Total Pengabdian dengan Mahasiswa</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="text-info font-weight-bold">${data.informatika}</h4>
                            <small class="text-muted">Mahasiswa Informatika</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="text-warning font-weight-bold">${data.sistem_informasi}</h4>
                            <small class="text-muted">Mahasiswa Sistem Informasi</small>
                        </div>
                    `;
                }

                html += `</div></div></div></div></div>`;

                // Data table
                if (data.details && data.details.length > 0) {
                    html += `<div class="card">`;
                    html += `<div class="card-header bg-primary text-white">`;
                    html += `<h6 class="mb-0"><i class="fas fa-table mr-2"></i>Data Detail ${title}</h6>`;
                    html += `</div>`;
                    html += `<div class="card-body">`;
                    html += `<div class="table-responsive">`;
                    html += `<table class="table table-striped table-hover" id="detailTable">`;

                    // Table headers based on type
                    if (type === 'pengabdian') {
                        html += `
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul Pengabdian</th>
                                    <th>Tanggal</th>
                                    <th>Ketua</th>
                                    <th>Sumber Dana</th>
                                    <th>Prodi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;

                        data.details.forEach((item, index) => {
                            const statusClass = item.dengan_mahasiswa ? 'badge-success' : 'badge-secondary';
                            const statusText = item.dengan_mahasiswa ? 'Dengan Mahasiswa' : 'Tanpa Mahasiswa';

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="font-weight-bold text-primary">${item.judul}</div>
                                        <small class="text-muted">${item.id_pengabdian}</small>
                                    </td>
                                    <td>${new Date(item.tanggal_pengabdian).toLocaleDateString('id-ID')}</td>
                                    <td>${item.ketua}</td>
                                    <td><span class="badge badge-info">${item.sumber_dana}</span></td>
                                    <td><span class="badge badge-secondary">${item.kategori_prodi}</span></td>
                                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                                </tr>
                            `;
                        });

                    } else if (type === 'dosen') {
                        html += `
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Dosen</th>
                                    <th>NIK</th>
                                    <th>Program Studi</th>
                                    <th>Jumlah Pengabdian</th>
                                    <th>Jabatan</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;

                        data.details.forEach((item, index) => {
                            const prodiClass = item.prodi === 'Informatika' ? 'badge-info' : 'badge-warning';

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="font-weight-bold text-primary">${item.nama}</div>
                                        <small class="text-muted">NIDN: ${item.nidn || 'N/A'}</small>
                                    </td>
                                    <td><span class="badge badge-secondary">${item.nik}</span></td>
                                    <td><span class="badge ${prodiClass}">${item.prodi}</span></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">${item.jumlah_pengabdian}</span>
                                    </td>
                                    <td>${item.jabatan || 'N/A'}</td>
                                    <td>${item.email || 'N/A'}</td>
                                </tr>
                            `;
                        });

                    } else if (type === 'mahasiswa') {
                        html += `
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul Pengabdian</th>
                                    <th>Tanggal</th>
                                    <th>Ketua</th>
                                    <th>Jumlah Mahasiswa</th>
                                    <th>Prodi Mahasiswa</th>
                                    <th>Sumber Dana</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;

                        data.details.forEach((item, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="font-weight-bold text-primary">${item.judul}</div>
                                        <small class="text-muted">${item.id_pengabdian}</small>
                                    </td>
                                    <td>${new Date(item.tanggal_pengabdian).toLocaleDateString('id-ID')}</td>
                                    <td>${item.ketua}</td>
                                    <td class="text-center">
                                        <span class="badge badge-success">${item.jumlah_mahasiswa}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">Informatika: ${item.mahasiswa_informatika}</span>
                                        <span class="badge badge-warning">SI: ${item.mahasiswa_sistem_informasi}</span>
                                    </td>
                                    <td><span class="badge badge-secondary">${item.sumber_dana}</span></td>
                                </tr>
                            `;
                        });
                    }

                    html += `</tbody></table></div></div></div>`;
                } else {
                    html += `
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada data</h5>
                            <p class="text-muted">Belum ada data detail untuk kategori ini.</p>
                        </div>
                    `;
                }

                $('#modalBody').html(html);

                // Initialize DataTable if there's data
                if (data.details && data.details.length > 0) {
                    setTimeout(() => {
                        $('#detailTable').DataTable({
                            "pageLength": 10,
                            "order": [
                                [0, "asc"]
                            ],
                            "language": {
                                "search": "Cari:",
                                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                                "zeroRecords": "Tidak ada data yang sesuai",
                                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                "infoEmpty": "Tidak ada data",
                                "paginate": {
                                    "first": "Pertama",
                                    "last": "Terakhir",
                                    "next": "Selanjutnya",
                                    "previous": "Sebelumnya"
                                }
                            }
                        });
                    }, 100);
                }

                // Show export button if there's data
                if (data.details && data.details.length > 0) {
                    $('#exportBtn').show().off('click').on('click', function() {
                        exportModalData(type, data);
                    });
                } else {
                    $('#exportBtn').hide();
                }
            }

            function exportModalData(type, data) {
                // Simple CSV export functionality
                let csvContent = "data:text/csv;charset=utf-8,";

                if (type === 'pengabdian') {
                    csvContent += "No,Judul Pengabdian,ID Pengabdian,Tanggal,Ketua,Sumber Dana,Prodi,Dengan Mahasiswa\n";
                    data.details.forEach((item, index) => {
                        csvContent +=
                            `${index + 1},"${item.judul}","${item.id_pengabdian}","${item.tanggal_pengabdian}","${item.ketua}","${item.sumber_dana}","${item.kategori_prodi}","${item.dengan_mahasiswa ? 'Ya' : 'Tidak'}"\n`;
                    });
                } else if (type === 'dosen') {
                    csvContent += "No,Nama Dosen,NIK,NIDN,Program Studi,Jumlah Pengabdian,Jabatan,Email\n";
                    data.details.forEach((item, index) => {
                        csvContent +=
                            `${index + 1},"${item.nama}","${item.nik}","${item.nidn || ''}","${item.prodi}","${item.jumlah_pengabdian}","${item.jabatan || ''}","${item.email || ''}"\n`;
                    });
                } else if (type === 'mahasiswa') {
                    csvContent +=
                        "No,Judul Pengabdian,ID Pengabdian,Tanggal,Ketua,Jumlah Mahasiswa,Mahasiswa Informatika,Mahasiswa SI,Sumber Dana\n";
                    data.details.forEach((item, index) => {
                        csvContent +=
                            `${index + 1},"${item.judul}","${item.id_pengabdian}","${item.tanggal_pengabdian}","${item.ketua}","${item.jumlah_mahasiswa}","${item.mahasiswa_informatika}","${item.mahasiswa_sistem_informasi}","${item.sumber_dana}"\n`;
                    });
                }

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `detail_${type}_{{ $filterYear }}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        </script>
    @endpush
@endsection
