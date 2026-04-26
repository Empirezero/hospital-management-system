@include('lab.header')
@include('lab.menusidebar')

<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Lab Dashboard</h1>
    </div>
    <div class="section-body">

      @if(session('message'))
      <div class="alert alert-success alert-dismissible fade show">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      @endif

      {{-- Stats --}}
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-flask"></i></div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Total Requests</h4>
              </div>
              <div class="card-body">{{ $totalTests }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Pending</h4>
              </div>
              <div class="card-body">{{ $pendingRequests }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Completed Today</h4>
              </div>
              <div class="card-body">{{ $completedToday }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-statistic-1">
            <div class="card-icon bg-info"><i class="fas fa-check-double"></i></div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Total Completed</h4>
              </div>
              <div class="card-body">{{ $totalCompleted }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Lab Tests Catalog --}}
      <div class="card">
        <div class="card-header">
          <h4>Lab Test Catalog</h4>
          <div class="card-header-action">
            <a href="{{ route('lab.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus"></i> Add Test
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Test Name</th>
                  <th>Code</th>
                  <th>Price (Ksh)</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($labTests as $test)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $test->name }}</td>
                  <td><span class="badge badge-primary">{{ $test->code }}</span></td>
                  <td>Ksh {{ number_format($test->price, 2) }}</td>
                  <td>{{ Str::limit($test->description, 40) ?? '—' }}</td>
                  <td>
                    <span class="badge {{ $test->is_active ? 'badge-success' : 'badge-secondary' }}">
                      {{ $test->is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('lab.delete', $test->id) }}"
                      class="btn btn-danger btn-sm"
                      onclick="return confirm('Delete this test?')">
                      <i class="fas fa-trash"></i>
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    No lab tests yet. <a href="{{ route('lab.create') }}">Add one</a>.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

@include('lab.footer')