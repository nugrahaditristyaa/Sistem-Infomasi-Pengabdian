<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('admin/dashboard') }}">
        <div class="sidebar-brand-icon ">
            <img src="{{ asset('assets/img/fti-ukdw.png') }}" alt="Logo FTI" style="width: 60px; height: 50px;">
        </div>
        <div class="sidebar-brand-text mx-3">Admin FTI</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('admin/dashboard') }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        MENU
    </div>

    <!-- Nav Item - Pengabdian -->
    <li class="nav-item {{ request()->is('admin/pengabdian*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('admin/pengabdian') }}">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Data Pengabdian</span>
        </a>
    </li>

    <!-- Nav Item - Data HKI -->
    <li class="nav-item {{ request()->is('admin/hki') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('admin/hki') }}">
            <i class="fas fa-lightbulb"></i>
            <span>Data Luaran HKI</span>
        </a>
    </li>

    <!-- Nav Item - Data Dosen -->
    <li class="nav-item {{ request()->is('admin/dosen*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('admin/dosen') }}">
            <i class="fas fa-user-tie"></i>
            <span>Data Dosen</span>
        </a>
    </li>

    <!-- Nav Item - Data Mahasiswa -->
    <li class="nav-item {{ request()->is('admin/mahasiswa*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('admin/mahasiswa') }}">
            <i class="fas fa-user-graduate"></i>
            <span>Data Mahasiswa</span>
        </a>
    </li>

    @if (auth('admin')->check() && strtolower(auth('admin')->user()->role) === 'dekan')
        <!-- Nav Item - KPI (Dekan) -->
        <li
            class="nav-item {{ request()->is('dekan/kpi*') || request()->is('dekan/kpi-monitoring') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKPI"
                aria-expanded="{{ request()->is('dekan/kpi*') || request()->is('dekan/kpi-monitoring') ? 'true' : 'false' }}"
                aria-controls="collapseKPI">
                <i class="fas fa-chart-line"></i>
                <span>KPI</span>
            </a>
            <div id="collapseKPI"
                class="collapse {{ request()->is('inqa/kpi*') || request()->is('inqa/kpi-monitoring') ? 'show' : '' }}"
                aria-labelledby="headingKPI" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->is('inqa/kpi') ? 'active' : '' }}"
                        href="{{ route('inqa.kpi.index') }}">Data KPI</a>
                    <a class="collapse-item {{ request()->is('inqa/kpi-monitoring') ? 'active' : '' }}"
                        href="{{ route('inqa.kpi.monitoring') }}">Monitoring KPI</a>
                </div>
            </div>
        </li>
    @endif


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
