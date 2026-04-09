@include('pharmacist.header')
<!-- Sidebar -->
@include('pharmacist.sidebar')


@if($outOfStock->count())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>{{ $outOfStock->count() }} medicine(s) are out of stock:</strong>
    {{ $outOfStock->pluck('name')->join(', ') }}
</div>
@endif

@if($lowStock->count())
<div class="alert alert-warning">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <strong>{{ $lowStock->count() }} medicine(s) are running low:</strong>
    {{ $lowStock->pluck('name')->join(', ') }}
</div>
@endif
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Inventories</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Inventories</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            <h2 class="section-title">Inventory List</h2>
            <p class="section-lead">Manage your inventories below.</p>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Inventory Table</h4>
                            <div class="card-header-form">
                                <form>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Medicine Name</th>
                                            <th>Stock Added</th>
                                            <th>Current Stock</th>
                                            <th>Price</th>
                                            <th>Stock Date</th>
                                            <th>Expiry Date</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventories as $inventory)
                                        <tr>
                                            <td>{{ $inventory->medicine->name }}</td>
                                            <td>{{ $inventory->stock_added }}</td>
                                            <td>{{ $inventory->current_stock }}</td>
                                            <td>{{ $inventory->price }}</td>
                                            <td>{{ $inventory->stock_date }}</td>
                                            <td>{{ $inventory->expiry_date }}</td>
                                            <td>{{ $inventory->medicine->description }}</td>
                                            <td>
                                                <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i> Update
                                                </a>
                                                <a href="{{ route('pharmacist.inventory.destroy', $inventory->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this inventory item?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pharmacist.footer')