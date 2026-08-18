@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Prescriptions</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('patient.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Prescriptions</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Prescription History</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Prescribed By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescriptions as $prescription)
                                <tr>
                                    <td><strong>{{ $prescription->medicine?->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ ucfirst($prescription->frequency) }}</td>
                                    <td>{{ $prescription->duration_days }} day{{ $prescription->duration_days > 1 ? 's' : '' }}</td>
                                    <td>Dr. {{ $prescription->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $prescription->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $prescription->status == 'pending'   ? 'badge-warning' : '' }}
                                            {{ $prescription->status == 'dispensed' ? 'badge-success' : '' }}
                                            {{ $prescription->status == 'cancelled' ? 'badge-danger'  : '' }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($prescription->instructions)
                                <tr>
                                    <td colspan="7" class="pt-0 pb-3">
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>{{ $prescription->instructions }}</small>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No prescriptions yet.
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