@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $bill->bill_number }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('billing.index') }}">Billing</a></div>
                <div class="breadcrumb-item">{{ $bill->bill_number }}</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            <div class="row">

                {{-- LEFT COLUMN --}}
                <div class="col-md-8">

                    {{-- Bill Summary --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                Bill Summary
                                @php
                                $badge = match($bill->status->value) {
                                'draft' => 'badge-secondary',
                                'open' => 'badge-primary',
                                'partial' => 'badge-warning',
                                'paid' => 'badge-success',
                                'void' => 'badge-danger',
                                'written_off' => 'badge-dark',
                                default => 'badge-secondary',
                                };
                                @endphp
                                <span class="badge {{ $badge }} ml-2">
                                    {{ $bill->status->label() }}
                                </span>
                            </h4>
                            <div class="card-header-action">
                                @if($bill->status->value === 'draft')
                                <form action="{{ route('billing.open', $bill) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary"
                                        onclick="return confirm('Open this bill for payment?')">
                                        <i class="fas fa-unlock"></i> Open Bill
                                    </button>
                                </form>
                                @endif
                                @if(!in_array($bill->status->value, ['void','written_off','paid']))
                                <button class="btn btn-sm btn-danger ml-1"
                                    data-toggle="modal" data-target="#voidModal">
                                    <i class="fas fa-ban"></i> Void
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">Patient</td>
                                            <td><strong>{{ $bill->patient->user->name ?? 'N/A' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Patient No.</td>
                                            <td>{{ $bill->patient->patient_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Bill Type</td>
                                            <td>{{ $bill->bill_type->label() }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Created</td>
                                            <td>{{ $bill->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        @if($bill->due_date)
                                        <tr>
                                            <td class="text-muted">Due Date</td>
                                            <td class="{{ $bill->is_overdue ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $bill->due_date->format('d M Y') }}
                                                @if($bill->is_overdue)
                                                <span class="badge badge-danger">Overdue</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if($bill->notes)
                                        <tr>
                                            <td class="text-muted">Notes</td>
                                            <td>{{ $bill->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">Subtotal</td>
                                            <td class="text-right">KES {{ number_format($bill->subtotal, 2) }}</td>
                                        </tr>
                                        @if($bill->discount_amount > 0)
                                        <tr>
                                            <td class="text-muted">
                                                Discount
                                                @if($bill->discount_percent > 0)
                                                ({{ $bill->discount_percent }}%)
                                                @endif
                                            </td>
                                            <td class="text-right text-danger">
                                                - KES {{ number_format($bill->discount_amount, 2) }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($bill->insurance_covered > 0)
                                        <tr>
                                            <td class="text-muted">Insurance</td>
                                            <td class="text-right text-info">
                                                - KES {{ number_format($bill->insurance_covered, 2) }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr style="border-top: 2px solid #dee2e6;">
                                            <td><strong>Total</strong></td>
                                            <td class="text-right">
                                                <strong>KES {{ number_format($bill->total_amount, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Amount Paid</td>
                                            <td class="text-right text-success">
                                                KES {{ number_format($bill->amount_paid, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Balance Due</strong></td>
                                            <td class="text-right {{ $bill->balance_due > 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">
                                                KES {{ number_format($bill->balance_due, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bill Items --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>Bill Items</h4>
                            @if($bill->status->value === 'draft')
                            <div class="card-header-action">
                                <button class="btn btn-sm btn-primary"
                                    data-toggle="modal" data-target="#addServiceModal">
                                    <i class="fas fa-plus"></i> From Catalogue
                                </button>
                                <button class="btn btn-sm btn-secondary ml-1"
                                    data-toggle="modal" data-target="#addManualModal">
                                    <i class="fas fa-edit"></i> Manual Item
                                </button>
                            </div>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Insurance</th>
                                        @if($bill->status->value === 'draft')
                                        <th></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bill->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->description }}
                                            @if($item->notes)
                                            <br><small class="text-muted">{{ $item->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light border">
                                                {{ $item->item_type->label() }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                                        <td class="text-center">
                                            @if($item->is_insurance_covered)
                                            <span class="badge badge-success">
                                                KES {{ number_format($item->insurance_amount, 2) }}
                                            </span>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        @if($bill->status->value === 'draft')
                                        <td>
                                            <form action="{{ route('billing.items.remove', [$bill, $item]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Remove this item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No items added yet.
                                            @if($bill->status->value === 'draft')
                                            Click <strong>From Catalogue</strong> or <strong>Manual Item</strong> to add.
                                            @endif
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Payments --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>Payments</h4>
                            @if(in_array($bill->status->value, ['open', 'partial']))
                            <div class="card-header-action">
                                <button class="btn btn-sm btn-success"
                                    data-toggle="modal" data-target="#paymentModal">
                                    <i class="fas fa-money-bill-wave"></i> Cash / Manual
                                </button>
                                <button class="btn btn-sm btn-success ml-1"
                                    data-toggle="modal" data-target="#mpesaModal">
                                    <i class="fas fa-mobile-alt"></i> M-Pesa STK
                                </button>
                            </div>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Payment No.</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                        <th class="text-right">Amount (KES)</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bill->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_number }}</td>
                                        <td>{{ $payment->payment_method->label() }}</td>
                                        <td>{{ $payment->reference_number ?? '—' }}</td>
                                        <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @php
                                            $pb = match($payment->status->value) {
                                            'confirmed' => 'badge-success',
                                            'pending' => 'badge-warning',
                                            'failed' => 'badge-danger',
                                            'reversed' => 'badge-secondary',
                                            default => 'badge-secondary',
                                            };
                                            @endphp
                                            <span class="badge {{ $pb }}">
                                                {{ $payment->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->paid_at->format('d M Y H:i') }}</td>
                                        <td>
                                            @if($payment->receipt)
                                            <span class="badge badge-light border">
                                                {{ $payment->receipt->receipt_number }}
                                            </span>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No payments recorded yet.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-md-4">

                    {{-- Discount --}}
                    @if($bill->status->value === 'draft')
                    <div class="card">
                        <div class="card-header">
                            <h4>Apply Discount</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('billing.discount', $bill) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Discount %</label>
                                    <input type="number" name="discount_percent"
                                        class="form-control"
                                        min="0" max="100" step="0.01"
                                        value="{{ $bill->discount_percent }}"
                                        placeholder="e.g. 10">
                                </div>
                                <div class="form-group">
                                    <label>Reason</label>
                                    <input type="text" name="notes" class="form-control"
                                        placeholder="Reason for discount">
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-tag"></i> Apply Discount
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- Insurance Claims --}}
                    @if(!in_array($bill->status->value, ['void','written_off']))
                    <div class="card">
                        <div class="card-header">
                            <h4>Insurance Claims</h4>
                        </div>
                        <div class="card-body">
                            @forelse($bill->insuranceClaims as $claim)
                            <div class="mb-2 p-2 border rounded">
                                <strong>{{ $claim->claim_number }}</strong><br>
                                <small class="text-muted">
                                    {{ $claim->insuranceProvider->name ?? $claim->insurer_name }}
                                </small><br>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="badge {{ $claim->getStatusBadgeAttribute() }}">
                                        {{ ucfirst($claim->status) }}
                                    </span>
                                    <strong>KES {{ number_format($claim->claimed_amount, 2) }}</strong>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center">No claims yet.</p>
                            @endforelse
                            <button class="btn btn-info btn-block mt-2"
                                data-toggle="modal" data-target="#claimModal">
                                <i class="fas fa-plus"></i> New Claim
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Receipts --}}
                    @if($bill->receipts->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h4>Receipts</h4>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Receipt No.</th>
                                        <th class="text-right">Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bill->receipts as $receipt)
                                    <tr class="{{ $receipt->is_void ? 'text-muted' : '' }}">
                                        <td>
                                            {{ $receipt->receipt_number }}
                                            @if($receipt->is_void)
                                            <span class="badge badge-danger">Void</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            KES {{ number_format($receipt->amount_received, 2) }}
                                        </td>
                                        <td>{{ $receipt->issued_at->format('d M H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</div>

{{-- ===== MODALS ===== --}}

{{-- Add Service Modal --}}
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Service from Catalogue</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('billing.items.service', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Service <span class="text-danger">*</span></label>
                        <select name="service_id" class="form-control" required>
                            <option value="">-- Select Service --</option>
                            @foreach($services->groupBy(fn($s) => $s->category->label()) as $category => $items)
                            <optgroup label="{{ $category }}">
                                @foreach($items as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->name }}
                                    — KES {{ number_format($service->standard_price, 2) }}
                                    @if($service->is_nhif_covered)
                                    (SHA: {{ number_format($service->nhif_price, 2) }})
                                    @endif
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control"
                            value="1" min="0.01" step="0.01">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                id="useNhif" name="use_nhif_rate" value="1">
                            <label class="custom-control-label" for="useNhif">
                                Use SHA/NHIF rate
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Manual Item Modal --}}
<div class="modal fade" id="addManualModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Manual Item</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('billing.items.manual', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="item_type" class="form-control" required>
                            <option value="consultation">Consultation</option>
                            <option value="lab">Laboratory</option>
                            <option value="pharmacy">Pharmacy</option>
                            <option value="procedure">Procedure</option>
                            <option value="bed">Bed Charge</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control"
                            required placeholder="e.g. General Consultation">
                    </div>
                    <div class="form-group">
                        <label>Unit Price (KES) <span class="text-danger">*</span></label>
                        <input type="number" name="unit_price" class="form-control"
                            required min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control"
                            value="1" min="0.01" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Discount %</label>
                        <input type="number" name="discount_percent" class="form-control"
                            value="0" min="0" max="100" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('billing.payments.store', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control"
                            id="paymentMethod" required>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa (Manual Entry)</option>
                            <option value="nhif">NHIF</option>
                            <option value="sha">SHA</option>
                            <option value="insurance">Insurance</option>
                            <option value="corporate">Corporate / Company</option>
                            <option value="waiver">Waiver</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (KES) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control"
                            required min="1" step="0.01"
                            value="{{ $bill->balance_due }}">
                    </div>
                    <div class="form-group" id="referenceGroup" style="display:none;">
                        <label>Reference Number</label>
                        <input type="text" name="reference_number" class="form-control"
                            placeholder="M-Pesa code, auth no, receipt no">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control"
                            placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- M-Pesa STK Modal --}}
<div class="modal fade" id="mpesaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-mobile-alt"></i> M-Pesa STK Push
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('billing.mpesa.initiate', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        The patient will receive a PIN prompt on their phone.
                        Payment confirms automatically once PIN is entered.
                    </div>
                    <div class="form-group">
                        <label>Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" class="form-control"
                            required placeholder="07XXXXXXXX or 2547XXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label>Amount (KES)</label>
                        <input type="number" name="amount" class="form-control"
                            min="1" step="1"
                            value="{{ (int) ceil($bill->balance_due) }}">
                        <small class="text-muted">Leave as is to charge full balance</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Send STK Push
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Insurance Claim Modal --}}
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Insurance Claim</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('billing.claims.store', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Insurance Provider <span class="text-danger">*</span></label>
                        <select name="insurance_provider_id" class="form-control" required>
                            <option value="">-- Select Provider --</option>
                            @foreach($providers as $provider)
                            <option value="{{ $provider->id }}">
                                {{ $provider->name }}
                                ({{ strtoupper($provider->type->value) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Member Number <span class="text-danger">*</span></label>
                        <input type="text" name="member_number" class="form-control"
                            required placeholder="Insurance card number">
                    </div>
                    <div class="form-group">
                        <label>Scheme Name</label>
                        <input type="text" name="scheme_name" class="form-control"
                            placeholder="e.g. Jubilee Gold Plus">
                    </div>
                    <div class="form-group">
                        <label>Claimed Amount (KES) <span class="text-danger">*</span></label>
                        <input type="number" name="claimed_amount" class="form-control"
                            required min="1" step="0.01"
                            value="{{ $bill->total_amount }}">
                    </div>
                    <div class="form-group">
                        <label>Principal Member Name</label>
                        <input type="text" name="principal_member_name" class="form-control"
                            placeholder="If patient is a dependent">
                    </div>
                    <div class="form-group">
                        <label>Relationship to Principal</label>
                        <select name="relationship_to_principal" class="form-control">
                            <option value="Self">Self</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Card Expiry Date</label>
                        <input type="date" name="card_expiry_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane"></i> Submit Claim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Void Modal --}}
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle"></i> Void Bill
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('billing.void', $bill) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        This action is <strong>irreversible</strong>.
                        The bill will be permanently voided.
                    </div>
                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            required minlength="5"
                            placeholder="Reason for voiding this bill..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Void Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('mpesa_transaction_id'))
<div class="modal fade show d-block" id="mpesaStatusModal" tabindex="-1"
    style="background:rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-mobile-alt"></i> M-Pesa Status
                </h5>
            </div>
            <div class="modal-body text-center">
                <div id="mpesaStatusIcon">
                    <i class="fas fa-spinner fa-spin fa-3x text-success"></i>
                    <p class="mt-3">Waiting for payment confirmation...</p>
                    <small class="text-muted">Ask patient to enter M-Pesa PIN</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary btn-sm" onclick="stopPolling()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var transactionId = {
        {
            session('mpesa_transaction_id')
        }
    };
    var pollInterval;

    function pollStatus() {
        fetch('{{ route("billing.mpesa.status", "") }}/' + transactionId)
            .then(res => res.json())
            .then(data => {
                if (data.is_complete) {
                    stopPolling();

                    var icon = document.getElementById('mpesaStatusIcon');

                    if (data.status === 'completed') {
                        icon.innerHTML = `
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                            <p class="mt-3 text-success font-weight-bold">Payment Confirmed!</p>
                            <small class="text-muted">Receipt: ${data.mpesa_receipt_number}</small>
                        `;
                        // Reload page after 2 seconds to show updated bill
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        icon.innerHTML = `
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                            <p class="mt-3 text-danger font-weight-bold">Payment Failed</p>
                            <small class="text-muted">${data.status}</small>
                        `;
                    }
                }
            })
            .catch(err => console.log('Poll error:', err));
    }

    function stopPolling() {
        clearInterval(pollInterval);
        document.getElementById('mpesaStatusModal').style.display = 'none';
    }

    // Poll every 5 seconds
    pollInterval = setInterval(pollStatus, 5000);

    // Stop polling after 2 minutes
    setTimeout(stopPolling, 120000);
</script>
@endif

@include('admin.footer')

<script>
    // Show reference number field for methods that require it
    document.getElementById('paymentMethod').addEventListener('change', function() {
        const requiresRef = ['mpesa', 'nhif', 'sha', 'insurance', 'corporate'];
        const refGroup = document.getElementById('referenceGroup');
        refGroup.style.display = requiresRef.includes(this.value) ? 'block' : 'none';
    });
</script>