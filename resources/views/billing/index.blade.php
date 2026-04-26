@include('admin.header')
@include('admin.menusidebar')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Billing</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Billing</div>
            </div>
        </div>

        <div class="section-body">

            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Bills</h4>
                            </div>
                            <div class="card-body">{{ $bills->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Open / Partial</h4>
                            </div>
                            <div class="card-body">
                                {{ \App\Models\Bill::whereIn('status', ['open','partial'])->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Paid Today</h4>
                            </div>
                            <div class="card-body">
                                {{ \App\Models\Bill::where('status','paid')->whereDate('paid_at', today())->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Overdue</h4>
                            </div>
                            <div class="card-body">
                                {{ \App\Models\Bill::whereIn('status',['open','partial'])->where('due_date','<',today())->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>All Bills</h4>
                    <div class="card-header-action">
                        <a href="{{ route('billing.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Bill
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Quick filters --}}
                    <div class="mb-3">
                        <a href="{{ route('billing.index') }}"
                            class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}">
                            All
                        </a>
                        <a href="{{ route('billing.index', ['status' => 'open']) }}"
                            class="btn btn-sm {{ request('status') == 'open' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Open
                        </a>
                        <a href="{{ route('billing.index', ['status' => 'partial']) }}"
                            class="btn btn-sm {{ request('status') == 'partial' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Partial
                        </a>
                        <a href="{{ route('billing.index', ['status' => 'paid']) }}"
                            class="btn btn-sm {{ request('status') == 'paid' ? 'btn-success' : 'btn-outline-success' }}">
                            Paid
                        </a>
                        <a href="{{ route('billing.index', ['status' => 'draft']) }}"
                            class="btn btn-sm {{ request('status') == 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            Draft
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Bill No.</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th class="text-right">Total (KES)</th>
                                    <th class="text-right">Paid (KES)</th>
                                    <th class="text-right">Balance (KES)</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                <tr>
                                    <td>
                                        <a href="{{ route('billing.show', $bill) }}">
                                            <strong>{{ $bill->bill_number }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $bill->patient->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $bill->bill_type->label() }}
                                        </span>
                                    </td>
                                    <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="text-right text-success">{{ number_format($bill->amount_paid, 2) }}</td>
                                    <td class="text-right {{ $bill->balance_due > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                        {{ number_format($bill->balance_due, 2) }}
                                    </td>
                                    <td>
                                        @php
                                        $badge = match($bill->status->value) {
                                        'draft' => 'badge-secondary',
                                        'open' => 'badge-primary',
                                        'partial' => 'badge-warning',
                                        'paid' => 'badge-success',
                                        'void' => 'badge-danger',
                                        'written_off' => 'badge-dark',
                                        default => 'badge-secondary',
                                        };
                                        @endphp
                                        <span class="badge {{ $badge }}">
                                            {{ $bill->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $bill->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('billing.show', $bill) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No bills found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bills->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.footer')