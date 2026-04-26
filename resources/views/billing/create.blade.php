@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>New Bill</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('billing.index') }}">Billing</a></div>
                <div class="breadcrumb-item">New Bill</div>
            </div>
        </div>

        <div class="section-body">
            <div class="col-12 col-md-8 col-lg-7 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Bill Details</h4>
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        @endif

                        <form action="{{ route('billing.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient_id"
                                    class="form-control @error('patient_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- Select Patient --</option>
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user->name ?? 'Patient #'.$patient->id }}
                                        ({{ $patient->patient_number }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Bill Type <span class="text-danger">*</span></label>
                                <select name="bill_type"
                                    class="form-control @error('bill_type') is-invalid @enderror"
                                    required>
                                    <option value="">-- Select Type --</option>
                                    <option value="outpatient" {{ old('bill_type') == 'outpatient' ? 'selected' : '' }}>Outpatient</option>
                                    <option value="inpatient" {{ old('bill_type') == 'inpatient'  ? 'selected' : '' }}>Inpatient</option>
                                    <option value="emergency" {{ old('bill_type') == 'emergency'  ? 'selected' : '' }}>Emergency</option>
                                </select>
                                @error('bill_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Link to Encounter</label>
                                <select name="encounter_id" class="form-control">
                                    <option value="">-- No Encounter --</option>
                                    @foreach($encounters as $encounter)
                                    <option value="{{ $encounter->id }}"
                                        {{ old('encounter_id') == $encounter->id ? 'selected' : '' }}>
                                        {{ $encounter->patient->user->name ?? 'Patient #'.$encounter->patient_id }}
                                        — {{ $encounter->created_at->format('d M Y') }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Optional</small>
                            </div>

                            <div class="form-group">
                                <label>Due Date</label>
                                <input type="date" name="due_date"
                                    class="form-control @error('due_date') is-invalid @enderror"
                                    value="{{ old('due_date') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Optional notes...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('billing.index') }}" class="btn btn-secondary mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Bill
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')