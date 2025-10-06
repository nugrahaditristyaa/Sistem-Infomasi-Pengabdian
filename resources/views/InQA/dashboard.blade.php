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
    </div>
@endsection
