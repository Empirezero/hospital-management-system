@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admit Patient</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('beds.overview') }}">Beds</a></div>
                <div class="breadcrumb-item">Admit Patient</div>
            </div>
        </div>
        <div class="section-body">

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Patient Admission Form</h4>
                        @if($selectedBed)
                        <small class="text-muted">
                            Bed: <strong>{{ $selectedBed->bed_number }}</strong> —
                            {{ $selectedBed->ward?->name }}
                        </small>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.beds.store_admission') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Ward <span class="text-danger">*</span></label>
                                <select name="ward_id" id="ward_select" class="form-control" required>
                                    <option value="">-- Select Ward --</option>
                                    @foreach($wards as $ward)
                                    <option value="{{ $ward->id }}"
                                        {{ ($selectedBed?->ward_id == $ward->id || old('ward_id') == $ward->id) ? 'selected' : '' }}>
                                        {{ $ward->name }} ({{ ucfirst($ward->type) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Bed <span class="text-danger">*</span></label>
                                <select name="bed_id" id="bed_select" class="form-control" required>
                                    <option value="">-- Select Ward First --</option>
                                    @foreach($wards as $ward)
                                    @foreach($ward->beds->where('status', 'available') as $bed)
                                    <option value="{{ $bed->id }}"
                                        data-ward="{{ $ward->id }}"
                                        {{ ($selectedBed?->id == $bed->id || old('bed_id') == $bed->id) ? 'selected' : '' }}>
                                        {{ $bed->bed_number }}
                                    </option>
                                    @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Doctor <span class="text-danger">*</span></label>
                                <select name="doctor_id" class="form-control" required>
                                    <option value="">-- Select Doctor --</option>
                                    @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->name }} — {{ $doctor->speciality }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Link to Appointment <small class="text-muted">(optional)</small></label>
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

                            <div class="form-group">
                                <label>Patient Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_name" class="form-control"
                                    value="{{ old('patient_name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Patient Email <small class="text-muted">(optional)</small></label>
                                <input type="email" name="patient_email" class="form-control"
                                    value="{{ old('patient_email') }}">
                            </div>

                            <div class="form-group">
                                <label>Patient Phone <small class="text-muted">(optional)</small></label>
                                <input type="text" name="patient_phone" class="form-control"
                                    value="{{ old('patient_phone') }}">
                            </div>

                            <div class="form-group">
                                <label>Reason for Admission</label>
                                <textarea name="reason" class="form-control" rows="3"
                                    placeholder="Primary diagnosis or reason...">{{ old('reason') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Additional Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('beds.overview') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> Admit Patient
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
    // Filter beds based on selected ward
    document.getElementById('ward_select').addEventListener('change', function() {
        var wardId = this.value;
        var bedSelect = document.getElementById('bed_select');
        var options = bedSelect.querySelectorAll('option');

        options.forEach(function(option) {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                option.style.display = option.dataset.ward == wardId ? 'block' : 'none';
            }
        });

        bedSelect.value = '';
    });

    // Trigger on load if ward is pre-selected
    window.addEventListener('load', function() {
        var wardId = document.getElementById('ward_select').value;
        if (wardId) {
            document.getElementById('ward_select').dispatchEvent(new Event('change'));
        }
    });
</script>

@include('admin.footer')