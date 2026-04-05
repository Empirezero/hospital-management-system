@include('doctor.header')
@include('doctor.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Encounters</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('doctor_index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Encounters</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>All Encounters</h4>
                    <div class="card-header-action">
                        <a href="{{ url('doctor_appointment') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Encounter from Appointment
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Visit Type</th>
                                    <th>Chief Complaint</th>
                                    <th>Visited At</th>
                                    <th>Status</th>
                                    <th>Prescriptions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($encounters as $encounter)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $encounter->appointment?->name ?? $encounter->patient?->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $encounter->visit_type)) }}</td>
                                    <td>{{ Str::limit($encounter->chief_complaint, 40) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($encounter->visited_at)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $encounter->status == 'open' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($encounter->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $encounter->prescriptions->count() }} medicines
                                        </span>
                                    </td>
                                    <td>
                                        @if($encounter->status == 'open')
                                        <a href="{{ route('doctor.prescriptions.create', $encounter->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-prescription-bottle-alt"></i> Prescribe
                                        </a>
                                        <a href="{{ route('doctor.encounter.close', $encounter->id) }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Close this encounter? No more prescriptions can be added.')">
                                            <i class="fas fa-lock"></i> Close
                                        </a>
                                        @else
                                        <span class="badge badge-secondary">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No encounters found. Start one from an appointment.
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

@include('doctor.footer')