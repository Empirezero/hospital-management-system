@include('pharmacist.header')
<!-- Sidebar -->
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 class="h2">Add Sale</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Sales</a></div>
                <div class="breadcrumb-item">Add Sale</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Record a New Sale</h2>
            <p class="section-lead">Fill in the details below to add a new sale record.</p>

            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Sale Details</h4>
                    </div>
                    <div class="card-body">
                        @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session()->get('message') }}
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <script>
                            setTimeout(function() {
                                document.querySelector('.alert').style.display = 'none';
                            }, 5000); // Hide after 5 seconds
                        </script>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ url('add_sale') }}">
                            @csrf

                            <!-- Medicine Selection -->
                            <div class="form-group">
                                <label for="medicine_id">Medicine Name <span class="text-danger">*</span></label>
                                <select class="form-control" name="medicine_id" id="medicine_id" required>
                                    <option value="">Select a medicine</option>
                                    @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Please select the medicine being sold.</small>
                            </div>

                            <!-- Quantity Sold -->
                            <div class="form-group">
                                <label for="quantity_sold">Quantity Sold <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter quantity sold" class="form-control" name="quantity_sold" id="quantity_sold" required min="1">
                                <small class="form-text text-muted">Enter the quantity of medicine sold.</small>
                            </div>

                            <!-- Total Price -->
                            <div class="form-group">
                                <label for="total_price">Total Price</label>
                                <input type="text" id="total_price" class="form-control" readonly>
                                <small class="form-text text-muted">The total price will be calculated based on the quantity sold.</small>
                            </div>

                            <!-- Admin ID -->
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <!-- Submit Button -->
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Submit Sale</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--<script>
    $(document).ready(function() {
        // Fetch price when medicine is selected
        $('#medicine_id').on('change', function() {
            var medicineId = $(this).val();
            if (medicineId) {
                $.ajax({
                    url: '{{ url("/inventory/stock") }}/' + medicineId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#total_price').val(data.price); // Set price based on response
                    },
                    error: function() {
                        $('#total_price').val('Error fetching price');
                    }
                });
            } else {
                $('#total_price').val('');
            }
        });

        // Update total price based on quantity sold
        $('#quantity_sold').on('input', function() {
            var price = parseFloat($('#total_price').val());
            var quantity = parseInt($(this).val());
            if (!isNaN(price) && !isNaN(quantity)) {
                $('#total_price').val((price * quantity).toFixed(2)); // Calculate total price
            }
        });
    });-->
</script>

@include('pharmacist.footer')