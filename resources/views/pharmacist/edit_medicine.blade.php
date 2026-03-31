@include('pharmacist.header')
@include('pharmacist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Medicine</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Update Medicine Information</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('update_medicine', $medicine->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Medicine Name</label>
                                    <input type="text" name="name" value="{{ $medicine->name }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="number" name="price" value="{{ $medicine->price }}" class="form-control" required>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" value="{{ $medicine->stock }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="date" name="expiry_date" value="{{ $medicine->expiry_date }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" class="form-control">{{ $medicine->description }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="image">Medicine Image</label><br>
                                    <img src="{{ asset('doctorimage/' . $medicine->image) }}" alt="{{ $medicine->name }}" style="width: 100px; height: 100px;"><br><br>
                                    <input type="file" name="image" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-primary">Update Medicine</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pharmacist.footer')