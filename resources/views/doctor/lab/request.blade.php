@php
$role = auth()->user()->role;
$header = match($role) {
'admin' => 'admin.header',
'doctor' => 'doctor.header',
'patient' => 'patient.header',
'lab_technician' => 'lab.header',
default => 'admin.header',
};
$sidebar = match($role) {
'admin' => 'admin.menusidebar',
'doctor' => 'doctor.sidebar',
'patient' => 'patient.sidebar',
'lab_technician' => 'lab.sidebar',
default => 'admin.menusidebar',
};
$footer = match($role) {
'admin' => 'admin.footer',
'doctor' => 'doctor.footer',
'patient' => 'patient.footer',
'lab_technician' => 'lab.footer',
default => 'admin.footer',
};
@endphp

@include($header)
@include($sidebar)

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Request Lab Test</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('doctor_index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Request Lab Test</div>
            </div>
        </div>
        <div class="section-body">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>New Lab Test Request</h4>
                    </div>
                    <div class="card-body">

                        {{-- Error Display --}}
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('doctor.lab.store') }}" method="POST">
                            @csrf

                            {{-- Lab Test --}}
                            <div class="form-group">
                                <label>Select Lab Test <span class="text-danger">*</span></label>
                                <select name="lab_test_id" class="form-control" required>
                                    <option value="">-- Select Test --</option>
                                    @foreach($labTests as $test)
                                    <option value="{{ $test->id }}"
                                        {{ old('lab_test_id') == $test->id ? 'selected' : '' }}>
                                        {{ $test->name }} ({{ $test->code }}) — Ksh {{ number_format($test->price, 2) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Source --}}
                            <div class="form-group">
                                <label>Source <span class="text-danger">*</span></label>
                                <select name="source" id="source" class="form-control" required>
                                    <option value="appointment" {{ old('source') == 'appointment' ? 'selected' : '' }}>
                                        Appointment
                                    </option>
                                    <option value="encounter" {{ old('source') == 'encounter' ? 'selected' : '' }}>
                                        Encounter
                                    </option>
                                </select>
                            </div>

                            {{-- Appointment Select --}}
                            <div id="appointment_div" class="form-group">
                                <label>Select Appointment <small class="text-muted">(optional)</small></label>
                                <select name="appointment_id" class="form-control">
                                    <option value="">-- Select Appointment --</option>
                                    @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}"
                                        {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                        {{ $appointment->name }} —
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Encounter Select --}}
                            <div id="encounter_div" class="form-group" style="display:none;">
                                <label>Select Encounter <small class="text-muted">(optional)</small></label>
                                <select name="encounter_id" class="form-control">
                                    <option value="">-- Select Encounter --</option>
                                    @foreach($encounters as $encounter)
                                    <option value="{{ $encounter->id }}"
                                        {{ old('encounter_id') == $encounter->id ? 'selected' : '' }}>
                                        {{ $encounter->appointment?->name ?? 'Unknown Patient' }} —
                                        {{ \Carbon\Carbon::parse($encounter->visited_at)->format('d M Y') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Patient Name --}}
                            <div class="form-group">
                                <label>Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_name" class="form-control"
                                    value="{{ old('patient_name') }}" required>
                            </div>

                            {{-- Patient Email --}}
                            <div class="form-group">
                                <label>Patient Email <small class="text-muted">(optional)</small></label>
                                <input type="email" name="patient_email" class="form-control"
                                    value="{{ old('patient_email') }}">
                            </div>

                            {{-- Patient Phone --}}
                            <div class="form-group">
                                <label>Patient Phone <small class="text-muted">(optional)</small></label>
                                <input type="text" name="patient_phone" class="form-control"
                                    value="{{ old('patient_phone') }}">
                            </div>

                            {{-- Clinical Notes --}}
                            <div class="form-group">
                                <label>Clinical Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" class="form-control"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('doctor.lab.requests') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-flask"></i> Submit Request
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function toggleSource() {
        var source = document.getElementById('source').value;
        document.getElementById('appointment_div').style.display =
            source === 'appointment' ? 'block' : 'none';
        document.getElementById('encounter_div').style.display =
            source === 'encounter' ? 'block' : 'none';
    }

    document.getElementById('source').addEventListener('change', toggleSource);

    // Restore on validation error
    window.addEventListener('load', function() {
        toggleSource();
    });
</script>

@include($footer)