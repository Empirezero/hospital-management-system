@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Insurance Claims</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item active">Claims</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Claimed</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalClaimed, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Approved</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalApproved, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">{{ $totalPending }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Paid</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalPaid, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>All Claims</h4>
                    <div class="card-header-action">
                        <a href="{{ route('pharmacist.claims.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Claim
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Insurer</th>
                                    <th>Policy No.</th>
                                    <th>Claimed</th>
                                    <th>Approved</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $claim)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $claim->patient?->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $claim->insurer_name }}</td>
                                    <td>{{ $claim->policy_number }}</td>
                                    <td>Ksh {{ number_format($claim->claimed_amount, 2) }}</td>
                                    <td>
                                        {{ $claim->approved_amount
                                            ? 'Ksh ' . number_format($claim->approved_amount, 2)
                                            : '—' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $claim->status_badge }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $claim->submitted_at?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('pharmacist.claims.show', $claim->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($claim->status === 'draft')
                                        <form method="POST"
                                            action="{{ route('pharmacist.claims.submit', $claim->id) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Submit this claim to insurer?')">
                                            @csrf
                                            <button class="btn btn-sm btn-primary">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No claims found.</td>
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