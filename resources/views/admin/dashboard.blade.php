    @extends('admin.layouts.main')

    @section('title', 'Dashboard Staf Fakultas')

    @push('styles')
        <style>
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
                /* Ganti ukuran sesuai keinginan */
            }

            /* Sembunyikan teks default treemap yang tidak diinginkan */
            #luaranBarChart canvas {
                font-size: 0 !important;
            }

            /* Override semua teks pada treemap canvas */
            .chart-area canvas text {
                display: none !important;
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

            /* Enhanced Dashboard Layout Styling */
            .statistics-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-left-width: 0.25rem !important;
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            }

            .statistics-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
            }

            .statistics-card .card-body {
                padding: 1.2rem 1.5rem !important;
            }

            /* Enhanced Font Sizes for Statistics Cards */
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

            .statistics-card .mt-2 {
                margin-top: 0.5rem !important;
            }

            .statistics-card .mt-1 {
                margin-top: 0.4rem !important;
            }

            .statistics-card .mb-1 {
                margin-bottom: 0.4rem !important;
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

            .badge-comparison {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
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
                transform: scale(1.03);
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

            .dashboard-actions .btn {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 8px;
            }

            .dashboard-actions .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            }

            /* Card Enhancement */
            .card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 12px;
                border: none;
                box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
            }

            .card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
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
            body.modal-open .card:hover {
                transform: none !important;
                box-shadow: none !important;
            }

            body.modal-open .modern-card:hover {
                transform: none !important;
                box-shadow: none !important;
            }

            body.modal-open .statistics-card:hover {
                transform: none !important;
                box-shadow: none !important;
            }

            /* Modern Card Enhanced Hover Animation */
            .modern-card {
                border-radius: 16px;
                border: none;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .modern-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15) !important;
            }

            .modern-card .card-header {
                background: transparent;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 1.25rem 1.5rem;
                border-radius: 16px 16px 0 0;
            }

            .modern-card .card-body {
                padding: 1.5rem;
            }

            .modern-card .card-header h6 {
                font-weight: 600;
                font-size: 0.95rem;
            }

            /* Action List Items */
            .list-group-item-action {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-left: 3px solid transparent;
                border-radius: 8px;
            }

            /* Status Button Consistent Size - Enhanced */
            .status-btn {
                width: 130px !important;
                min-width: 130px !important;
                max-width: 130px !important;
                height: 32px !important;
                text-align: center !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                white-space: nowrap !important;
                font-weight: 500 !important;
                font-size: 0.8rem !important;
                line-height: 1 !important;
                padding: 0.375rem 0.5rem !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                border: none !important;
                outline: none !important;
                border-radius: 8px !important;
            }

            .status-btn:hover {
                transform: translateY(-2px) scale(1.02) !important;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
            }

            .status-btn:focus {
                box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.25) !important;
            }

            .status-btn.btn-success {
                background-color: #1cc88a !important;
                border-color: #1cc88a !important;
            }

            .status-btn.btn-danger {
                background-color: #f6c23e !important;
                border-color: #f6c23e !important;
            }

            .list-group-item-action:hover {
                border-left-color: #4e73df;
                background-color: #f8f9fc;
                transform: translateX(4px);
                box-shadow: 0 4px 15px rgba(78, 115, 223, 0.1);
            }

            .statPengabdianDosen {

                /* Latest Activity Styling */
                #latestPengabdianCard .border-bottom:last-child {
                    border-bottom: none !important;
                }

                #latestPengabdianCard .border-bottom:hover {
                    background-color: #f8f9fc;
                }

                /* Sorting button styles */
                #dosenSortBtn {
                    border: 1px solid #e3e6f0;
                    transition: all 0.2s ease;
                }

                #dosenSortBtn:hover {
                    background-color: #4e73df;
                    color: white;
                    border-color: #4e73df;
                }

                #dosenSortBtn.active {
                    background-color: #4e73df;
                    color: white;
                    border-color: #4e73df;
                }

                /* Chart container improvements */
                .chart-bar,
                .chart-area,
                .chart-pie {
                    position: relative;
                }

                .chart-bar::-webkit-scrollbar {
                    width: 6px;
                }

                .chart-bar::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                .chart-bar::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 3px;
                }

                .chart-bar::-webkit-scrollbar-thumb:hover {
                    background: #a8a8a8;
                }

                /* Chart Scrollable Styles */
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

                /* Enhanced chart container */
                .chart-container-enhanced {
                    position: relative;
                    transition: all 0.3s ease;
                }

                .chart-container-enhanced:hover {
                    box-shadow: inset 0 0 10px rgba(78, 115, 223, 0.1);
                }

                /* Chart Container Toggle */
                .chart-container {
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    opacity: 1;
                }

                .chart-container.d-none {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.98);
                }

                /* Toggle Buttons */
                .btn-group .btn {
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

                /* Badge Animation */
                #currentViewBadge {
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .badge {
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .badge:hover {
                    transform: scale(1.05);
                }

                /* Chart Info Text */
                #chartInfoText {
                    transition: all 0.3s ease;
                }

                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .statistics-card {
                        margin-bottom: 0.75rem;
                    }

                    .statistics-card .card-body {
                        padding: 0.75rem 1rem !important;
                    }

                    .statistics-card .text-xs {
                        font-size: 0.7rem !important;
                    }

                    .statistics-card .h5 {
                        font-size: 1.3rem !important;
                    }

                    .statistics-card .text-muted {
                        font-size: 0.65rem !important;
                    }

                    .statistics-card .font-weight-bold {
                        font-size: 0.7rem !important;
                    }

                    .statistics-card .fa-2x {
                        font-size: 1.8em !important;
                    }

                    #latestPengabdianCard .d-flex {
                        flex-direction: column;
                        align-items: flex-start !important;
                    }

                    #latestPengabdianCard .ml-2 {
                        margin-left: 0 !important;
                        margin-top: 0.5rem;
                    }

                    .chart-bar {
                        height: 350px !important;
                    }

                    .chart-bar-scrollable {
                        max-height: 400px !important;
                    }
                }

                /* Extra small screens */
                @media (max-width: 576px) {
                    .statistics-card .card-body {
                        padding: 0.5rem 0.75rem !important;
                    }

                    .statistics-card .text-xs {
                        font-size: 0.65rem !important;
                    }

                    .statistics-card .h5 {
                        font-size: 1.1rem !important;
                    }

                    .statistics-card .text-muted,
                    .statistics-card .font-weight-bold {
                        font-size: 0.65rem !important;
                    }

                    .statistics-card .fa-2x {
                        font-size: 1.5em !important;
                    }

                    .statistics-card .badge {
                        font-size: 0.6rem !important;
                        padding: 0.2rem 0.4rem !important;
                    }

                    .chart-bar-scrollable {
                        max-height: 300px !important;
                        padding: 10px !important;
                    }
                }
        </style>
    @endpush

    @section('content')
        <!-- H1: Dashboard Header -->
        <div class="mb-3">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt mr-2 text-primary"></i>Dashboard Pengabdian Admin FTI
            </h1>
        </div>

        <!-- Filter Grafik -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="small text-muted mb-1 d-block">
                        <i class="fas fa-filter mr-1"></i>Tahun :
                    </label>
                    <select class="form-control form-control-sm" id="yearFilter">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                                @if ($year == $currentYear)
                                    (Tahun Ini)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- BARIS 1: GAMBARAN UMUM (KPIs / Key Performance Indicators) -->
        <div class="row mb-4">
            <!-- [ Statistik Cepat 1 ] - Total Pengabdian -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 statistics-card modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengabdian
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Total pengabdian {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div id="statTotalPengabdian"
                                    class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat-number"
                                    style="cursor: pointer;"
                                    onclick="window.location.href='{{ route('admin.pengabdian.index') }}'">
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
                                            <i class="fas fa-handshake mr-1"></i>Kolaborasi IT & SI:
                                            {{ $stats['pengabdian_kolaborasi'] }}
                                        </span>
                                        <br>
                                        <span class="text-secondary font-weight-bold" data-toggle="tooltip"
                                            title="Pengabdian khusus Informatika">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['pengabdian_khusus_informatika'] }}
                                        </span>
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

            <!-- [ Statistik Cepat 2 ] - Total Dosen Terlibat -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 statistics-card modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Dosen Terlibat
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Jumlah dosen yang terlibat dalam pengabdian {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div id="statDosenTerlibat"
                                    class="h5 mb-0 font-weight-bold text-gray-800
                                    clickable-stat-number"
                                    style="cursor: pointer;"
                                    onclick="window.location.href='{{ route('admin.dosen.index') }}'">
                                    {{ $stats['total_dosen'] }}
                                </div>
                                <div class="text-xs text-success mt-1 mb-2" data-toggle="tooltip"
                                    title="Jumlah dosen terlibat dari total seluruh dosen FTI">


                                    @if (isset($stats['total_dosen_keseluruhan']) && $stats['total_dosen_keseluruhan'] > 0)
                                        {{-- 1. Logika Blade untuk menentukan class warna (di luar div agar bisa dipakai di class div) --}}
                                        @php
                                            $badgeClass = 'badge-warning'; // Default: Warning (kuning)
                                            $textClass = 'text-warning'; // Default: Warning text color

                                            // Cek jika total dosen terlibat SAMA dengan total keseluruhan
                                            if ($stats['total_dosen'] >= $stats['total_dosen_keseluruhan']) {
                                                $badgeClass = 'badge-success'; // Ubah menjadi Success (hijau)
                                                $textClass = 'text-success'; // Ubah menjadi Success text color
                                            }
                                        @endphp

                                        {{-- 2. Terapkan variabel $textClass ke div --}}
                                        <div class="{{ $textClass }} text-xs mt-1 mb-2" data-toggle="tooltip"
                                            title="Jumlah dosen terlibat dari total seluruh dosen FTI">
                                            <i class="fas fa-users mr-1"></i>
                                            <span class="font-weight-bold">{{ $stats['total_dosen'] }} dari
                                                {{ $stats['total_dosen_keseluruhan'] ?? 'N/A' }} dosen</span>

                                            {{-- 3. Terapkan variabel $badgeClass ke badge --}}
                                            <span class="badge {{ $badgeClass }} ml-1">
                                                {{ round(($stats['total_dosen'] / $stats['total_dosen_keseluruhan']) * 100, 1) }}%
                                            </span>
                                        </div>
                                    @else
                                        {{-- Tampilan Default jika data tidak tersedia atau total keseluruhan 0 --}}
                                        <div class="text-secondary text-xs mt-1 mb-2" data-toggle="tooltip"
                                            title="Data total dosen FTI tidak tersedia">
                                            <i class="fas fa-users mr-1"></i>
                                            <span class="font-weight-bold">{{ $stats['total_dosen'] }} dari N/A
                                                dosen</span>
                                            <span class="badge badge-secondary ml-1">N/A</span>
                                        </div>
                                    @endif
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
                                        <span class="text-secondary font-weight-bold">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['dosen_informatika'] }}
                                        </span>
                                        <br>
                                        <span class="text-info font-weight-bold">
                                            <i class="fas fa-database mr-1"></i>Sistem Informasi:
                                            {{ $stats['dosen_sistem_informasi'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- [ Statistik Cepat 3 ] - % dgn Mahasiswa -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 statistics-card modern-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    PkM DENGAN MAHASISWA
                                    @if ($filterYear !== 'all')
                                        <span class="fs-3">({{ $filterYear }})</span>
                                    @endif
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Persentase pengabdian yang melibatkan mahasiswa {{ $filterYear !== 'all' ? 'pada tahun ' . $filterYear : 'keseluruhan' }}"
                                        style="cursor: pointer;"></i>
                                </div>
                                <div id="statDenganMahasiswa"
                                    class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat-number"
                                    style="cursor: pointer;"
                                    onclick="window.location.href='{{ route('admin.mahasiswa.index') }}'">
                                    {{ $stats['persentase_pengabdian_dengan_mahasiswa'] }}%
                                </div>
                                <div class="text-xs text-muted mt-1 ">
                                    @if ($stats['percentage_change_mahasiswa'] != 0)
                                        <span
                                            class="badge badge-{{ $stats['percentage_change_mahasiswa'] > 0 ? 'success' : 'danger' }} mr-1"
                                            data-toggle="tooltip"
                                            title="Perubahan dari {{ $stats['previous_year'] }}: {{ $stats['percentage_change_mahasiswa'] > 0 ? 'Peningkatan' : 'Penurunan' }} {{ abs($stats['percentage_change_mahasiswa']) }}%">
                                            {{ $stats['percentage_change_mahasiswa'] > 0 ? '+' : '' }}{{ $stats['percentage_change_mahasiswa'] }}%
                                        </span>
                                    @endif
                                    <span data-toggle="tooltip"
                                        title="{{ $stats['total_mahasiswa'] }} dari {{ $stats['total_pengabdian'] }} pengabdian melibatkan mahasiswa">
                                        {{ $stats['total_mahasiswa'] }}/{{ $stats['total_pengabdian'] }}
                                    </span>
                                    • {{ $stats['year_label'] }}
                                    <div class="mt-2">
                                        <span class="text-secondary font-weight-bold">
                                            <i class="fas fa-laptop-code mr-1"></i>Informatika:
                                            {{ $stats['mahasiswa_informatika'] }}
                                        </span>
                                        <br>
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

        <!-- BARIS KPI: HKI Per Prodi -->

        <!-- BARIS 2: KONTEN UTAMA & DETAIL -->
        <div class="row">
            <!-- KOLOM KIRI (Area Aksi & Analisis Utama) (col-xl-8) -->
            <div class="col-xl-8">
                <!-- [ 1. Dokumen Belum Lengkap ] (Daftar pengabdian dengan dokumen kurang) -->
                <div class="card shadow mb-4 modern-card">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Dokumen Belum Lengkap
                        </h6>
                        <span class="badge badge-warning">{{ count($pengabdianNeedingDocs ?? []) }} pengabdian</span>
                    </div>
                    <div class="card-body">
                        @php
                            $hasMissingDocsForHeader = false;
                            foreach (
                                [
                                    'Laporan Akhir',
                                    'Surat Tugas Dosen',
                                    'Surat Permohonan',
                                    'Surat Ucapan Terima Kasih',
                                    'MoU/MoA/Dokumen Kerja Sama Kegiatan',
                                ]
                                as $rname
                            ) {
                                if (($missingCounts[$rname] ?? 0) > 0) {
                                    $hasMissingDocsForHeader = true;
                                    break;
                                }
                            }
                        @endphp

                        @if ($hasMissingDocsForHeader)
                            @php
                                $requiredDocNames = [
                                    'Laporan Akhir',
                                    'Surat Tugas Dosen',
                                    'Surat Permohonan',
                                    'Surat Ucapan Terima Kasih',
                                    'MoU/MoA/Dokumen Kerja Sama Kegiatan',
                                ];
                            @endphp
                            <div class="list-group list-group-flush">
                                @foreach ($requiredDocNames as $rname)
                                    @php $missingCount = $missingCounts[$rname] ?? 0; @endphp
                                    @if ($missingCount > 0)
                                        <a href="#" data-toggle="modal" data-target="#needActionModal"
                                            data-filter="{{ $rname }}"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="font-weight-bold">{{ $rname }}</div>
                                                <div class="small text-gray-500">Pengabdian yang belum memiliki dokumen ini
                                                </div>
                                            </div>
                                            <span class="badge badge-danger badge-pill">{{ $missingCount }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="font-weight-bold text-success">Semua Dokumen Lengkap!</div>
                                <div class="small text-gray-500">Tidak ada pengabdian yang memerlukan tindakan</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- [ 2. Rekap Pengabdian per Dosen ] (Unified Chart with Toggle) -->
                <div class="card shadow mb-4 modern-card">
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
                                <canvas id="dosenChart" width="100%"
                                    height="{{ max(400, $top5DosenCount * 80) }}"></canvas>
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


                <!-- [ 3. Distribusi Luaran ] (Grafik Treemap) -->
                <div class="card shadow mb-4 modern-card">
                    <div class="card-header py-3">
                        <div>
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar mr-2"></i>Distribusi Luaran
                            </h6>
                            @if ($filterYear !== 'all')
                                <small class="text-muted">
                                    <i class="fas fa-filter mr-1"></i>Tahun: {{ $filterYear }}
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (count($dataTreemap) > 0)
                            <div class="chart-area" style="height: 350px;">
                                <canvas id="luaranBarChart"></canvas>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-chart-area fa-3x mb-3"></i>
                                    <div class="h6">Belum ada data luaran</div>
                                    <p class="text-muted small">Data distribusi luaran akan muncul di sini</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (Info Sekunder) (col-xl-4) -->
            <div class="col-xl-4">
                <!-- [ 4. Status Kelengkapan ] (Grafik Donut) -->
                <div class="card shadow mb-4 modern-card">
                    <div class="card-header py-3">
                        <div>
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-check mr-2"></i>Status Kelengkapan Dokumen
                            </h6>
                            @if ($filterYear !== 'all')
                                <small class="text-muted">
                                    <i class="fas fa-filter mr-1"></i>Tahun: {{ $filterYear }}
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                            <canvas id="statusDokumenChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- [ 5. Pengabdian Terbaru ] (Tabel Log Aktivitas) -->
                <div class="card shadow mb-4 modern-card" id="latestPengabdianCard">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history mr-2"></i>Pengabdian Terbaru
                        </h6>
                        <a href="{{ route('admin.pengabdian.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list mr-1"></i>Semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($latestPengabdian as $item)
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.pengabdian.show', $item->id_pengabdian) }}"
                                                class="text-dark text-decoration-none">
                                                {{ Str::limit($item->judul_pengabdian, 60) }}
                                            </a>
                                        </h6>
                                        <div class="text-muted small mb-2">
                                            <i class="fas fa-user mr-1"></i>{{ $item->ketua->nama ?? '-' }}
                                            <span class="mx-2">•</span>
                                            <i
                                                class="fas fa-calendar mr-1"></i>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        @php
                                            $isComplete = $completenessMap[$item->id_pengabdian] ?? false;
                                            $requiredDocNames = [
                                                'Laporan Akhir',
                                                'Surat Tugas Dosen',
                                                'Surat Permohonan',
                                                'Surat Ucapan Terima Kasih',
                                                'MoU/MoA/Dokumen Kerja Sama Kegiatan',
                                            ];
                                            $jenisByName = [];
                                            foreach ($jenisDokumenList as $jd) {
                                                $jenisByName[$jd->nama_jenis_dokumen] = $jd;
                                            }
                                        @endphp

                                        <button type="button"
                                            class="btn btn-sm {{ $isComplete ? 'btn-success' : 'btn-danger' }} status-btn"
                                            data-toggle="modal" data-target="#docsModal{{ $item->id_pengabdian }}">
                                            <i
                                                class="fas {{ $isComplete ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1"></i>
                                            {{ $isComplete ? 'Lengkap' : 'Belum Lengkap' }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Modal: Detail Dokumen -->
                                <div class="modal fade" id="docsModal{{ $item->id_pengabdian }}" tabindex="-1"
                                    role="dialog" aria-labelledby="docsModalLabel{{ $item->id_pengabdian }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="docsModalLabel{{ $item->id_pengabdian }}">
                                                    Dokumen: {{ Str::limit($item->judul_pengabdian, 120) }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted">Ketua: {{ $item->ketua->nama ?? '-' }} —
                                                    Ditambahkan:
                                                    {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                                                </p>
                                                <ul class="list-group">
                                                    @foreach ($requiredDocNames as $name)
                                                        @php
                                                            $jenis = $jenisByName[$name] ?? null;
                                                            $has = false;
                                                            $dok = null;
                                                            if ($jenis) {
                                                                $dok = $item->dokumen->firstWhere(
                                                                    'id_jenis_dokumen',
                                                                    $jenis->id_jenis_dokumen,
                                                                );
                                                                $has = (bool) $dok;
                                                            }
                                                            $labelToKeyLocal = [
                                                                'Laporan Akhir' => 'laporan_akhir',
                                                                'Surat Tugas Dosen' => 'surat_tugas',
                                                                'Surat Permohonan' => 'surat_permohonan',
                                                                'Surat Ucapan Terima Kasih' => 'ucapan_terima_kasih',
                                                                'MoU/MoA/Dokumen Kerja Sama Kegiatan' => 'kerjasama',
                                                            ];
                                                            $highlightKey = $labelToKeyLocal[$name] ?? null;
                                                        @endphp
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                @if ($has)
                                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                                @else
                                                                    <i
                                                                        class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                                                @endif
                                                                <strong>{{ $name }}</strong>
                                                                @if ($has && $dok->created_at)
                                                                    <div class="small text-muted">
                                                                        {{ $dok->created_at->format('d/m/Y') }}</div>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                @if ($has)
                                                                    <a href="{{ $dok->url_file }}" target="_blank"
                                                                        class="btn btn-sm btn-outline-secondary">Download</a>
                                                                @else
                                                                    <a href="{{ route('admin.pengabdian.edit', $item->id_pengabdian) }}{{ $highlightKey ? '?highlight=' . $highlightKey : '' }}#dokumen"
                                                                        target="_blank" rel="noopener noreferrer"
                                                                        class="btn btn-sm btn-outline-success">Unggah</a>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <div class="text-muted mb-2">
                                    <i class="fas fa-inbox fa-2x"></i>
                                </div>
                                <div class="font-weight-bold">Belum ada aktivitas</div>
                                <div class="small text-muted">Aktivitas terbaru akan muncul di sini</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')

        <script>
            // --- KODE LENGKAP DAN TERBARU DIMULAI DARI SINI ---

            // Daftarkan plugin datalabels secara global
            Chart.register(ChartDataLabels);

            // Set default font untuk semua chart
            Chart.defaults.font.family =
                'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            Chart.defaults.color = '#858796';

            // 1. Ambil data dari Controller
            const allNamaDosen = @json($namaDosen);
            const allJumlahPengabdian = @json($jumlahPengabdianDosen);

            // 2. Gabungkan data menjadi satu array objek agar mudah dikelola
            let originalData = allNamaDosen.map((nama, index) => ({
                nama: nama,
                jumlah: allJumlahPengabdian[index]
            }));

            // 3. Fungsi terpusat untuk membuat chart dosen (dengan gradasi warna)
            function createDosenChart(canvasId, labels, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');

                const existingChart = Chart.getChart(canvasId);
                if (existingChart) {
                    existingChart.destroy();
                }

                // Membuat array warna dinamis berdasarkan nilai data
                const backgroundColors = data.map(value => {
                    const baseColor = [78, 115, 223]; // RGB untuk #4e73df
                    const maxValue = Math.max(...data);

                    if (maxValue === 0) return `rgb(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]})`;

                    // Hitung opacity: nilai tertinggi = opacity 1 (solid), terendah = opacity 0.3 (pudar)
                    const minOpacity = 0.3;
                    const maxOpacity = 1.0;
                    const opacity = minOpacity + (maxOpacity - minOpacity) * (value / maxValue);

                    return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${opacity.toFixed(2)})`;
                });

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: data,
                            backgroundColor: backgroundColors, // Gunakan array warna dinamis
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

            // 4. Fungsi untuk update dan render ulang kedua chart dosen
            function updateDosenCharts(dataToShow) {
                const labels = dataToShow.map(d => d.nama);
                const counts = dataToShow.map(d => d.jumlah);

                createDosenChart('dosenChart', labels.slice(0, 5), counts.slice(0, 5));
                createDosenChart('dosenAllChart', labels, counts);
            }

            // 5. Logika yang dijalankan setelah halaman siap (DOMContentLoaded)
            document.addEventListener('DOMContentLoaded', function() {
                // --- Chart Rekap Pengabdian per Dosen ---
                updateDosenCharts(originalData);

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
                    updateDosenCharts(originalData);
                });

                // --- Logika untuk Filter Tahun ---
                const yearFilter = document.getElementById('yearFilter');
                if (yearFilter) {
                    yearFilter.addEventListener('change', function() {
                        const selectedYear = this.value;
                        const currentUrl = new URL(window.location);
                        currentUrl.searchParams.set('year', selectedYear);
                        window.location.href = currentUrl.toString();
                    });
                }

                // --- Kode untuk Chart Lainnya (Donut & Luaran) ---
                // Kode ini disalin dari file admin Anda sebelumnya untuk menjaga fungsionalitasnya

                // 1. Grafik Status Kelengkapan Dokumen (Doughnut Chart)

                // Set default font
                Chart.defaults.font.family = 'Nunito',
                    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
                Chart.defaults.color = '#858796';
                // Daftarkan plugin datalabels secara global
                Chart.register(ChartDataLabels);

                // Plugin: draw centered percentage text inside doughnut
                const centerPercentPlugin = {
                    id: 'centerPercent',
                    beforeDraw: (chart) => {
                        if (!chart.config || chart.config.type !== 'doughnut') return;
                        const ctx = chart.ctx;
                        const {
                            width,
                            height
                        } = chart;
                        const dataset = chart.data && chart.data.datasets && chart.data.datasets[0];
                        if (!dataset) return;
                        const data = dataset.data || [];
                        const total = data.reduce((a, b) => a + b, 0);
                        const complete = data[0] || 0;
                        const percent = total ? Math.round((complete * 100) / total) : 0;

                        ctx.save();
                        // Determine font sizes responsive to canvas size
                        const fontSize = Math.round(Math.min(width, height) / 6);
                        ctx.font = `bold ${fontSize}px Nunito, Arial, sans-serif`;
                        ctx.fillStyle = '#4e73df';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        // Primary percentage
                        ctx.fillText(percent + '%', width / 2, height / 2 - (fontSize * 0.08));
                        // Small label under the number
                        ctx.font = `${Math.max(Math.round(fontSize / 3), 12)}px Nunito, Arial, sans-serif`;
                        ctx.fillStyle = '#858796';
                        ctx.fillText('Lengkap', width / 2, height / 2 + Math.round(fontSize / 2.2));
                        ctx.restore();
                    }
                };
                Chart.register(centerPercentPlugin);
                new Chart(document.getElementById("statusDokumenChart"), {
                    type: 'doughnut',
                    data: {
                        labels: ["Dokumen Lengkap", "Belum Lengkap"],
                        datasets: [{
                            data: [{{ $pengabdianLengkap }}, {{ $pengabdianTidakLengkap }}],
                            backgroundColor: ['#1cc88a', '#f6c23e'],
                            hoverBackgroundColor: ['#17a673', '#dda20a'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '80%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#fff',
                                borderWidth: 1,
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    title: function(context) {
                                        return 'Status Kelengkapan Dokumen';
                                    },
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                            0;

                                        return [
                                            `${label}: ${value} pengabdian`,
                                            `Persentase: ${percentage}%`,
                                            `Total keseluruhan: ${total} pengabdian`
                                        ];
                                    },
                                    footer: function(context) {
                                        const totalComplete = {{ $pengabdianLengkap }};
                                        const totalIncomplete = {{ $pengabdianTidakLengkap }};
                                        const total = totalComplete + totalIncomplete;

                                        if (total > 0) {
                                            const completionRate = ((totalComplete / total) * 100).toFixed(
                                                1);
                                            return `Tingkat kelengkapan: ${completionRate}%`;
                                        }
                                        return '';
                                    }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    if (value === 0 || sum === 0) return '';
                                    let percentage = (value * 100 / sum).toFixed(0) + "%";
                                    return percentage;
                                }
                            }
                        }
                    }
                });

                // 2. Grafik Distribusi Luaran (Bar Chart)
                @if (count($dataTreemap) > 0)
                    // Data untuk bar chart distribusi luaran
                    const luaranData = @json($dataTreemap);

                    // Sortir data berdasarkan jumlah luaran (descending)
                    luaranData.sort((a, b) => b.v - a.v);

                    const labels = luaranData.map(item => item.g);
                    const values = luaranData.map(item => item.v);
                    const colors = [
                        '#4e73df', '#1cc88a', '#36b9cc',
                        '#f6c23e', '#e74a3b', '#858796',
                        '#6f42c1', '#fd7e14', '#20c997'
                    ];

                    new Chart(document.getElementById("luaranBarChart"), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Luaran',
                                data: values,
                                backgroundColor: colors.slice(0, labels.length),
                                borderColor: colors.slice(0, labels.length),
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            interaction: {
                                intersect: false,
                                mode: 'nearest'
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        maxRotation: 45,
                                        minRotation: 0,
                                        callback: function(value, index, ticks) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.substring(0, 12) + '...' :
                                                label;
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    mode: 'nearest',
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    cornerRadius: 6,
                                    displayColors: true,
                                    callbacks: {
                                        title: function(context) {
                                            return context[0].label;
                                        },
                                        label: function(context) {
                                            const value = context.parsed
                                                .y; // Untuk vertical bar, nilai ada di y
                                            const total = values.reduce((sum, val) => sum + val, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(
                                                1) : 0;

                                            return [
                                                `Jumlah: ${value} luaran`,
                                                `Persentase: ${percentage}%`,
                                                `Ranking: #${context.dataIndex + 1}`
                                            ];
                                        },
                                        labelColor: function(context) {
                                            return {
                                                borderColor: context.dataset.backgroundColor[context
                                                    .dataIndex],
                                                backgroundColor: context.dataset.backgroundColor[context
                                                    .dataIndex],
                                                borderWidth: 2,
                                                borderRadius: 4
                                            };
                                        }
                                    }
                                }
                            },
                            layout: {
                                padding: {
                                    top: 10,
                                    right: 20,
                                    bottom: 10,
                                    left: 10
                                }
                            }
                        }
                    });
                @endif
            });
        </script>
        {{-- Modal: Daftar Pengabdian dengan Dokumen Belum Lengkap --}}
        <div class="modal fade" id="needActionModal" tabindex="-1" role="dialog"
            aria-labelledby="needActionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="needActionModalLabel">Pengabdian dengan Dokumen Belum Lengkap
                            ({{ $needActionCount ?? 0 }})</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @if (!empty($pengabdianNeedingDocs) && count($pengabdianNeedingDocs) > 0)
                            <div class="list-group">
                                @foreach ($pengabdianNeedingDocs as $p)
                                    @php
                                        // normalize missing labels: accept either human label or internal key
                                        $labelToKey = [
                                            'Laporan Akhir' => 'laporan_akhir',
                                            'Surat Tugas Dosen' => 'surat_tugas',
                                            'Surat Permohonan' => 'surat_permohonan',
                                            'Surat Ucapan Terima Kasih' => 'ucapan_terima_kasih',
                                            'MoU/MoA/Dokumen Kerja Sama Kegiatan' => 'kerjasama',
                                        ];
                                        // preferred order (same as edit form)
                                        $preferred = array_keys($labelToKey);

                                        $rawMissing = $p['missing'] ?? [];
                                        $missingLabels = [];
                                        foreach ($rawMissing as $m) {
                                            if (isset($labelToKey[$m])) {
                                                // already a human label
                                                $missingLabels[] = $m;
                                            } else {
                                                // maybe it's an internal key -> find corresponding label
                                        $labelFound = array_search($m, $labelToKey, true);
        if ($labelFound !== false) {
            $missingLabels[] = $labelFound;
        } else {
            // unknown value, keep as-is
            $missingLabels[] = $m;
        }
    }
}
// build data-missing to include both labels and keys for robust client-side matching
$missingKeys = array_map(fn($lab) => $labelToKey[$lab] ?? $lab, $missingLabels);
$dataMissingArr = array_values(
    array_unique(array_merge($missingLabels, $missingKeys)),
);
$dataMissing = implode('|', $dataMissingArr);

                                        // determine first missing by preferred order
                                        $firstMissing = null;
                                        foreach ($preferred as $lab) {
                                            if (in_array($lab, $missingLabels, true)) {
                                                $firstMissing = $lab;
                                                break;
                                            }
                                        }
                                        if (!$firstMissing) {
                                            $firstMissing = $missingLabels[0] ?? null;
                                        }
                                        $highlight = $firstMissing ? $labelToKey[$firstMissing] ?? null : null;
                                    @endphp
                                    <div class="list-group-item" data-missing="{{ $dataMissing }}">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><a
                                                        href="{{ route('admin.pengabdian.show', $p['id']) }}">{{ Str::limit($p['judul'], 80) }}</a>
                                                </h6>
                                                <small class="text-muted">Ketua: {{ $p['ketua'] }}</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-danger mr-2">{{ count($p['missing']) }}
                                                    kurang</span>
                                                <a href="{{ route('admin.pengabdian.edit', $p['id']) }}{{ $highlight ? '?highlight=' . $highlight : '' }}#dokumen"
                                                    target="_blank" rel="noopener noreferrer"
                                                    class="btn btn-sm btn-primary">Lengkapi Dokumen</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-muted">Semua pengabdian telah memiliki dokumen lengkap.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            // make modal/list initialization callable so we can re-run after dynamic updates
            window.initNeedActionModal = function() {
                // store original list items markup to filter client-side (use data-missing)
                var originalItems = [];
                $('#needActionModal .list-group .list-group-item').each(function() {
                    originalItems.push({
                        html: $(this).prop('outerHTML'),
                        missing: $(this).attr('data-missing') || ''
                    });
                });

                // serialize missingCounts from server for client-side lookups
                var missingCounts = @json($missingCounts ?? []);

                // unbind first to avoid duplicate handlers
                $('.list-group-item-action[data-filter]').off('click.initNeedAction');
                $('.list-group-item-action[data-filter]').on('click.initNeedAction', function(e) {
                    var filter = $(this).data('filter');
                    // update modal title using client-side map
                    var cnt = (missingCounts && missingCounts[filter]) ? missingCounts[filter] : 0;
                    $('#needActionModalLabel').text('Pengabdian yang perlu dokumen: ' + filter + ' (' + cnt + ')');
                    // rebuild modal list with only items whose missing list contains the filter
                    var container = $('#needActionModal .modal-body .list-group');
                    if (!container.length) {
                        // no items present (server-side empty), just show modal
                        return;
                    }
                    container.empty();
                    var originalHtml = '';

                    // client-side map label -> key (mirror server mapping)
                    var labelToKeyClient = {
                        'Laporan Akhir': 'laporan_akhir',
                        'Surat Tugas Dosen': 'surat_tugas',
                        'Surat Permohonan': 'surat_permohonan',
                        'Surat Ucapan Terima Kasih': 'ucapan_terima_kasih',
                        'MoU/MoA/Dokumen Kerja Sama Kegiatan': 'kerjasama'
                    };
                    var desiredKey = labelToKeyClient[filter] || filter;

                    originalItems.forEach(function(it) {
                        if (it.missing.indexOf(filter) !== -1) {
                            // build jquery element so we can modify the link safely
                            var $el = $(it.html);
                            // find the primary action button (Lengkapi Dokumen) and rewrite href
                            $el.find('a.btn-primary').each(function() {
                                try {
                                    var $a = $(this);
                                    var href = $a.attr('href') || '';
                                    // remove any existing highlight param
                                    href = href.replace(/([?&])highlight=[^&]*(&?)/, function(_, p1,
                                        p2) {
                                        return p2 ? p1 : '';
                                    });
                                    var sep = href.indexOf('?') === -1 ? '?' : '&';
                                    // ensure anchor points to #dokumen
                                    href = href.split('#')[0];
                                    href = href + sep + 'highlight=' + encodeURIComponent(
                                        desiredKey) + '#dokumen';
                                    $a.attr('href', href);
                                } catch (e) {
                                    console.warn('Failed to rewrite Lengkapi Dokumen href', e);
                                }
                            });
                            container.append($el);
                        }
                        originalHtml += it.html;
                    });
                    // save originalHtml in container data attribute for restore
                    container.data('originalHtml', originalHtml);
                });

                // when modal is hidden, restore original title and content
                $('#needActionModal').off('hidden.initNeedAction').on('hidden.initNeedAction', function() {
                    $('#needActionModalLabel').text(
                        'Pengabdian dengan Dokumen Belum Lengkap ({{ $needActionCount ?? 0 }})');
                    var container = $('#needActionModal .modal-body .list-group');
                    if (container.length) {
                        var originalHtml = container.data('originalHtml') || originalItems.map(function(it) {
                            return it.html;
                        }).join('');
                        container.html(originalHtml);
                    }
                });
            };

            // initial run
            window.initNeedActionModal();

            // Initialize tooltips with configuration
            $(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    placement: 'top',
                    trigger: 'hover focus',
                    delay: {
                        "show": 500,
                        "hide": 100
                    },
                    html: true
                });
            });

            // Enhanced Dashboard Interactions
            $(document).ready(function() {
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip({
                    placement: 'auto',
                    trigger: 'hover focus',
                    delay: {
                        "show": 300,
                        "hide": 100
                    }
                });

                // Refresh dashboard button
                $('.dashboard-actions .btn').on('click', function() {
                    const btn = $(this);
                    const icon = btn.find('i');

                    // Add spinning animation
                    icon.addClass('fa-spin');
                    btn.prop('disabled', true);

                    // Simulate refresh (you can replace with actual refresh logic)
                    setTimeout(function() {
                        icon.removeClass('fa-spin');
                        btn.prop('disabled', false);

                        // Show success feedback
                        btn.tooltip('dispose')
                            .attr('title', 'Dashboard Diperbarui!')
                            .tooltip('show');

                        setTimeout(function() {
                            btn.tooltip('dispose')
                                .attr('title', 'Refresh Dashboard')
                                .tooltip();
                        }, 2000);
                    }, 1000);
                });
            });

            // Polling: refresh latest pengabdian card every 30 seconds (reduced frequency)
            (function() {
                var pollingInterval = 30000; // 30s
                var timer = null;

                function refreshLatestCard() {
                    try {
                        // Skip refresh if modal is currently open
                        if ($('.modal.show').length > 0) {
                            console.log('Skipping card refresh: modal is open');
                            return;
                        }

                        fetch(window.location.href, {
                            credentials: 'same-origin'
                        }).then(function(resp) {
                            return resp.text();
                        }).then(function(htmlText) {
                            try {
                                var parser = new DOMParser();
                                var doc = parser.parseFromString(htmlText, 'text/html');
                                var newCard = doc.querySelector('#latestPengabdianCard');
                                var oldCard = document.querySelector('#latestPengabdianCard');
                                if (newCard && oldCard) {
                                    // Clean up existing modals first to prevent conflicts
                                    $('.modal[id^="docsModal"]').each(function() {
                                        $(this).modal('hide');
                                        $(this).remove();
                                    });
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open').css({
                                        'padding-right': '',
                                        'overflow': ''
                                    });

                                    // Replace card content
                                    oldCard.innerHTML = newCard.innerHTML;

                                    // Wait for DOM to update, then re-initialize
                                    setTimeout(function() {
                                        // re-initialize tooltips for new content
                                        $('[data-toggle="tooltip"]').tooltip();

                                        // re-run modal init to rebind handlers
                                        if (window.initNeedActionModal) window.initNeedActionModal();

                                        // Ensure modals are properly initialized
                                        $('.modal[id^="docsModal"]').modal({
                                            show: false,
                                            backdrop: true,
                                            keyboard: true
                                        });
                                    }, 100);
                                }
                            } catch (e) {
                                console.warn('Failed to parse refreshed dashboard HTML', e);
                            }
                        }).catch(function(err) {
                            console.warn('Failed to fetch dashboard for refresh', err);
                        });
                    } catch (e) {
                        console.warn('refreshLatestCard error', e);
                    }
                }

                // start polling with reduced frequency
                timer = setInterval(refreshLatestCard, pollingInterval);
            })();
        </script>
    @endpush
