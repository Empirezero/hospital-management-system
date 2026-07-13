@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add User</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.view_users') }}">Users</a></div>
                <div class="breadcrumb-item">Add User</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>New User</h4>
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

                    <form action="{{ route('admin.store_user') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                @foreach([
                                'admin' => 'Admin',
                                'doctor' => 'Doctor',
                                'patient' => 'Patient',
                                'pharmacist' => 'Pharmacist',
                                'lab_technician' => 'Lab Technician',
                                'receptionist' => 'Receptionist',
                                'nurse' => 'Nurse',
                                'radiologist' => 'Radiologist',
                                'physiotherapist' => 'Physiotherapist',
                                'billing_officer' => 'Billing Officer',
                                'medical_records_officer' => 'Medical Records Officer',
                                ] as $value => $label)
                                <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Profile Image</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                            <small class="text-muted">Optional. Max 2MB.</small>
                        </div>

                        {{-- After the role select field --}}
                        <div id="doctor-fields" style="display:none;">
                            <hr>
                            <h6 class="text-muted mb-3">Doctor Profile</h6>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="number" class="form-control" value="{{ old('number') }}">
                            </div>
                            <div class="form-group">
                                <label>Speciality</label>
                                <input type="text" name="speciality" class="form-control" value="{{ old('speciality') }}">
                            </div>
                            <div class="form-group">
                                <label>Room Number</label>
                                <input type="text" name="room" class="form-control" value="{{ old('room') }}">
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea name="bio" class="form-control" rows="3">{{ old('bio') }}</textarea>
                            </div>
                            
                        </div>
                        {{-- patients-fields div --}}
                        <div id="patient-fields" style="display:none;">
                            <hr>
                            <h6 class="text-muted mb-3">Patient Profile</h6>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select name="blood_group" class="form-control">
                                    <option value="">-- Select --</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                            </div>
                            <div class="form-group">
                                <label>Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                            </div>
                            <div class="form-group">
                                <label>Allergies</label>
                                <textarea name="allergies" class="form-control" rows="2">{{ old('allergies') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Chronic Conditions</label>
                                <textarea name="chronic_conditions" class="form-control" rows="2">{{ old('chronic_conditions') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create User</button>
                        <a href="{{ route('admin.view_users') }}" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.querySelector('select[name="role"]').addEventListener('change', function() {
        document.getElementById('doctor-fields').style.display = this.value === 'doctor' ? 'block' : 'none';
        document.getElementById('patient-fields').style.display = this.value === 'patient' ? 'block' : 'none';
    });

    var oldRole = "{{ old('role') }}";
    if (oldRole === 'doctor') document.getElementById('doctor-fields').style.display = 'block';
    if (oldRole === 'patient') document.getElementById('patient-fields').style.display = 'block';
</script>

@include('admin.footer')