@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Claim Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('pharmacist.claims.index') }}">Claims</a></div>
                <div class="breadcrumb-item active">Claim #{{ $claim->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Claim #{{ $claim->id }}</h4>
                        <div class="card-header-action">
                            <span class="badge {{ $claim->status_badge }} mr-2" style="font-size:0.85rem;">
                                {{ ucfirst($claim->status) }}
                            </span>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Patient & Insurer --}}
                        <h6 class="text-muted mb-3">Patient & Insurer</h6>
                        <table class="table table-bordered mb-4">
                            <tr>
                                <td width="35%" class="text-muted">Patient</td>
                                <td><strong>{{ $claim->patient?->user?->name ?? 'N/A' }}</strong>
                                    ({{ $claim->patient?->patient_number }})
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Insurer</td>
                                <td>{{ $claim->insurer_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Policy Number</td>
                                <td>{{ $claim->policy_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Member Number</td>
                                <td>{{ $claim->member_number ?? '—' }}</td>
                            </tr>
                        </table>

                        {{-- Sale Info --}}
                        <h6 class="text-muted mb-3">Linked Sale</h6>
                        <table class="table table-bordered mb-4">
                            <tr>
                                <td width="35%" class="text-muted">Medicine</td>
                                <td>{{ $claim->sale?->medicine?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Quantity</td>
                                <td>{{ $claim->sale?->quantity_sold }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sale Total</td>
                                <td>Ksh {{ number_format($claim->sale?->total_price, 2) }}</td>
                            </tr>
                            @if($claim->sale?->prescription)
                            <tr>
                                <td class="text-muted">Prescription</td>
                                <td>#{{ $claim->sale->prescription->id }}</td>
                            </tr>
                            @endif
                        </table>

                        {{-- Financials --}}
                        <h6 class="text-muted mb-3">Financials</h6>
                        <table class="table table-bordered mb-4">
                            <tr>
                                <td width="35%" class="text-muted">Claimed Amount</td>
                                <td>Ksh {{ number_format($claim->claimed_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Approved Amount</td>
                                <td>{{ $claim->approved_amount
                                    ? 'Ksh ' . number_format($claim->approved_amount, 2)
                                    : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Patient Co-pay</td>
                                <td>Ksh {{ number_format($claim->patient_copay, 2) }}</td>
                            </tr>
                            @if($claim->payment_reference)
                            <tr>
                                <td class="text-muted">Payment Reference</td>
                                <td>{{ $claim->payment_reference }}</td>
                            </tr>
                            @endif
                        </table>

                        {{-- Timeline --}}
                        <h6 class="text-muted mb-3">Timeline</h6>
                        <table class="table table-bordered mb-4">
                            <tr>
                                <td width="35%" class="text-muted">Created</td>
                                <td>{{ $claim->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted</td>
                                <td>{{ $claim->submitted_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Response Date</td>
                                <td>{{ $claim->response_date?->format('d M Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Payment Date</td>
                                <td>{{ $claim->payment_date?->format('d M Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Due Date</td>
                                <td>{{ $claim->due_date?->format('d M Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted By</td>
                                <td>{{ $claim->submittedBy?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Reviewed By</td>
                                <td>{{ $claim->reviewedBy?->name ?? '—' }}</td>
                            </tr>
                        </table>

                        {{-- Rejection Reason --}}
                        @if($claim->rejection_reason)
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-times-circle"></i> Rejection Reason:</strong>
                            <p class="mb-0 mt-1">{{ $claim->rejection_reason }}</p>
                        </div>
                        @endif

                        {{-- Notes --}}
                        @if($claim->notes)
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle"></i> Notes:</strong>
                            <p class="mb-0 mt-1">{{ $claim->notes }}</p>
                        </div>
                        @endif

                        {{-- Submit button for draft --}}
                        @if($claim->status === 'draft')
                        <form method="POST"
                            action="{{ route('pharmacist.claims.submit', $claim->id) }}"
                            onsubmit="return confirm('Submit this claim to insurer?')">
                            @csrf
                            <button class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane"></i> Submit to Insurer
                            </button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pharmacist.footer')