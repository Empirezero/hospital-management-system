@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Lab Results</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item active">Lab Results</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-flask"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Results</h4>
                            </div>
                            <div class="card-body">{{ $requests->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>This Month</h4>
                            </div>
                            <div class="card-body">
                                {{ $requests->filter(fn($r) => $r->released_at?->isCurrentMonth())->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Latest Result</h4>
                            </div>
                            <div class="card-body">
                                {{ $requests->first()?->released_at?->format('d M Y') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Released Results</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Test</th>
                                    <th>Doctor</th>
                                    <th>Requested</th>
                                    <th>Released On</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $req->labTest?->name }}</strong>
                                        <small class="text-muted d-block">{{ $req->labTest?->code }}</small>
                                    </td>
                                    <td>
                                        Dr. {{ $req->doctor?->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $req->requested_at->format('d M Y') }}</td>
                                    <td>{{ $req->released_at?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-success">Released</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('lab.view_result', $req->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-file-alt"></i> View Result
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-flask fa-3x text-muted d-block mb-3"></i>
                                        <p class="text-muted">No lab results available yet.</p>
                                        <small class="text-muted">
                                            Results appear here once your doctor releases them.
                                        </small>
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

@include('patient.footer')