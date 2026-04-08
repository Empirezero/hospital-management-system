@include('patient.header')
@include('patient.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Lab Results</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Lab Results</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Released Results</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Test</th>
                                    <th>Doctor</th>
                                    <th>Requested</th>
                                    <th>Released On</th>
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
                                    <td>Dr. {{ $req->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $req->requested_at->format('d M Y') }}</td>
                                    <td>{{ $req->released_at?->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('lab.result', $req->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-file-alt"></i> View Result
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
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