<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                @if(auth()->user()?->image)
                <img alt="{{ auth()->user()->name }}"
                    src="{{ asset('userimage/' . auth()->user()->image) }}"
                    class="rounded-circle mr-1"
                    style="height:35px; width:35px; object-fit:cover;">
                @else
                <img alt="avatar"
                    src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                    class="rounded-circle mr-1"
                    style="height:35px; width:35px; object-fit:cover;">
                @endif
                <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()?->name ?? 'Guest' }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()?->role ?? '')) }}
                </div>
                <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <a href="features-settings.html" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button type="submit" class="dropdown-item has-icon text-danger" @click.prevent>
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ url('index') }}">Hospital</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('index') }}">H</a>
        </div>
        <ul class="sidebar-menu">

            <li class="menu-header">Dashboard</li>
            <li class="{{ request()->is('index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('index') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <li class="menu-header">Doctors</li>
            <li class="dropdown {{ request()->is('add_doctor_view', 'view_doctor', 'show_doctor/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-user-md"></i> <span>Doctors</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ url('add_doctor_view') }}">Add Doctor</a></li>
                    <li><a class="nav-link" href="{{ url('view_doctor') }}">View Doctors</a></li>
                </ul>
            </li>
            <li class="menu-header">Scheduling</li>
            <li class="{{ request()->is('schedules*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.schedules.index') }}">
                    <i class="fas fa-calendar-alt"></i> Doctor Schedules
                </a>
            </li>

            <li class="menu-header">Appointments</li>
            <li class="dropdown {{ request()->is('add_appointment', 'show_appointment', 'update_appoint/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-calendar-check"></i> <span>Appointments</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ url('add_appointment') }}">Add Appointment</a></li>
                    <li><a class="nav-link" href="{{ url('show_appointment') }}">View Appointments</a></li>
                </ul>
            </li>

            <li class="menu-header">Users</li>
            <li class="dropdown {{ request()->is('view_users', 'add_user', 'edit_user/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-users"></i> <span>Users</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.add_user') }}">Add User</a></li>
                    <li><a class="nav-link" href="{{ route('admin.view_users') }}">View Users</a></li>
                </ul>
            </li>
            <li class="menu-header">Laboratory</li>
            <li class="dropdown {{ request()->is('admin/lab*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-flask"></i> <span>Laboratory</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.lab.index') }}">Lab Tests</a></li>
                    <li><a class="nav-link" href="{{ route('admin.lab.requests') }}">Lab Requests</a></li>
                </ul>
            </li>

            <li class="menu-header">Insurance</li>
            <li class="{{ request()->is('admin/claims*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.claims.index') }}">
                    <i class="fas fa-file-invoice-dollar"></i> Insurance Claims
                </a>
            </li>


            <li class="menu-header">Reports</li>
            <li class="{{ request()->is('reports/admin') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.admin') }}">
                    <i class="fas fa-chart-bar"></i> Reports Dashboard
                </a>
            </li>
        </ul>
    </aside>
</div>
</ul>
</aside>
</div>