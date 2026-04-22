@php
$role = auth()->user()->role;
$header = match($role) { 'admin' => 'admin.header', 'doctor' => 'doctor.header', 'patient' => 'patient.header', 'pharmacist' => 'pharmacist.header', 'lab_technician' => 'lab.header', default => 'admin.header' };
$sidebar = match($role) { 'admin' => 'admin.menusidebar', 'doctor' => 'doctor.sidebar', 'patient' => 'patient.sidebar', 'pharmacist' => 'pharmacist.sidebar', 'lab_technician' => 'lab.menusidebar', default => 'admin.menusidebar' };
$footer = match($role) { 'admin' => 'admin.footer', 'doctor' => 'doctor.footer', 'patient' => 'patient.footer', 'pharmacist' => 'pharmacist.footer', 'lab_technician' => 'lab.footer', default => 'admin.footer' };
@endphp

@include($header)
@include($sidebar)

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Notifications</h1>
            <div class="section-header-breadcrumb">
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="btn btn-sm btn-secondary">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </button>
                </form>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                    <div class="d-flex align-items-start p-3 border-bottom
                        {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                        <div class="mr-3 mt-1">
                            @php
                            $icon = match($notification->data['type'] ?? '') {
                            'lab_result' => 'fas fa-flask text-success',
                            'appointment' => 'fas fa-calendar text-primary',
                            'prescription' => 'fas fa-pills text-warning',
                            'claim' => 'fas fa-file-invoice text-info',
                            default => 'fas fa-bell text-secondary',
                            };
                            @endphp
                            <i class="{{ $icon }} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $notification->data['title'] }}</strong>
                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <p class="mb-1 text-muted">{{ $notification->data['message'] }}</p>
                            @if(!is_null($notification->data['url'] ?? null))
                            <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">
                                
                                View
                            </a>
                            @endif
                        </div>
                        @if(is_null($notification->read_at))
                        <span class="badge badge-primary ml-2 mt-1">New</span>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bell fa-3x mb-3"></i>
                        <p>No notifications yet.</p>
                    </div>
                    @endforelse
                </div>
                @if($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

{{-- Mark as read via AJAX --}}
<script>
    function markRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
    }
</script>

@include($footer)