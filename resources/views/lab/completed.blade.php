@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Completed Tests</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('lab.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Completed</div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Completed Lab Tests</h4>
                    <div class="card-header-action">
                        <a href="{{ route('lab.queue') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-clock"></i> View Queue
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
                                    <th>Completed</th>
                                    <th>Released to Patient</th>
                                    <th>Result</th>
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
                                    <td>Dr. {{ $req->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $req->completed_at?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        @if($req->released_to_patient)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Released
                                        </span>
                                        @else
                                        <span class="badge badge-warning">Pending Doctor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('lab.result', $req->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No completed tests yet.
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