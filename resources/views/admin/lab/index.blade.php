@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Laboratory</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Laboratory</div>
            </div>
        </div>
        <div class="section-body">

            {{-- Stats --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-flask"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Requests</h4>
                            </div>
                            <div class="card-body">{{ $totalTests }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">{{ $pending }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-check"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Completed</h4>
                            </div>
                            <div class="card-body">{{ $completed }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lab Tests Catalog --}}
            <div class="card">
                <div class="card-header">
                    <h4>Lab Test Catalog</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.lab.requests') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-list"></i> View All Requests
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Test Name</th>
                                    <th>Code</th>
                                    <th>Price (Ksh)</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labTests as $test)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->name }}</td>
                                    <td><span class="badge badge-primary">{{ $test->code }}</span></td>
                                    <td>Ksh {{ number_format($test->price, 2) }}</td>
                                    <td>{{ Str::limit($test->description, 50) ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $test->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $test->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No lab tests found.</td>
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