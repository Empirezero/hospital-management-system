@include('receptionist.header')
@include('receptionist.sidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Register Patient</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('receptionist.home') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('receptionist.index') }}">Patients</a></div>
                <div class="breadcrumb-item">Register</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>New Patient</h4>
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

                    <form action="{{ route('receptionist.store_patient') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Email <small class="text-muted">(optional — leave blank for walk-ins)</small></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
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

                        <hr>
                        <h6 class="text-muted mb-3">Emergency Contact</h6>

                        <div class="form-group">
                            <label>Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                        </div>

                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Medical Notes</h6>

                        <div class="form-group">
                            <label>Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2">{{ old('allergies') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Chronic Conditions</label>
                            <textarea name="chronic_conditions" class="form-control" rows="2">{{ old('chronic_conditions') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Register Patient</button>
                        <a href="{{ route('receptionist.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

@include('receptionist.footer')