@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit User</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.view_users') }}">Users</a></div>
                <div class="breadcrumb-item">Edit User</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Edit — {{ $user->name }}</h4>
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

                    <form action="{{ route('admin.update_user', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Profile Image --}}
                        <div class="form-group">
                            <label>Profile Image</label>
                            <div class="mb-2">
                                @if($user->image)
                                <img src="{{ asset('userimage/' . $user->image) }}"
                                    alt="{{ $user->name }}"
                                    style="height:80px; width:80px; border-radius:50%; object-fit:cover;">
                                @else
                                <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                                    alt="Default Avatar"
                                    style="height:80px; width:80px; border-radius:50%; object-fit:cover;">
                                @endif
                            </div>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                            <small class="text-muted">Optional. Max 2MB. Leave blank to keep current image.</small>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
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
                                <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('admin.view_users') }}" class="btn btn-secondary">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')