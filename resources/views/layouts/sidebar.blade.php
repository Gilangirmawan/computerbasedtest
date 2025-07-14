<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    {{-- <i class="fas fa-laugh-wink"></i> --}}
                </div>
                <div class="sidebar-brand-text mx-3">Admin CBT</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Manajemen Data
            </div>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('guru') ? 'active' : '' }}">
                <a class="nav-link" href="/guru">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Guru</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('siswa') ? 'active' : '' }}">
                <a class="nav-link" href="/siswa">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Siswa</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('mapel') ? 'active' : '' }}">
                <a class="nav-link" href="/mapel">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Mata Pelajaran</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('jurusan') ? 'active' : '' }}">
                <a class="nav-link" href="/jurusan">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Jurusan</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('kelas') ? 'active' : '' }}">
                <a class="nav-link" href="/kelas">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Kelas</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>