@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admission Detail</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.beds.admissions') }}">Admissions</a></div>
                <div class="breadcrumb-item">Detail</div>
            </div>
        </div>
        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $admission->patient_name }}</h4>
                        <span class="badge badge-{{ $admission->status == 'admitted' ? 'success' : 'secondary' }} float-right mt-1">
                            {{ ucfirst($admission->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-muted" width="35%">Patient Name</td>
                                <td><strong>{{ $admission->patient_name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>{{ $admission->patient_phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $admission->patient_email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ward</td>
                                <td>{{ $admission->ward?->name }} ({{ ucfirst($admission->ward?->type) }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Bed</td>
                                <td>{{ $admission->bed?->bed_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Doctor</td>
                                <td>Dr. {{ $admission->doctor?->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Reason</td>
                                <td>{{ $admission->reason ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Notes</td>
                                <td>{{ $admission->notes ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Admitted At</td>
                                <td>{{ $admission->admitted_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discharged At</td>
                                <td>{{ $admission->discharged_at?->format('d M Y H:i') ?? '—' }}</td>
                            </tr>
                            @if($admission->discharged_at)
                            <tr>
                                <td class="text-muted">Total Stay</td>
                                <td>{{ $admission->admitted_at->diffForHumans($admission->discharged_at, true) }}</td>
                            </tr>
                            @endif
                        </table>

                        <div class="text-right mt-3">
                            <a href="{{ route('admin.beds.admissions') }}" class="btn btn-secondary mr-2">Back</a>
                            @if($admission->status == 'admitted')
                            <a href="{{ route('admin.beds.discharge', $admission->id) }}"
                                class="btn btn-warning"
                                onclick="return confirm('Discharge this patient?')">
                                <i class="fas fa-sign-out-alt"></i> Discharge Patient
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')