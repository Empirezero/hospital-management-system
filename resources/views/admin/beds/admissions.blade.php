@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admissions</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Admissions</div>
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
                    <h4>All Admissions</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.beds.admit') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New Admission
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
                                    <th>Ward</th>
                                    <th>Bed</th>
                                    <th>Doctor</th>
                                    <th>Admitted</th>
                                    <th>Discharged</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admissions as $admission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $admission->patient_name }}</strong>
                                        @if($admission->patient_phone)
                                        <small class="text-muted d-block">{{ $admission->patient_phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $admission->ward?->name }}</td>
                                    <td>{{ $admission->bed?->bed_number }}</td>
                                    <td>Dr. {{ $admission->doctor?->name ?? 'N/A' }}</td>
                                    <td>{{ $admission->admitted_at->format('d M Y H:i') }}</td>
                                    <td>{{ $admission->discharged_at?->format('d M Y H:i') ?? '—' }}</td>
                                    <td>
                                        @if($admission->discharged_at)
                                        {{ $admission->admitted_at->diffForHumans($admission->discharged_at, true) }}
                                        @elseif($admission->status == 'admitted')
                                        <span class="text-info">
                                            {{ $admission->admitted_at->diffForHumans() }}
                                        </span>
                                        @else
                                        —
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            {{ $admission->status == 'admitted'    ? 'badge-success'  : '' }}
                                            {{ $admission->status == 'discharged'  ? 'badge-secondary': '' }}
                                            {{ $admission->status == 'transferred' ? 'badge-info'     : '' }}">
                                            {{ ucfirst($admission->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.beds.admission_detail', $admission->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($admission->status == 'admitted')
                                        <a href="{{ route('admin.beds.discharge', $admission->id) }}"
                                            class="btn btn-warning btn-sm"
                                            onclick="return confirm('Discharge this patient?')">
                                            <i class="fas fa-sign-out-alt"></i> Discharge
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        No admissions yet.
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

@include('admin.footer')