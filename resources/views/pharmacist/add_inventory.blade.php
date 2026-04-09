@include('pharmacist.header')
<!-- Sidebar -->
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 class="h2">Add Inventory</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Forms</a></div>
                <div class="breadcrumb-item">Add Inventory</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Add New Inventory Stock</h2>
            <p class="section-lead">Fill in the details below to add new stock to the inventory.</p>

            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Inventory Details</h4>
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

                        <form method="POST" action="{{ url('add_inventory') }}">
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
                                <small class="form-text text-muted">Please select the medicine to add to inventory.</small>
                            </div>
                            <div class="form-group">
                                <label>Price (in Ksh)</label>
                                <input type="text" placeholder="Enter price" class="form-control" name="price" required>
                            </div>
                            <!-- Current Stock (Dynamically Updated) -->
                            <div class="form-group">
                                <label for="current_stock">Current Stock</label>
                                <input type="text" id="current_stock" class="form-control" readonly>
                                <small class="form-text text-muted">The current stock will be displayed here once a medicine is selected.</small>
                            </div>

                            <!-- Stock Added -->
                            <div class="form-group">
                                <label for="stock_added">Stock Added <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter stock quantity" class="form-control" name="stock_added" id="stock_added" required min="1">
                                <small class="form-text text-muted">Enter the quantity of stock being added.</small>
                            </div>

                            <!-- Stock Date -->
                            <div class="form-group">
                                <label for="stock_date">Stock Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="stock_date" id="stock_date" required>
                                <small class="form-text text-muted">Select the date of stock addition.</small>
                            </div>

                            <!-- Expiry Date -->
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date (optional)</label>
                                <input type="date" class="form-control" name="expiry_date" id="expiry_date" placeholder="Enter expiry date if applicable">
                                <small class="form-text text-muted">Specify the expiry date if applicable.</small>
                            </div>

                            <!-- Admin ID -->
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <!-- Submit Button -->
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
    $(document).ready(function() {
        $('#medicine_id').on('change', function() {
            var medicineId = $(this).val();
            if (medicineId) {
                $.ajax({
                    url: '{{ url("/inventory/stock") }}/' + medicineId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#current_stock').val(data.current_stock);
                    },
                    error: function() {
                        $('#current_stock').val('Error fetching stock');
                    }
                });
            } else {
                $('#current_stock').val('0');
            }
        });
    });
</script>

@include('pharmacist.footer')