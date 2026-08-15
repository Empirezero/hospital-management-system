@include('nurse.header')
@include('nurse.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Appointments</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('nurse.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Appointments</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Pending &amp; Confirmed Appointments</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Contact</th>
                                    <th>Doctor</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td><strong>{{ $appointment->name }}</strong></td>
                                    <td>
                                        {{ $appointment->email }}
                                        @if($appointment->number)
                                        <br><small class="text-muted">{{ $appointment->number }}</small>
                                        @endif
                                    </td>
                                    <td>Dr. {{ $appointment->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->scheduled_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $appointment->status == 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No pending or confirmed appointments.</td>
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

@include('nurse.footer')