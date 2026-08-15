@include('nurse.header')
@include('nurse.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Current Admissions</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('nurse.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Admissions</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4>Admitted Patients</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Ward</th>
                                    <th>Bed</th>
                                    <th>Doctor</th>
                                    <th>Reason</th>
                                    <th>Admitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admissions as $admission)
                                <tr>
                                    <td>
                                        <strong>{{ $admission->patient_name }}</strong>
                                        @if($admission->patient_phone)
                                        <br><small class="text-muted">{{ $admission->patient_phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $admission->ward?->name ?? '—' }}
                                        @if($admission->ward)
                                        <br>
                                        <span class="badge badge-{{
                                            $admission->ward->type == 'icu'       ? 'danger'  :
                                            ($admission->ward->type == 'emergency' ? 'warning' :
                                            ($admission->ward->type == 'private'   ? 'info'    : 'primary'))
                                        }}">{{ ucfirst($admission->ward->type) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $admission->bed?->bed_number ?? '—' }}</td>
                                    <td>Dr. {{ $admission->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $admission->reason ?? '—' }}</td>
                                    <td>{{ $admission->admitted_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.beds.admission_detail', $admission->id) }}" class="btn btn-sm btn-info">View</a>
                                        <form action="{{ route('admin.beds.discharge', $admission->id) }}" method="GET" style="display:inline;"
                                              onsubmit="return confirm('Discharge {{ $admission->patient_name }}? This will free bed {{ $admission->bed?->bed_number }}.');">
                                            <a href="{{ route('admin.beds.discharge', $admission->id) }}" class="btn btn-sm btn-warning"
                                               onclick="return confirm('Discharge {{ $admission->patient_name }}? This will free bed {{ $admission->bed?->bed_number }}.');">
                                                Discharge
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No patients currently admitted.</td>
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

@include('nurse.footer')