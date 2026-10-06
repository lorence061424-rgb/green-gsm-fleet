@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; background: #CE2029 !important;">FLEET MANAGEMENT</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-clipboard2-check text-danger me-1"></i> ESS Request Queue</span>
        </div>
        <h2 class="page-header-title mt-1">ESS Maintenance Requests — Manager Queue</h2>
        <p class="page-header-subtitle">Review, approve, assign motorshops, and convert ESS employee maintenance requests into PMS records.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ess.maintenance.index') }}" class="btn btn-outline-secondary rounded-3 px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to ESS Portal
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 bg-success bg-opacity-10 text-success fw-medium" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Pending Review</span>
                    <h3 class="fw-bold my-1 text-warning">{{ $pendingCount }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-4 fs-4"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">In Progress</span>
                    <h3 class="fw-bold my-1 text-info">{{ $inProgressCount }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-4 fs-4"><i class="bi bi-gear-wide-connected"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Requests</span>
                    <h3 class="fw-bold my-1">{{ $essRequests->count() }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-4 fs-4"><i class="bi bi-inbox"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card premium-card p-3 mb-4 border-0 shadow-sm bg-light">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="fw-bold text-dark me-2" style="font-size:13px;"><i class="bi bi-funnel-fill text-danger me-1"></i> Filter by Status:</span>
        @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','in_progress'=>'In Progress','completed'=>'Completed','rejected'=>'Rejected'] as $val => $label)
            <a href="{{ route('ess.maintenance.queue', ['filter'=>$val, 'urgency'=>$urgency]) }}"
               class="btn btn-sm {{ $filter === $val || ($val === 'all' && $filter === 'all') ? 'btn-danger text-white' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                {{ $label }}
            </a>
        @endforeach
        <span class="ms-3 fw-bold text-dark me-2" style="font-size:13px;"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Urgency:</span>
        @foreach(['all'=>'All','critical'=>'🔴 Critical','high'=>'🟠 High','medium'=>'🟡 Medium','low'=>'🟢 Low'] as $val => $label)
            <a href="{{ route('ess.maintenance.queue', ['filter'=>$filter, 'urgency'=>$val]) }}"
               class="btn btn-sm {{ $urgency === $val ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Queue Table --}}
<div class="card premium-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable text-danger me-2"></i> ESS Maintenance Request Queue</h5>
        <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background:#CE2029!important;">{{ $essRequests->count() }} Requests</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size:13px;">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Employee</th>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Issue / Description</th>
                    <th>Date</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($essRequests as $req)
                    @php
                        $urgencyColors = ['critical'=>'danger','high'=>'warning','medium'=>'info','low'=>'secondary'];
                        $uc = $urgencyColors[$req->urgency_level] ?? 'secondary';
                        $statusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','in_progress'=>'info','completed'=>'secondary'];
                        $sc = $statusColors[$req->status] ?? 'secondary';
                        $typeLabels = ['repair'=>'Repair','reimbursement'=>'Reimburse','scheduled_maintenance'=>'PMS'];
                    @endphp
                    <tr>
                        <td><span class="fw-bold text-danger">#{{ $req->id }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $req->employee->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ ucfirst($req->employee->role ?? '') }}</div>
                        </td>
                        <td>
                            @if($req->vehicle)
                                <div class="fw-semibold">{{ $req->vehicle->license_plate }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $req->vehicle->make }} {{ $req->vehicle->model }}</div>
                            @else <span class="text-muted">N/A</span> @endif
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill" style="font-size:11px;">{{ $typeLabels[$req->request_type] ?? $req->request_type }}</span></td>
                        <td style="max-width:180px;" title="{{ $req->anomaly_description }}">{{ Str::limit($req->anomaly_description, 55) }}</td>
                        <td class="text-muted">{{ optional($req->anomaly_date)->format('M d, Y') }}</td>
                        <td><span class="badge bg-{{ $uc }} px-2 py-1 rounded-pill text-uppercase fw-bold" style="font-size:10px;">{{ ucfirst($req->urgency_level) }}</span></td>
                        <td><span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }} border border-{{ $sc }} px-2 py-1 rounded-pill fw-bold" style="font-size:10px;">{{ ucwords(str_replace('_',' ',$req->status)) }}</span></td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Approve button (for pending only) --}}
                                @if($req->status === 'pending')
                                    <button class="btn btn-sm btn-success rounded-pill px-2 fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}" style="font-size:11px;">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-2 fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}" style="font-size:11px;">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                @endif
                                {{-- Convert to PMS (approved and not yet converted) --}}
                                @if($req->status === 'approved' && !$req->maintenance_record_id)
                                    <form method="POST" action="{{ route('ess.maintenance.convert-pms', $req->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-2 fw-bold" style="font-size:11px;"
                                            onclick="return confirm('Convert ESS Request #{{ $req->id }} to a PMS maintenance record?')">
                                            <i class="bi bi-arrow-right-circle me-1"></i>→ PMS
                                        </button>
                                    </form>
                                @endif
                                {{-- Mark complete (in_progress) --}}
                                @if(in_array($req->status, ['approved','in_progress']))
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-2 fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#completeModal{{ $req->id }}" style="font-size:11px;">
                                        <i class="bi bi-check-circle me-1"></i>Done
                                    </button>
                                @endif
                                @if($req->maintenance_record_id)
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2" style="font-size:10px;">
                                        <i class="bi bi-link-45deg me-1"></i>PMS #{{ $req->maintenance_record_id }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            No ESS maintenance requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Approve Modals ─────────────────────────────────────── --}}
@foreach($essRequests->where('status','pending') as $req)
<div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle text-success me-2"></i>Approve ESS Request #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ess.maintenance.approve', $req->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <p class="text-muted small mb-3">
                        Employee: <strong>{{ $req->employee->name ?? 'N/A' }}</strong> |
                        Vehicle: <strong>{{ $req->vehicle->license_plate ?? 'N/A' }}</strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Motorshop / Repair Shop <span class="text-danger">*</span></label>
                        <input type="text" name="assigned_shop" class="form-control" placeholder="e.g., Mabuhay Motorshop, Dela Cruz Auto Repair" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Estimated Cost (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="estimated_cost" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Scheduled Repair Date <span class="text-danger">*</span></label>
                            <input type="date" name="scheduled_repair_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold small">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Additional instructions for the shop or employee..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-5 fw-bold"><i class="bi bi-check-lg me-1"></i>Approve & Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ── Reject Modals ──────────────────────────────────────── --}}
@foreach($essRequests->where('status','pending') as $req)
<div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle text-danger me-2"></i>Reject ESS Request #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ess.maintenance.reject', $req->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why this request is being rejected..." required maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-5 fw-bold"><i class="bi bi-x-lg me-1"></i>Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ── Complete Modals ─────────────────────────────────────── --}}
@foreach($essRequests->whereIn('status',['approved','in_progress']) as $req)
<div class="modal fade" id="completeModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle text-success me-2"></i>Mark as Completed — #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ess.maintenance.complete', $req->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Actual Cost (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="actual_cost" class="form-control" step="0.01" min="0"
                                placeholder="0.00" value="{{ $req->estimated_cost ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Completion Date</label>
                            <input type="date" name="completed_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-5 fw-bold"><i class="bi bi-check-circle me-1"></i>Mark Completed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
