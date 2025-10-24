<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar"">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dekan.dashboard') }}">
        <div class="sidebar-brand-icon ">
            <img src="{{ asset('assets/img/logo-ukdw.png') }}" alt="Logo FTI" style="width: 60px; height: 50px;">
        </div>
        <div class="sidebar-brand-icon ">
            <img src="{{ asset('assets/img/fti-ukdw.png') }}" alt="Logo FTI" style="width: 60px; height: 50px;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @php
        $dashboardRole = auth('admin')->check() ? auth('admin')->user()->role : null;
        $dashboardRoute = match ($dashboardRole) {
            'Kaprodi TI' => 'kaprodi.ti.dashboard',
            'Kaprodi SI' => 'kaprodi.si.dashboard',
            default => 'dekan.dashboard',
        };
        $isDashboardActive =
            request()->routeIs('dekan.dashboard') ||
            request()->routeIs('kaprodi.ti.dashboard') ||
            request()->routeIs('kaprodi.si.dashboard');
    @endphp

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ $isDashboardActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($dashboardRoute) }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    @if (auth('admin')->check() && auth('admin')->user()->role === 'Dekan')
        <!-- Divider -->
        <hr class="sidebar-divider">
        <!-- Heading -->
        <div class="sidebar-heading">
            KPI Management
        </div>

        <!-- Nav Item - Data KPI -->
        <li class="nav-item {{ request()->routeIs('dekan.kpi.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dekan.kpi.index') }}">
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
    @else
        <!-- Divider for Kaprodi (smaller spacing) -->
        <hr class="sidebar-divider my-0">
    @endif

    @php
        $role = auth('admin')->check() ? auth('admin')->user()->role : null;
        $rekapRoute =
            $role === 'Kaprodi TI'
                ? 'kaprodi.ti.dosen.rekap'
                : ($role === 'Kaprodi SI'
                    ? 'kaprodi.si.dosen.rekap'
                    : 'dekan.dosen.rekap');
        $isActive =
            request()->routeIs('dekan.dosen.*') ||
            request()->routeIs('kaprodi.ti.dosen.*') ||
            request()->routeIs('kaprodi.si.dosen.*');
    @endphp

    <li class="nav-item {{ $isActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($rekapRoute) }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Rekap Pengabdian Dosen</span>
        </a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
