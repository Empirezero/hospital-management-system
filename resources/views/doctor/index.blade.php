

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <div class="section-body">


            {{-- Stats Row 1 --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Appointments</h4>
                            </div>
                            <div class="card-body">{{ $totalAppointments }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">{{ $pendingAppointments }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Confirmed</h4>
                            </div>
                            <div class="card-body">{{ $confirmedAppointments }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Today's Appointments</h4>
                            </div>
                            <div class="card-body">{{ $todayAppointments }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Row 2 --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Completed</h4>
                            </div>
                            <div class="card-body">{{ $completedAppointments }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Encounters</h4>
                            </div>
                            <div class="card-body">{{ $totalEncounters }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Open Encounters</h4>
                            </div>
                            <div class="card-body">{{ $openEncounters }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Prescriptions</h4>
                            </div>
                            <div class="card-body">{{ $totalPrescriptions }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="row">

                {{-- Recent Appointments --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Appointments</h4>
                            <div class="card-header-action">
                                <a href="{{ url('doctor_appointment') }}" class="btn btn-primary btn-sm">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAppointments as $apt)
                                        <tr>
                                            <td>{{ $apt->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($apt->scheduled_at)->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge
                                                    {{ $apt->status == 'pending'   ? 'badge-warning'   : '' }}
                                                    {{ $apt->status == 'confirmed' ? 'badge-success'   : '' }}
                                                    {{ $apt->status == 'completed' ? 'badge-info'      : '' }}
                                                    {{ $apt->status == 'cancelled' ? 'badge-danger'    : '' }}">
                                                    {{ ucfirst($apt->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('doctor.encounter.create', $apt->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-stethoscope"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                No appointments yet.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Encounters --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Recent Encounters</h4>
                            <div class="card-header-action">
                                <a href="{{ route('doctor.encounters') }}" class="btn btn-primary btn-sm">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentEncounters as $encounter)
                                        <tr>
                                            <td>{{ $encounter->appointment?->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $encounter->visit_type)) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($encounter->visited_at)->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge {{ $encounter->status == 'open' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ ucfirst($encounter->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                No encounters yet.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</div>
