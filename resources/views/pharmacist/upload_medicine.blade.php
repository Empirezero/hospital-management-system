@include('pharmacist.header')
<!-- Sidebar -->
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Medicine</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Forms</a></div>
                <div class="breadcrumb-item">Add Medicine</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Add New Medicine</h2>
            <p class="section-lead">Fill in the details below to add a new medicine.</p>

            <!-- Adjusted form container -->
            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Medicine Details</h4>
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

                        <form method="POST" action="{{ url('upload_medicine') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Medicine Name</label>
                                <input type="text" placeholder="Enter medicine name" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Price (in Ksh)</label>
                                <input type="text" placeholder="Enter price" class="form-control" name="price" required>
                            </div>
                            <div class="form-group" style="display: none;">
                                <label>Stock Quantity</label>
                                <input type="number" placeholder="Enter stock quantity" class="form-control" name="quantity" required>
                            </div>
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" required>
                            </div>
                            <div class="form-group">
                                <label>Medicine Description</label>
                                <textarea placeholder="Enter description (optional)" class="form-control" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Medicine Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>

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

@include('pharmacist.footer')