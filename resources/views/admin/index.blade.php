<section class="section">
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Doctors</h4>
                    </div>
                    <div class="card-body">{{ $totalDoctors }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Patients</h4>
                    </div>
                    <div class="card-body">{{ $totalPatients }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Pending Appointments</h4>
                    </div>
                    <div class="card-body">{{ $pendingAppointments }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Users</h4>
                    </div>
                    <div class="card-body">{{ $totalUsers }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Appointments Today</h4>
                    </div>
                    <div class="card-body">{{ $appointmentsToday }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Occupied Beds</h4>
                    </div>
                    <div class="card-body">{{ $occupiedBeds }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Available Beds</h4>
                    </div>
                    <div class="card-body">{{ $availableBeds }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Appointments</h4>
                    </div>
                    <div class="card-body">{{ $totalAppointments }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Appointments</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.appointments') }}" class="btn btn-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->patient?->user?->name ?? $appointment->name }}</td>
                                    <td>Dr. {{ $appointment->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->scheduled_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{
                                            $appointment->status == 'confirmed' ? 'success' :
                                            ($appointment->status == 'pending'  ? 'warning' :
                                            ($appointment->status == 'cancelled' ? 'danger' : 'secondary'))
                                        }}">{{ ucfirst($appointment->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No appointments yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>