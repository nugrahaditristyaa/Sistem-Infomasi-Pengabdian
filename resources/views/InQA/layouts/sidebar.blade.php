<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('inqa.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text mx-3">InQA Dashboard</div>
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

    <!-- Nav Item - KPI -->
    <li class="nav-item {{ request()->routeIs('inqa.kpi.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKpi"
            aria-expanded="true" aria-controls="collapseKpi">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>KPI</span>
        </a>
        <div id="collapseKpi" class="collapse {{ request()->routeIs('inqa.kpi.*') ? 'show' : '' }}"
            aria-labelledby="headingKpi" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kelola KPI:</h6>
                <a class="collapse-item {{ request()->routeIs('inqa.kpi.index') ? 'active' : '' }}"
                    href="{{ route('inqa.kpi.index') }}">Daftar KPI</a>
                <a class="collapse-item {{ request()->routeIs('inqa.kpi.create') ? 'active' : '' }}"
                    href="{{ route('inqa.kpi.create') }}">Tambah KPI</a>
                <div class="dropdown-divider"></div>
                <a class="collapse-item {{ request()->routeIs('inqa.kpi.monitoring') ? 'active' : '' }}"
                    href="{{ route('inqa.kpi.monitoring') }}">Monitoring KPI</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
