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
                <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()?->name ?? 'Doctor' }}</div>
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
            <a href="{{ url('doctor_index') }}">Hospital</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('doctor_index') }}">H</a>
        </div>
        <ul class="sidebar-menu">

            <li class="menu-header">Dashboard</li>
            <li class="{{ request()->is('doctor_index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('doctor_index') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <li class="menu-header">Appointments</li>
            <li class="{{ request()->is('doctor_appointment') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('doctor_appointment') }}">
                    <i class="fas fa-calendar-check"></i> Appointments
                </a>
            </li>

            <li class="menu-header">Clinical</li>
            <li class="dropdown {{ request()->is('encounters', 'encounter/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-stethoscope"></i> <span>Encounters</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('doctor.encounters') }}">All Encounters</a></li>
                </ul>
            </li>

            <li class="dropdown {{ request()->is('encounter/*/prescribe') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-prescription-bottle-alt"></i> <span>Prescriptions</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('doctor.encounters') }}">Write Prescription</a></li>
                </ul>
            </li>

        </ul>
    </aside>
</div>