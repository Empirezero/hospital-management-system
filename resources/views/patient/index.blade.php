<section class="section">
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    @if($upcomingAppointment)
    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <i class="fas fa-calendar-check mr-2"></i>
        <div>
            <strong>Next appointment:</strong>
            Dr. {{ $upcomingAppointment->doctor?->name ?? 'N/A' }}
            on {{ $upcomingAppointment->scheduled_at?->format('D, d M Y \a\t h:i A') }}
            <span class="badge badge-{{ $upcomingAppointment->status == 'confirmed' ? 'success' : 'warning' }} ml-2">
                {{ ucfirst($upcomingAppointment->status) }}
            </span>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Upcoming Appointments</h4>
                    </div>
                    <div class="card-body">{{ $upcomingCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Visits</h4>
                    </div>
                    <div class="card-body">{{ $totalVisits }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Active Prescriptions</h4>
                    </div>
                    <div class="card-body">{{ $activePrescriptions }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Pending Claims</h4>
                    </div>
                    <div class="card-body">{{ $pendingClaims }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>My Appointments</h4>
                    <div class="card-header-action">
                        <a href="{{ url('add_appointment_view') }}" class="btn btn-primary btn-sm">Book Appointment</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appoint->take(5) as $appointment)
                                <tr>
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
                                    <td colspan="3" class="text-center text-muted py-3">
                                        No appointments yet. <a href="{{ url('add_appointment_view') }}">Book one now</a>.
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
</section>