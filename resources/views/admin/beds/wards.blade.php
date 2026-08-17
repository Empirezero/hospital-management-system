@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Wards</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Wards</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>All Wards</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.beds.create_ward') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Ward
                        </a>
                        <a href="{{ route('beds.overview') }}" class="btn btn-info btn-sm ml-1">
                            <i class="fas fa-th"></i> Bed Overview
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ward Name</th>
                                    <th>Type</th>
                                    <th>Total Beds</th>
                                    <th>Available</th>
                                    <th>Occupied</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wards as $ward)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ward->name }}</td>
                                    <td>
                                        <span class="badge badge-{{
                                            $ward->type == 'icu'        ? 'danger'  :
                                            ($ward->type == 'emergency' ? 'warning' :
                                            ($ward->type == 'private'   ? 'info'    : 'primary'))
                                        }}">
                                            {{ ucfirst($ward->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $ward->beds_count }}</td>
                                    <td><span class="text-success">{{ $ward->available_beds_count }}</span></td>
                                    <td><span class="text-danger">{{ $ward->occupied_beds_count }}</span></td>
                                    <td>
                                        <span class="badge {{ $ward->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $ward->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('beds.by_ward', $ward->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-bed"></i> Beds
                                        </a>
                                        <a href="{{ route('admin.beds.delete_ward', $ward->id) }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this ward and all its beds?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No wards created yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')