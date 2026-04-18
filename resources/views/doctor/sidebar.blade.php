@php $unread = auth()->user()?->unreadNotifications->count() ?? 0; @endphp

<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">

        <li class="dropdown notification-list">
            <a href="#" class="nav-link dropdown-toggle nav-link-lg"
                data-toggle="dropdown">
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
                <a href="{{ $notification->data['url'] ?? '#' }}"
                    class="dropdown-item"
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
                <div class="dropdown-item text-center text-muted py-3">
                    No new notifications
                </div>
                @endforelse

                <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary">
                    View All Notifications
                </a>
            </div>
        </li>
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
            <li class="{{ request()->is('reports/doctor') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.doctor') }}">
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

            <li class="menu-header">Laboratory</li>
            <li class="dropdown {{ request()->is('doctor/lab*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-flask"></i> <span>Lab Tests</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('doctor.lab.request') }}">Request Test</a></li>
                    <li><a class="nav-link" href="{{ route('doctor.lab.requests') }}">My Requests</a></li>
                </ul>
            </li>
            <li class="menu-header">Reports</li>
            <li class="{{ request()->is('reports/doctor') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.doctor') }}">
                    <i class="fas fa-chart-bar"></i> My Reports
                </a>
            </li>
        </ul>
    </aside>
</div>