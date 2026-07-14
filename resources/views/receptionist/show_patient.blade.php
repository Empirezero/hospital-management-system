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
                    <a href="{{ route('receptionist.index') }}" class="btn btn-secondary mt-3">Back to Patients</a>
                </div>
            </div>
        </div>
    </section>
</div>

@include('receptionist.footer')