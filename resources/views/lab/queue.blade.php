@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Lab Queue</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('lab.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Queue</div>
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
                    <h4>Pending Tests</h4>
                    <div class="card-header-action">
                        <a href="{{ route('lab.completed') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle"></i> View Completed
                        </a>
                    </div>
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
                                    <th>Clinical Notes</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $req->patient_name }}</strong>
                                        @if($req->patient_phone)
                                        <small class="text-muted d-block">{{ $req->patient_phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $req->labTest?->name ?? 'N/A' }}</strong>
                                        <small class="text-muted d-block">{{ $req->labTest?->code }}</small>
                                    </td>
                                    <td>Dr. {{ $req->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($req->notes, 40) ?? '—' }}</td>
                                    <td>{{ $req->requested_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $req->status == 'requested' ? 'badge-warning' : 'badge-info' }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('lab.upload', $req->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-upload"></i> Upload Result
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-check-circle fa-3x d-block mb-3 text-success"></i>
                                        No pending tests. Queue is clear.
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

@include('lab.footer')