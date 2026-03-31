@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Inventory</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Update Inventory Information</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('update_inventory', $inventory->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="medicine_id">Medicine Name</label>
                                    <select class="form-control" name="medicine_id" id="medicine_id" required>
                                        @foreach($medicines as $medicine)
                                        <option value="{{ $medicine->id }}" {{ $inventory->medicine_id == $medicine->id ? 'selected' : '' }}>
                                            {{ $medicine->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="stock_added">Stock Added</label>
                                    <input type="number" name="stock_added" value="{{ $inventory->stock_added }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="number" name="price" value="{{ $inventory->price }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="current_stock">Current Stock</label>
                                    <input type="number" name="current_stock" value="{{ $inventory->current_stock }}" class="form-control" readonly required>
                                </div>

                                <div class="form-group">
                                    <label for="stock_date">Stock Date</label>
                                    <input type="date" name="stock_date" value="{{ $inventory->stock_date }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date (optional)</label>
                                    <input type="date" name="expiry_date" value="{{ $inventory->expiry_date }}" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Update Inventory</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pharmacist.footer')