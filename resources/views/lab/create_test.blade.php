@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Lab Test</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('lab.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Add Lab Test</div>
            </div>
        </div>
        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>New Lab Test</h4>
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('lab.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Test Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="e.g. Complete Blood Count"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Test Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control"
                                    placeholder="e.g. CBC"
                                    value="{{ old('code') }}" required>
                                <small class="text-muted">Short unique code — will be saved in uppercase</small>
                            </div>
                            <div class="form-group">
                                <label>Price (Ksh) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control"
                                    placeholder="0.00" step="0.01" min="0"
                                    value="{{ old('price', 0) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="What this test checks for...">{{ old('description') }}</textarea>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('lab.home') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Test
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('lab.footer')