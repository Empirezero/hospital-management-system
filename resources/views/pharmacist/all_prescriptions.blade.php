@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>All Prescriptions</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('pharmacist.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">All Prescriptions</div>
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
                    <h4>Prescriptions History</h4>
                    <div class="card-header-action">
                        <a href="{{ route('pharmacy.prescriptions') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-clock"></i> View Pending
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
                                    <th>Doctor</th>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Days</th>
                                    <th>Instructions</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescriptions as $prescription)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $prescription->encounter?->appointment?->name ?? $prescription->patient?->name ?? 'N/A' }}</td>
                                    <td>{{ $prescription->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $prescription->medicine?->name ?? 'N/A' }}</td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ $prescription->frequency }}</td>
                                    <td>{{ $prescription->duration_days }} days</td>
                                    <td>{{ $prescription->instructions ?? '—' }}</td>
                                    <td>{{ $prescription->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $prescription->status == 'pending'   ? 'badge-warning' : '' }}
                                            {{ $prescription->status == 'dispensed' ? 'badge-success' : '' }}
                                            {{ $prescription->status == 'cancelled' ? 'badge-danger'  : '' }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($prescription->status == 'pending')
                                        <a href="{{ route('pharmacy.dispense', $prescription->id) }}"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Mark as dispensed?')">
                                            <i class="fas fa-check"></i> Dispense
                                        </a>
                                        <a href="{{ route('pharmacy.cancel_prescription', $prescription->id) }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Cancel this prescription?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        No prescriptions found.
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

@include('pharmacist.footer')