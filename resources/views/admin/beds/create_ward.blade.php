@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Create Ward</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.beds.wards') }}">Wards</a></div>
                <div class="breadcrumb-item">Create Ward</div>
            </div>
        </div>
        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>New Ward</h4>
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

                        <form action="{{ route('admin.beds.store_ward') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Ward Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="e.g. Ward A" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Ward Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="general" {{ old('type') == 'general'   ? 'selected' : '' }}>General Ward</option>
                                    <option value="icu" {{ old('type') == 'icu'       ? 'selected' : '' }}>ICU</option>
                                    <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    <option value="private" {{ old('type') == 'private'   ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Number of Beds <span class="text-danger">*</span></label>
                                <input type="number" name="total_beds" class="form-control"
                                    min="1" max="100" placeholder="e.g. 20"
                                    value="{{ old('total_beds') }}" required>
                                <small class="text-muted">Beds will be auto-created with sequential numbers.</small>
                            </div>
                            <div class="form-group">
                                <label>Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('admin.beds.wards') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Ward
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')