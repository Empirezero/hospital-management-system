@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>New Insurance Claim</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('pharmacist.claims.index') }}">Claims</a></div>
                <div class="breadcrumb-item active">New Claim</div>
            </div>
        </div>

        <div class="section-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="col-12 col-md-8 col-lg-7 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Claim Details</h4>
                        <div class="card-header-action">
                            <a href="{{ route('pharmacist.claims.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pharmacist.claims.store') }}">
                            @csrf

                            {{-- Link to Sale --}}
                            <div class="form-group">
                                <label>Linked Sale <span class="text-danger">*</span></label>
                                <select class="form-control" name="sale_id" id="sale_id" required>
                                    <option value="">Select insurance sale</option>
                                    @foreach($sales as $sale)
                                    <option value="{{ $sale->id }}"
                                        data-patient="{{ $sale->patient_id }}"
                                        data-amount="{{ $sale->total_price }}"
                                        {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                        #{{ $sale->id }} —
                                        {{ $sale->patient?->user?->name ?? 'Unknown' }} —
                                        {{ $sale->medicine?->name ?? 'Unknown' }} —
                                        Ksh {{ number_format($sale->total_price, 2) }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($sales->isEmpty())
                                <small class="text-muted">
                                    No unclaimed insurance sales found.
                                </small>
                                @endif
                            </div>

                            {{-- Patient --}}
                            <div class="form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select class="form-control" name="patient_id" id="patient_id" required>
                                    <option value="">Select patient</option>
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user?->name }} ({{ $patient->patient_number }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Insurer --}}
                            <div class="form-group">
                                <label>Insurer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="insurer_name"
                                    placeholder="e.g. NHIF, AAR, Jubilee"
                                    value="{{ old('insurer_name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Policy Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="policy_number"
                                    placeholder="Patient's policy number"
                                    value="{{ old('policy_number') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Member Number</label>
                                <input type="text" class="form-control" name="member_number"
                                    placeholder="Member/card number (optional)"
                                    value="{{ old('member_number') }}">
                            </div>

                            {{-- Financials --}}
                            <div class="form-group">
                                <label>Claimed Amount (Ksh) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control"
                                    name="claimed_amount" id="claimed_amount"
                                    placeholder="Amount to claim from insurer"
                                    value="{{ old('claimed_amount') }}" required>
                                <small class="text-muted">Auto-filled from sale — adjust if insurer covers partial amount.</small>
                            </div>

                            <div class="form-group">
                                <label>Patient Co-pay (Ksh)</label>
                                <input type="number" step="0.01" class="form-control"
                                    name="patient_copay"
                                    placeholder="Amount patient pays out of pocket"
                                    value="{{ old('patient_copay', 0) }}">
                            </div>

                            <div class="form-group">
                                <label>Due Date</label>
                                <input type="date" class="form-control" name="due_date"
                                    value="{{ old('due_date') }}">
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3"
                                    placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="card-footer text-right px-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Claim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Auto-fill patient and claimed amount from selected sale
    $('#sale_id').on('change', function() {
        var selected = $(this).find(':selected');
        var patientId = selected.data('patient');
        var amount = selected.data('amount');

        if (patientId) $('#patient_id').val(patientId);
        if (amount) $('#claimed_amount').val(parseFloat(amount).toFixed(2));
    });

    // Trigger on load if old value exists
    $(document).ready(function() {
        if ($('#sale_id').val()) $('#sale_id').trigger('change');
    });
</script>

@include('pharmacist.footer')