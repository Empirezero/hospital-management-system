@include('receptionist.header')
@include('receptionist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Patients</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('receptionist.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Patients</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>All Patients</h4>
                    <div class="card-header-action">
                        <a href="{{ route('receptionist.add_patient') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Register Patient
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="form-inline mb-3">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Search name, patient #, phone..."
                            value="{{ $search }}" style="min-width:260px;">
                        <button type="submit" class="btn btn-secondary">Search</button>
                        @if($search)
                        <a href="{{ route('receptionist.index') }}" class="btn btn-link">Clear</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient #</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>DOB</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                <tr>
                                    <td>{{ $patient->patient_number }}</td>
                                    <td>{{ $patient->user->name ?? '—' }}</td>
                                    <td>{{ $patient->phone ?? '—' }}</td>
                                    <td>{{ ucfirst($patient->gender ?? '—') }}</td>
                                    <td>{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $patient->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('receptionist.show_patient', $patient->id) }}" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No patients found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    </section>
</div>

@include('receptionist.footer')