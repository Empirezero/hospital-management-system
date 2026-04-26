@php $unread = auth()->user()?->unreadNotifications()->count()?? o; @endphp

<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">

        {{-- Bell Icon --}}
        <li class="dropdown notification-list">
            <a href="#" class="nav-link dropdown-toggle nav-link-lg" data-toggle="dropdown">
                <i class="fas fa-bell"></i>
                @if($unread > 0)
                <span class="badge badge-danger navbar-badge">{{ $unread }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                <div class="dropdown-header">
                    <h6 class="mb-0">Notifications
                        @if($unread > 0)
                        <span class="badge badge-danger float-right">{{ $unread }}</span>
                        @endif
                    </h6>
                </div>
                <div class="dropdown-divider"></div>
                @forelse(auth()->user()?->unreadNotifications->take(5) ?? [] as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item"
                    onclick="markRead('{{ $notification->id }}')">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            @php
                            $icon = match($notification->data['type'] ?? '') {
                            'lab_result' => 'fas fa-flask text-success',
                            'appointment' => 'fas fa-calendar text-primary',
                            'prescription' => 'fas fa-pills text-warning',
                            'claim' => 'fas fa-file-invoice text-info',
                            default => 'fas fa-bell text-secondary',
                            };
                            @endphp
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold" style="font-size:0.85rem;">
                                {{ $notification->data['title'] }}
                            </p>
                            <small class="text-muted">{{ $notification->data['message'] }}</small>
                            <br>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                @empty
                <div class="dropdown-item text-center text-muted py-3">No new notifications</div>
                @endforelse
                <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary">
                    View All Notifications
                </a>
            </div>
        </li>
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
            <li class="menu-header">Bed Management</li>
            <li class="dropdown {{ request()->is('beds*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-bed"></i> <span>Beds</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('beds.overview') }}">Bed Overview</a></li>
                    <li><a class="nav-link" href="{{ route('admin.beds.wards') }}">Manage Wards</a></li>
                    <li><a class="nav-link" href="{{ route('admin.beds.admissions') }}">Admissions</a></li>
                    <li><a class="nav-link" href="{{ route('admin.beds.admit') }}">Admit Patient</a></li>
                </ul>
            </li>
            @if(in_array(auth()->user()->role, ['admin', 'accountant']))
            <li class="menu-header">Billing</li>
            <li class="dropdown {{ request()->is('billing*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-file-invoice-dollar"></i> <span>Billing</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('billing.index') }}">All Bills</a></li>
                    <li><a class="nav-link" href="{{ route('billing.create') }}">New Bill</a></li>
                </ul>
            </li>
            @endif

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