@include('admin.header')
@include('admin.menusidebar')

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

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4">
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
                <div class="col-lg-2 col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Approved</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalApproved, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
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
                <div class="col-lg-2 col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rejected</h4>
                            </div>
                            <div class="card-body">{{ $totalRejected }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Paid</h4>
                            </div>
                            <div class="card-body">Ksh {{ number_format($totalPaid, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Claims Table --}}
            <div class="card">
                <div class="card-header">
                    <h4>All Claims</h4>
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
                                    <th>Due Date</th>
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
                                    <td>{{ $claim->approved_amount
                                        ? 'Ksh ' . number_format($claim->approved_amount, 2)
                                        : '—' }}</td>
                                    <td>
                                        <span class="badge {{ $claim->status_badge }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $claim->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('admin.claims.show', $claim->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!in_array($claim->status, ['paid', 'draft']))
                                        <button class="btn btn-sm btn-warning"
                                            data-toggle="modal"
                                            data-target="#updateModal{{ $claim->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
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

{{-- ── Modals — outside the table, before footer ──────────────── --}}
@foreach($claims as $claim)
@if(!in_array($claim->status, ['paid', 'draft']))
<div class="modal fade" id="updateModal{{ $claim->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Claim #{{ $claim->id }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.claims.update_status', $claim->id) }}">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="under_review" {{ $claim->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="approved" {{ $claim->status == 'approved'     ? 'selected' : '' }}>Approved</option>
                            <option value="partial" {{ $claim->status == 'partial'      ? 'selected' : '' }}>Partially Approved</option>
                            <option value="rejected" {{ $claim->status == 'rejected'     ? 'selected' : '' }}>Rejected</option>
                            <option value="paid" {{ $claim->status == 'paid'         ? 'selected' : '' }}>Paid</option>
                            <option value="appealed" {{ $claim->status == 'appealed'     ? 'selected' : '' }}>Appealed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Approved Amount (Ksh)</label>
                        <input type="number" step="0.01" class="form-control"
                            name="approved_amount"
                            value="{{ $claim->approved_amount }}">
                    </div>

                    <div class="form-group">
                        <label>Patient Co-pay (Ksh)</label>
                        <input type="number" step="0.01" class="form-control"
                            name="patient_copay"
                            value="{{ $claim->patient_copay }}">
                    </div>

                    <div class="form-group">
                        <label>Response Date</label>
                        <input type="date" class="form-control" name="response_date"
                            value="{{ $claim->response_date?->format('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" class="form-control" name="payment_date"
                            value="{{ $claim->payment_date?->format('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label>Payment Reference</label>
                        <input type="text" class="form-control" name="payment_reference"
                            value="{{ $claim->payment_reference }}"
                            placeholder="Insurer payment reference">
                    </div>

                    <div class="form-group">
                        <label>Rejection Reason</label>
                        <textarea class="form-control" name="rejection_reason" rows="2"
                            placeholder="Required if rejecting...">{{ $claim->rejection_reason }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ $claim->notes }}</textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@include('admin.footer')