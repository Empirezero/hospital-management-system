@include('receptionist.header')
@include('receptionist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Patient</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('receptionist.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('receptionist.index') }}">Patients</a></div>
                <div class="breadcrumb-item">Edit — {{ $patient->patient_number }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Edit {{ $patient->user->name }} — {{ $patient->patient_number }}</h4>
                </div>
                <div class="card-body">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('receptionist.update_patient', $patient->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Profile Image</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                            <div class="mt-2">
                                <img src="{{ $patient->user->image ? asset('userimage/' . $patient->user->image) : asset('assets/img/avatar/avatar-1.png') }}"
                                    style="height:80px; width:80px; border-radius:50%; object-fit:cover;">
                            </div>
                            <small class="text-muted">Leave blank to keep current photo.</small>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $patient->user->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $patient->user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('gender', $patient->gender) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Blood Group</label>
                            <select name="blood_group" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $patient->address) }}</textarea>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Emergency Contact</h6>

                        <div class="form-group">
                            <label>Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                        </div>

                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Medical Notes</h6>

                        <div class="form-group">
                            <label>Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $patient->allergies) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Chronic Conditions</label>
                            <textarea name="chronic_conditions" class="form-control" rows="2">{{ old('chronic_conditions', $patient->chronic_conditions) }}</textarea>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Reset Password <small>(optional)</small></h6>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('receptionist.show_patient', $patient->id) }}" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

@include('receptionist.footer')