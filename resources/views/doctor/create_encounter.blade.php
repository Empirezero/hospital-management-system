@include('doctor.header')
@include('doctor.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>New Encounter</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('doctor_index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('doctor_appointment') }}">Appointments</a></div>
                <div class="breadcrumb-item">New Encounter</div>
            </div>
        </div>

        <div class="section-body">

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="row">

                {{-- Patient Info Card --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Patient Info</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                                    class="rounded-circle"
                                    style="height:80px; width:80px; object-fit:cover;">
                            </div>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Name</td>
                                    <td><strong>{{ $appointment->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td>{{ $appointment->email }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone</td>
                                    <td>{{ $appointment->number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Scheduled</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($appointment->message)
                                <tr>
                                    <td class="text-muted">Note</td>
                                    <td><em>{{ $appointment->message }}</em></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Encounter Form --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Encounter Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('doctor.encounter.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                                <input type="hidden" name="patient_id" value="{{ $appointment->patient_id ?? '' }}">

                                <div class="form-group">
                                    <label>Visit Type</label>
                                    <select name="visit_type" class="form-control" required>
                                        <option value="outpatient" {{ old('visit_type') == 'outpatient'  ? 'selected' : '' }}>Outpatient</option>
                                        <option value="inpatient" {{ old('visit_type') == 'inpatient'   ? 'selected' : '' }}>Inpatient</option>
                                        <option value="emergency" {{ old('visit_type') == 'emergency'   ? 'selected' : '' }}>Emergency</option>
                                        <option value="follow_up" {{ old('visit_type') == 'follow_up'   ? 'selected' : '' }}>Follow Up</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Chief Complaint <span class="text-danger">*</span></label>
                                    <textarea name="chief_complaint" class="form-control" rows="3"
                                        required placeholder="What is the patient's main complaint?">{{ old('chief_complaint') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Examination Notes</label>
                                    <textarea name="examination_notes" class="form-control" rows="3"
                                        placeholder="Physical examination findings...">{{ old('examination_notes') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Treatment Plan</label>
                                    <textarea name="treatment_plan" class="form-control" rows="3"
                                        placeholder="Planned treatment...">{{ old('treatment_plan') }}</textarea>
                                </div>

                                <div class="form-group text-right">
                                    <a href="{{ url('doctor_appointment') }}" class="btn btn-secondary mr-2">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save & Add Prescriptions
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@include('doctor.footer')