@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Appointments</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">My Appointments</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- Stats Row --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total</h4>
                            </div>
                            <div class="card-body">{{ $appoint->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">{{ $appoint->where('status', 'pending')->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Confirmed</h4>
                            </div>
                            <div class="card-body">{{ $appoint->where('status', 'confirmed')->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Cancelled</h4>
                            </div>
                            <div class="card-body">{{ $appoint->where('status', 'cancelled')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Appointment History</h4>
                    <div class="card-header-action">
                        <a href="{{ url('add_appointment_view') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Book New Appointment
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Doctor</th>
                                    <th>Speciality</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appoint as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($appointment->doctor?->image)
                                            <img src="{{ asset('doctorimage/' . $appointment->doctor->image) }}"
                                                class="rounded-circle mr-2"
                                                style="height:35px; width:35px; object-fit:cover;">
                                            @else
                                            <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                                                class="rounded-circle mr-2"
                                                style="height:35px; width:35px; object-fit:cover;">
                                            @endif
                                            {{ $appointment->doctor?->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>{{ $appointment->doctor?->speciality ?? '—' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('g:i A') }}</td>
                                    <td>{{ Str::limit($appointment->message, 40) ?? '—' }}</td>
                                    <td>
                                        <span class="badge
                                            @if($appointment->status == 'pending')   badge-warning
                                            @elseif($appointment->status == 'confirmed') badge-success
                                            @elseif($appointment->status == 'completed') badge-info
                                            @elseif($appointment->status == 'cancelled') badge-danger
                                            @elseif($appointment->status == 'no_show')   badge-secondary
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                                        <a href="{{ url('cancel_appoint', $appointment->id) }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">You have no appointments yet.</p>
                                        <a href="{{ url('add_appointment_view') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Book Your First Appointment
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

@include('patient.footer')