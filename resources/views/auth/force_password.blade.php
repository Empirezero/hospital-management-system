<!DOCTYPE html>
<html>

<head>
    <title>Change Your Password</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center" style="min-height:100vh; background:#f4f6f9;">
        <div class="card" style="max-width:420px; width:100%;">
            <div class="card-header text-center">
                <h4>Set a New Password</h4>
                <p class="text-muted mb-0">For your security, please set your own password before continuing.</p>
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

                <form action="{{ route('password.force.update') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Password & Continue</button>
                </form>

            </div>
        </div>
    </div>
</body>

</html>