@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>My Profile</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('index') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Profile</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="row">

                {{-- Left: Profile Card --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($user->image)
                            <img src="{{ asset('userimage/' . $user->image) }}"
                                alt="{{ $user->name }}"
                                class="rounded-circle mb-3"
                                style="height:120px; width:120px; object-fit:cover; border: 4px solid #f0f0f0;">
                            @else
                            <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                                alt="Default Avatar"
                                class="rounded-circle mb-3"
                                style="height:120px; width:120px; object-fit:cover; border: 4px solid #f0f0f0;">
                            @endif
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            <span class="badge badge-primary">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                            <hr>
                            <div class="text-left">
                                <p class="mb-1"><i class="fas fa-calendar-alt mr-2 text-muted"></i>
                                    <small>Joined {{ $user->created_at->format('d M Y') }}</small>
                                </p>
                                <p class="mb-0"><i class="fas fa-clock mr-2 text-muted"></i>
                                    <small>Last updated {{ $user->updated_at->diffForHumans() }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Edit Form --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Profile</h4>
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

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Profile Image --}}
                                <div class="form-group">
                                    <label>Profile Image</label>
                                    <input type="file" name="image" class="form-control-file" accept="image/*"
                                        onchange="previewImage(event)">
                                    <small class="text-muted">Optional. Max 2MB. Leave blank to keep current.</small>
                                    <div class="mt-2">
                                        <img id="image-preview"
                                            src="{{ $user->image ? asset('userimage/' . $user->image) : asset('assets/img/avatar/avatar-1.png') }}"
                                            alt="Preview"
                                            style="height:80px; width:80px; border-radius:50%; object-fit:cover; display:block;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" class="form-control"
                                        value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled>
                                </div>

                                <hr>
                                <h6 class="text-muted mb-3">Change Password</h6>

                                <div class="form-group">
                                    <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                    <input type="password" name="password" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                                <a href="{{ url('index') }}" class="btn btn-secondary">Cancel</a>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('image-preview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

@include('admin.footer')