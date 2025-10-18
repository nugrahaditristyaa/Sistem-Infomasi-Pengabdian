<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar"">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('inqa.dashboard') }}">
        <div class="sidebar-brand-icon ">
            <img src="{{ asset('assets/img/fti-ukdw.png') }}" alt="Logo FTI" style="width: 60px; height: 50px;">
        </div>
        <div class="sidebar-brand-text mx-3">Dashboard Dekan</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('inqa.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('inqa.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        KPI Management
    </div>

    <!-- Nav Item - Data KPI -->
    <li class="nav-item {{ request()->routeIs('inqa.kpi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('inqa.kpi.index') }}">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Data KPI</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data Akademik
    </div>

    <!-- Nav Item - Rekap Dosen -->
    <li class="nav-item {{ request()->routeIs('inqa.dosen.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('inqa.dosen.rekap') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Rekap Pengabdian Dosen</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
