\@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Sale</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('pharmacist.sales') }}">Sales</a></div>
                <div class="breadcrumb-item active">Edit Sale</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

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
                        <h4>Sale #{{ $sale->id }}</h4>
                        <div class="card-header-action">
                            <a href="{{ route('pharmacist.sales') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pharmacist.sales.update', $sale->id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Sale Type --}}
                            <div class="form-group">
                                <label>Sale Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="sale_type" id="sale_type" required>
                                    <option value="otc" {{ old('sale_type', $sale->sale_type) == 'otc' ? 'selected' : '' }}>Over the Counter (OTC)</option>
                                    <option value="prescription" {{ old('sale_type', $sale->sale_type) == 'prescription' ? 'selected' : '' }}>Prescription</option>
                                    <option value="insurance" {{ old('sale_type', $sale->sale_type) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                </select>
                            </div>

                            {{-- Prescription --}}
                            <div class="form-group" id="prescription_group"
                                style="{{ old('sale_type', $sale->sale_type) === 'prescription' ? '' : 'display:none;' }}">
                                <label>Prescription</label>
                                <select class="form-control" name="prescription_id" id="prescription_id">
                                    <option value="">Select prescription</option>
                                    @foreach($prescriptions as $prescription)
                                    <option value="{{ $prescription->id }}"
                                        data-medicine="{{ $prescription->medicine_id }}"
                                        {{ old('prescription_id', $sale->prescription_id) == $prescription->id ? 'selected' : '' }}>
                                        {{ $prescription->patient?->name ?? 'Unknown' }} —
                                        {{ $prescription->medicine?->name ?? 'Unknown' }}
                                        (Dr. {{ $prescription->doctor?->name ?? 'Unknown' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Medicine --}}
                            <div class="form-group">
                                <label>Medicine <span class="text-danger">*</span></label>
                                <select class="form-control" name="medicine_id" id="medicine_id" required>
                                    <option value="">Select medicine</option>
                                    @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}"
                                        {{ old('medicine_id', $sale->medicine_id) == $medicine->id ? 'selected' : '' }}>
                                        {{ $medicine->name }} (Stock: {{ $medicine->stock }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Unit Price (Ksh)</label>
                                <input type="text" id="unit_price" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Available Stock</label>
                                <input type="text" id="available_stock" class="form-control" readonly>
                            </div>

                            {{-- Quantity --}}
                            <div class="form-group">
                                <label>Quantity Sold <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity_sold"
                                    id="quantity_sold" min="1"
                                    value="{{ old('quantity_sold', $sale->quantity_sold) }}" required>
                                <small class="text-muted">Original quantity: {{ $sale->quantity_sold }}</small>
                            </div>

                            <div class="form-group">
                                <label>Total Price (Ksh)</label>
                                <input type="text" id="total_price" class="form-control" readonly
                                    value="Ksh {{ number_format($sale->total_price, 2) }}">
                            </div>

                            {{-- Payment Method --}}
                            <div class="form-group">
                                <label>Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="mpesa" {{ old('payment_method', $sale->payment_method) == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                    <option value="insurance" {{ old('payment_method', $sale->payment_method) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="credit" {{ old('payment_method', $sale->payment_method) == 'credit' ? 'selected' : '' }}>Credit</option>
                                </select>
                            </div>

                            {{-- Payment Reference --}}
                            <div class="form-group" id="reference_group"
                                style="{{ in_array(old('payment_method', $sale->payment_method), ['mpesa','insurance','credit']) ? '' : 'display:none;' }}">
                                <label>Payment Reference</label>
                                <input type="text" class="form-control" name="payment_reference"
                                    placeholder="e.g. M-Pesa code or insurance ref"
                                    value="{{ old('payment_reference', $sale->payment_reference) }}">
                            </div>

                            {{-- Payment Status --}}
                            <div class="form-group">
                                <label>Payment Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="payment_status" required>
                                    <option value="paid" {{ old('payment_status', $sale->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="pending" {{ old('payment_status', $sale->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="billed" {{ old('payment_status', $sale->payment_status) == 'billed' ? 'selected' : '' }}>Billed</option>
                                </select>
                            </div>

                            {{-- Billed To (shown when status = billed) --}}
                            <div class="form-group" id="billed_group"
                                style="{{ old('payment_status', $sale->payment_status) === 'billed' ? '' : 'display:none;' }}">
                                <label>Billed To</label>
                                <input type="text" class="form-control" name="billed_to"
                                    placeholder="e.g. Insurance company name"
                                    value="{{ old('billed_to', $sale->billed_to) }}">
                            </div>

                            {{-- Bill Due Date (shown when status = billed) --}}
                            <div class="form-group" id="due_date_group"
                                style="{{ old('payment_status', $sale->payment_status) === 'billed' ? '' : 'display:none;' }}">
                                <label>Bill Due Date</label>
                                <input type="date" class="form-control" name="bill_due_date"
                                    value="{{ old('bill_due_date', $sale->bill_due_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="card-footer text-right px-0">
                                <a href="{{ route('pharmacist.sales') }}" class="btn btn-secondary mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Sale
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
    var unitPrice = 0;
    var originalQty = {
        {
            $sale - > quantity_sold
        }
    };

    // Sale type toggle
    $('#sale_type').on('change', function() {
        if ($(this).val() === 'prescription') {
            $('#prescription_group').show();
        } else {
            $('#prescription_group').hide();
            $('#prescription_id').val('');
        }
    });

    // Payment method toggle
    $('#payment_method').on('change', function() {
        var method = $(this).val();
        if (method === 'mpesa' || method === 'insurance' || method === 'credit') {
            $('#reference_group').show();
        } else {
            $('#reference_group').hide();
            $('input[name=payment_reference]').val('');
        }
    });

    // Payment status toggle
    $('select[name=payment_status]').on('change', function() {
        if ($(this).val() === 'billed') {
            $('#billed_group').show();
            $('#due_date_group').show();
        } else {
            $('#billed_group').hide();
            $('#due_date_group').hide();
        }
    });

    // Auto-fill medicine from prescription
    $('#prescription_id').on('change', function() {
        var medicineId = $(this).find(':selected').data('medicine');
        if (medicineId) {
            $('#medicine_id').val(medicineId).trigger('change');
        }
    });

    // Fetch price and stock on medicine select
    $('#medicine_id').on('change', function() {
        var medicineId = $(this).val();
        if (medicineId) {
            $.ajax({
                url: '{{ url("/inventory/stock") }}/' + medicineId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    unitPrice = parseFloat(data.price) || 0;
                    $('#unit_price').val('Ksh ' + unitPrice.toFixed(2));
                    // Add back original qty to show true available stock
                    $('#available_stock').val(parseInt(data.current_stock) + parseInt(originalQty));
                    recalculate();
                },
                error: function() {
                    unitPrice = 0;
                    $('#unit_price').val('N/A');
                    $('#available_stock').val('N/A');
                    $('#total_price').val('');
                }
            });
        }
    });

    $('#quantity_sold').on('input', function() {
        recalculate();
    });

    function recalculate() {
        var qty = parseInt($('#quantity_sold').val()) || 0;
        var total = unitPrice * qty;
        $('#total_price').val(total > 0 ? 'Ksh ' + total.toFixed(2) : '');
    }

    // Load current medicine data on page load
    $(document).ready(function() {
        if ($('#medicine_id').val()) {
            $('#medicine_id').trigger('change');
        }
    });
</script>

@include('pharmacist.footer')