<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="@if (Auth::guard('admin')->user()->role === 'Kaprodi TI') {{ route('kaprodi.ti.dashboard') }} @else {{ route('kaprodi.si.dashboard') }} @endif">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/img/fti-ukdw.png') }}" alt="Logo FTI" style="width: 60px; height: 50px;">
        </div>
        <div class="sidebar-brand-text mx-3">
            @if (Auth::guard('admin')->user()->role === 'Kaprodi TI')
                Kaprodi TI
            @else
                Kaprodi SI
            @endif
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('kaprodi.*.dashboard') ? 'active' : '' }}">
        <a class="nav-link"
            href="@if (Auth::guard('admin')->user()->role === 'Kaprodi TI') {{ route('kaprodi.ti.dashboard') }} @else {{ route('kaprodi.si.dashboard') }} @endif">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data Pengabdian
    </div>

    <!-- Nav Item - Daftar Pengabdian -->
    <li class="nav-item {{ request()->routeIs('kaprodi.*.pengabdian') ? 'active' : '' }}">
        <a class="nav-link"
            href="@if (Auth::guard('admin')->user()->role === 'Kaprodi TI') {{ route('kaprodi.ti.pengabdian') }} @else {{ route('kaprodi.si.pengabdian') }} @endif">
            <i class="fas fa-fw fa-list"></i>
            <span>Daftar Pengabdian</span>
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
