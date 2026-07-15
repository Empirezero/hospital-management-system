@include('receptionist.header')
@include('receptionist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Patient Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('receptionist.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('receptionist.index') }}">Patients</a></div>
                <div class="breadcrumb-item">{{ $patient->patient_number }}</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="row">
                {{-- Left: Patient photo + quick facts --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($patient->user?->image)
                            <img src="{{ asset('userimage/' . $patient->user->image) }}"
                                alt="{{ $patient->user->name }}"
                                class="rounded-circle mb-3"
                                style="height:120px; width:120px; object-fit:cover; border: 4px solid #f0f0f0;">
                            @else
                            <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                                alt="Default Avatar"
                                class="rounded-circle mb-3"
                                style="height:120px; width:120px; object-fit:cover; border: 4px solid #f0f0f0;">
                            @endif
                            <h5 class="mb-1">{{ $patient->user->name ?? 'Unknown' }}</h5>
                            <p class="text-muted mb-1">{{ $patient->patient_number }}</p>
                            <span class="badge badge-info">{{ ucfirst($patient->gender ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Full details --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $patient->user->name ?? 'Unknown' }} — {{ $patient->patient_number }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> {{ $patient->user->email ?? '—' }}</p>
                                    <p><strong>Phone:</strong> {{ $patient->phone ?? '—' }}</p>
                                    <p><strong>Gender:</strong> {{ ucfirst($patient->gender ?? '—') }}</p>
                                    <p><strong>Date of Birth:</strong> {{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</p>
                                    <p><strong>Blood Group:</strong> {{ $patient->blood_group ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Address:</strong> {{ $patient->address ?? '—' }}</p>
                                    <p><strong>Emergency Contact:</strong> {{ $patient->emergency_contact_name ?? '—' }} ({{ $patient->emergency_contact_phone ?? '—' }})</p>
                                    <p><strong>Allergies:</strong> {{ $patient->allergies ?? 'None recorded' }}</p>
                                    <p><strong>Chronic Conditions:</strong> {{ $patient->chronic_conditions ?? 'None recorded' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('receptionist.edit_patient', $patient->id) }}" class="btn btn-primary mt-3">Edit Patient</a>
                            <a href="{{ route('receptionist.index') }}" class="btn btn-secondary mt-3">Back to Patients</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('receptionist.footer')