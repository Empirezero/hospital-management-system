@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Lab Requests</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.lab.index') }}">Laboratory</a></div>
                <div class="breadcrumb-item">Requests</div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>All Lab Requests</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Test</th>
                                    <th>Doctor</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $req->patient_name }}</td>
                                    <td>
                                        {{ $req->labTest?->name ?? 'N/A' }}
                                        <small class="text-muted d-block">{{ $req->labTest?->code }}</small>
                                    </td>
                                    <td>{{ $req->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $req->requested_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $req->status == 'requested'   ? 'badge-warning' : '' }}
                                            {{ $req->status == 'in_progress' ? 'badge-info'    : '' }}
                                            {{ $req->status == 'completed'   ? 'badge-success' : '' }}
                                            {{ $req->status == 'cancelled'   ? 'badge-danger'  : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    </td>
                                    <td>Ksh {{ number_format($req->labTest?->price ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No requests found.</td>
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