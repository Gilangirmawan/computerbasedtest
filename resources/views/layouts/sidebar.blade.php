@php
    $menus = [
        1 =>  [
            (object) [
                'title' => 'Dashboard',
                'path' => 'dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
            // (object) [
            //     'title' => 'Administrator',
            //     'path' => 'administrator',
            //     'icon' => 'fas fa-user',
            // ],
            (object) [
                'title' => 'Guru',
                'path' => 'guru',
                'icon' => 'fas fa-chalkboard-teacher',
            ],
            (object) [
                'title' => 'Siswa',
                'path' => 'siswa',
                'icon' => 'fas fa-user-graduate',
            ],
            (object) [
                'title' => 'Mata Pelajaran',
                'path' => 'mapel',
                'icon' => 'fas fa-book-open',
            ],(object) [
                'title' => 'Jurusan',
                'path' => 'jurusan',
                'icon' => 'fas fa-fw fa-table',
            ],
            (object) [
                'title' => 'Kelas',
                'path' => 'kelas',
                'icon' => 'fas fa-users',
            ],
            (object) [
                'title' => 'Ganti Password',
                'path' => 'ganti-password',
                'icon' => 'fas fa-lock',
            ],
            // (object) [
            //     'title' => 'Log Out',
            //     'path' => 'logout',
            //     'icon' => 'fas fa-sign-out-alt',
            // ],
        ],
        2 =>  [
            (object) [
                'title' => 'Dashboard',
                'path' => 'dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
            (object) [
                'title' => 'Bank Soal',
                'path' => 'banksoal',
                'icon' => 'fas fa-database',
            ],
            (object) [
                'title' => 'Ujian',
                'path' => 'ujian',
                'icon' => 'fas fa-graduation-cap',
            ],
            (object) [
                'title' => 'Ganti Password',
                'path' => 'ganti-password',
                'icon' => 'fas fa-lock',
            ],
        ],
        3 =>  [
            (object) [
                'title' => 'Dashboard',
                'path' => 'dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
            (object) [
                'title' => 'Ikut Ujian',
                'path' => 'ikut_ujian',
                'icon' => 'fas fa-book-open',
            ],
            (object) [
                'title' => 'Ganti Password',
                'path' => 'ganti-password',
                'icon' => 'fas fa-lock',
            ],
        ],
    ];
@endphp

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    {{-- <i class="fas fa-laugh-wink"></i> --}}
                </div>
                <div class="sidebar-brand-text mx-3">SISTEM CBT</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            {{-- <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li> --}}

            <!-- Divider -->
            {{-- <hr class="sidebar-divider"> --}}

            <!-- Heading -->
            {{-- <div class="sidebar-heading">
                Manajemen Data
            </div> --}}

            <!-- Nav Item - Tables -->
            @foreach ($menus[auth()->user()->role_id] as $menu)
            <li class="nav-item {{ request()->is($menu->path.'*') ? 'active' : '' }}">
                <a class="nav-link" href="/{{$menu->path}}">
                    <i class="{{ $menu->icon }}"></i>
                    <span>{{ $menu->title }}</span></a>
            </li>
            @endforeach

            @if (Auth::user()->is_superadmin)
            <li class="nav-item {{ request()->is('administrator*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('administrator.index') }}">
                    <i class="fas fa-fw fa-user-shield"></i>
                    <span>Administrator</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>

            

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>