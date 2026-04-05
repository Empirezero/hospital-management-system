@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Medicine</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Medicine</a></div>
                <div class="breadcrumb-item">Add Medicine</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Add New Medicine</h2>
            <p class="section-lead">Fill in the details below to add a new medicine.</p>

            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Medicine Details</h4>
                    </div>
                    <div class="card-body">

                        @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <script>
                            setTimeout(function() {
                                var alert = document.querySelector('.alert');
                                if (alert) alert.style.display = 'none';
                            }, 5000);
                        </script>
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

                        <form method="POST" action="{{ url('upload_medicine') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Medicine Name</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Enter medicine name"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Price (Ksh)</label>
                                <input type="number" name="price" class="form-control"
                                    placeholder="Enter price"
                                    value="{{ old('price') }}"
                                    step="0.01" min="0" required>
                            </div>

                            <div class="form-group">
                                <label>Initial Stock Quantity</label>
                                <input type="number" name="quantity" class="form-control"
                                    placeholder="Enter stock quantity"
                                    value="{{ old('quantity', 0) }}"
                                    min="0" required>
                            </div>

                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control"
                                    value="{{ old('expiry_date') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Medicine Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control"
                                    rows="3"
                                    placeholder="Enter description">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Medicine Image <small class="text-muted">(optional)</small></label>
                                <input type="file" name="image" class="form-control-file" accept="image/*">
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('pharmacist.home') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Add Medicine
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pharmacist.footer')