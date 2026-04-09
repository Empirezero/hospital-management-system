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
                <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()?->name ?? 'Pharmacist' }}</div>
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
            <a href="{{ route('pharmacist.home') }}">Hospital</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('pharmacist.home') }}">H</a>
        </div>
        <ul class="sidebar-menu">

            <li class="menu-header">Dashboard</li>
            <li class="{{ request()->is('show_medicine') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pharmacist.home') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <li class="menu-header">Medicines</li>
            <li class="dropdown {{ request()->is('view_medicine', 'show_medicine', 'edit_medicine/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-pills"></i> <span>Medicines</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ url('view_medicine') }}">Add Medicine</a></li>
                    <li><a class="nav-link" href="{{ url('show_medicine') }}">View Medicines</a></li>
                </ul>
            </li>

            <li class="menu-header">Prescriptions</li>
            <li class="dropdown {{ request()->is('pharmacy/prescriptions', 'pharmacy/all_prescriptions') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-prescription-bottle-alt"></i> <span>Prescriptions</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('pharmacy.prescriptions') }}">Pending Prescriptions</a></li>
                    <li><a class="nav-link" href="{{ route('pharmacy.all_prescriptions') }}">All Prescriptions</a></li>
                </ul>
            </li>

            <li class="menu-header">Inventory</li>
            <li class="dropdown {{ request()->is('Add_inventory', 'view_inventory', 'edit_inventory/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-boxes"></i> <span>Inventory</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ url('Add_inventory') }}">Add Inventory</a></li>
                    <li><a class="nav-link" href="{{ url('view_inventory') }}">View Inventory</a></li>
                </ul>
            </li>

            <li class="menu-header">Sales</li>
            <li class="dropdown {{ request()->routeIs('pharmacist.sales*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-cash-register"></i> <span>Sales</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('pharmacist.sales.create') }}">Add Sale</a></li>
                    <li><a class="nav-link" href="{{ route('pharmacist.sales') }}">View Sales</a></li>
                </ul>
            </li>
            <li class="menu-header">Insurance</li>
            <li class="{{ request()->is('claims*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pharmacist.claims.index') }}">
                    <i class="fas fa-file-invoice-dollar"></i> Insurance Claims
                </a>
            </li>
        </ul>
    </aside>
</div>