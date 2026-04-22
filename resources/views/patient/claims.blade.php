@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Insurance Claims</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item active">Insurance Claims</div>
            </div>
        </div>

        <div class="section-body">

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Claims</h4>
                            </div>
                            <div class="card-body">{{ $claims->count() }}</div>
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
                            <div class="card-body">
                                {{ $claims->whereIn('status', ['submitted','under_review'])->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Approved</h4>
                            </div>
                            <div class="card-body">
                                {{ $claims->whereIn('status', ['approved','partial','paid'])->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rejected</h4>
                            </div>
                            <div class="card-body">
                                {{ $claims->where('status', 'rejected')->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Claims History</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Insurer</th>
                                    <th>Policy No.</th>
                                    <th>Medicine</th>
                                    <th>Claimed</th>
                                    <th>Approved</th>
                                    <th>Co-pay</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $claim)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $claim->insurer_name }}</td>
                                    <td>{{ $claim->policy_number }}</td>
                                    <td>{{ $claim->sale?->medicine?->name ?? '—' }}</td>
                                    <td>Ksh {{ number_format($claim->claimed_amount, 2) }}</td>
                                    <td>
                                        {{ $claim->approved_amount
                                            ? 'Ksh ' . number_format($claim->approved_amount, 2)
                                            : '—' }}
                                    </td>
                                    <td>Ksh {{ number_format($claim->patient_copay, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $claim->status_badge }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $claim->created_at->format('d M Y') }}</td>
                                </tr>

                                {{-- Rejection reason --}}
                                @if($claim->status === 'rejected' && $claim->rejection_reason)
                                <tr>
                                    <td colspan="9">
                                        <div class="alert alert-danger mb-0 py-2">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Rejection Reason:</strong>
                                            {{ $claim->rejection_reason }}
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-file-invoice fa-3x text-muted d-block mb-3"></i>
                                        <p class="text-muted">No insurance claims found.</p>
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