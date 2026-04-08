@include('doctor.header')
@include('doctor.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Lab Requests</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('doctor_index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Lab Requests</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>My Lab Requests</h4>
                    <div class="card-header-action">
                        <a href="{{ route('doctor.lab.request') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Request
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
                                    <th>Source</th>
                                    <th>Requested</th>
                                    <th>Status</th>
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
                                    <td>
                                        @if($req->encounter_id)
                                        <span class="badge badge-info">Encounter</span>
                                        @else
                                        <span class="badge badge-primary">Appointment</span>
                                        @endif
                                    </td>
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
                                    <td>
                                        @if($req->result_file)
                                        <a href="{{ route('lab.result', $req->id) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if(!$req->released_to_patient)
                                        <a href="{{ route('doctor.lab.release', $req->id) }}"
                                            class="btn btn-primary btn-sm"
                                            onclick="return confirm('Release this result to the patient?')">
                                            <i class="fas fa-share"></i> Release
                                        </a>
                                        @else
                                        <span class="badge badge-success d-block mt-1">
                                            <i class="fas fa-check"></i> Released
                                            <small>{{ $req->released_at->format('d M Y') }}</small>
                                        </span>
                                        @endif
                                        @else
                                        <span class="text-muted">Awaiting result</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No lab requests yet.
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

@include('doctor.footer')