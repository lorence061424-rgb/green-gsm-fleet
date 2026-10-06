@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px; background: #CE2029 !important;">HIRNA MOBILITY SOLUTIONS INC.</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-person-lines-fill text-danger me-1"></i> Employee Self-Service (ESS) Portal</span>
        </div>
        <h2 class="page-header-title mt-1">ESS — Maintenance & Repair Requests</h2>
        <p class="page-header-subtitle">Submit vehicle repair, reimbursement, or scheduled maintenance requests. Track your request status in real time.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        @if($isManager)
            <a href="{{ route('ess.maintenance.queue') }}" class="btn btn-warning rounded-3 px-4 shadow-sm fw-bold">
                <i class="bi bi-inbox-fill me-1"></i> Fleet Manager Queue
            </a>
        @endif
        <button class="btn btn-premium rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#submitMaintenanceRequestModal">
            <i class="bi bi-plus-circle me-1"></i> Submit New Request
        </button>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 bg-success bg-opacity-10 text-success fw-medium" role="alert">
        <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ESS Integration Badge --}}
<div class="alert alert-dark bg-dark text-white border-0 rounded-4 p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-diagram-3-fill text-warning fs-4 me-2"></i>
            <div>
                <span class="fw-bold d-block text-white small">ESS ↔ FLEET MANAGEMENT INTEGRATION (TEAM 7)</span>
                <span class="text-white-50 fw-medium" style="font-size: 11px;">Your requests are sent directly to the Fleet Manager's dashboard. Approved requests are automatically converted into PMS maintenance records.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><i class="bi bi-tools me-1"></i> PMS Auto-Sync</span>
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-cash-stack me-1"></i> Reimbursement → Payroll</span>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
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
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Pending Review</span>
                    <h3 class="fw-bold my-1 text-warning">{{ $essRequests->where('status','pending')->count() }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-4 fs-4"><i class="bi bi-clock-history"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">In Progress</span>
                    <h3 class="fw-bold my-1 text-info">{{ $essRequests->where('status','in_progress')->count() }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-4 fs-4"><i class="bi bi-gear-wide-connected"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Completed</span>
                    <h3 class="fw-bold my-1 text-success">{{ $essRequests->where('status','completed')->count() }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-4 fs-4"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Requests Table --}}
<div class="card premium-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable text-danger me-2"></i> {{ $isManager ? 'All ESS Maintenance Requests' : 'My Maintenance Requests' }}</h5>
        <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background: #CE2029 !important;">{{ $essRequests->count() }} Records</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size: 13px;">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    @if($isManager) <th>Employee</th> @endif
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Date Reported</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Shop Assigned</th>
                    <th>Est. Cost</th>
                </tr>
            </thead>
            <tbody>
                @forelse($essRequests as $req)
                    @php
                        $urgencyColors = ['critical'=>'danger','high'=>'warning','medium'=>'info','low'=>'secondary'];
                        $urgencyColor = $urgencyColors[$req->urgency_level] ?? 'secondary';
                        $statusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','in_progress'=>'info','completed'=>'secondary'];
                        $statusColor = $statusColors[$req->status] ?? 'secondary';
                        $typeLabels = ['repair'=>'Vehicle Repair','reimbursement'=>'Reimbursement','scheduled_maintenance'=>'Scheduled PMS'];
                    @endphp
                    <tr>
                        <td><span class="fw-bold text-danger">#{{ $req->id }}</span></td>
                        @if($isManager)
                            <td>
                                <div class="fw-semibold" style="font-size:12px;">{{ $req->employee->name ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $req->employee->role ?? '' }}</div>
                            </td>
                        @endif
                        <td>
                            @if($req->vehicle)
                                <div class="fw-semibold">{{ $req->vehicle->license_plate }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $req->vehicle->make }} {{ $req->vehicle->model }}</div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeIcons = ['repair'=>'bi-wrench','reimbursement'=>'bi-receipt','scheduled_maintenance'=>'bi-calendar-check'];
                                $typeIcon = $typeIcons[$req->request_type] ?? 'bi-question';
                            @endphp
                            <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill" style="font-size:11px;">
                                <i class="bi {{ $typeIcon }} me-1"></i>{{ $typeLabels[$req->request_type] ?? $req->request_type }}
                            </span>
                        </td>
                        <td style="max-width:180px;">
                            <span title="{{ $req->anomaly_description }}">{{ Str::limit($req->anomaly_description, 60) }}</span>
                        </td>
                        <td class="text-muted">{{ optional($req->anomaly_date)->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $urgencyColor }} px-2 py-1 rounded-pill text-uppercase fw-bold" style="font-size:10px;">
                                {{ ucfirst($req->urgency_level) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColor }} bg-opacity-15 text-{{ $statusColor }} border border-{{ $statusColor }} px-2 py-1 rounded-pill fw-bold" style="font-size:10px;">
                                {{ ucwords(str_replace('_',' ', $req->status)) }}
                            </span>
                            @if($req->status === 'rejected' && $req->rejection_reason)
                                <div class="text-danger mt-1" style="font-size:10px;" title="{{ $req->rejection_reason }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ Str::limit($req->rejection_reason, 30) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:12px;">{{ $req->assigned_shop ?? '—' }}</td>
                        <td>
                            @if($req->estimated_cost)
                                <span class="fw-semibold text-success">₱{{ number_format($req->estimated_cost, 2) }}</span>
                                @if($req->actual_cost)
                                    <div class="text-muted" style="font-size:10px;">Actual: ₱{{ number_format($req->actual_cost, 2) }}</div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isManager ? 10 : 9 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            No maintenance requests found.<br>
                            <small>Click "Submit New Request" to get started.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ====================================================== --}}
{{-- MODAL: Submit New Maintenance Request                   --}}
{{-- ====================================================== --}}
<div class="modal fade" id="submitMaintenanceRequestModal" tabindex="-1" aria-labelledby="submitMaintLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="submitMaintLabel">
                        <i class="bi bi-wrench-adjustable text-danger me-2"></i> Submit Maintenance / Repair Request
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Report a vehicle anomaly, request a reimbursement, or schedule preventive maintenance.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('ess.maintenance.store') }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        {{-- Request Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Request Type <span class="text-danger">*</span></label>
                            <select name="request_type" class="form-select" required onchange="toggleReimbursementField(this.value)">
                                <option value="">— Select type —</option>
                                <option value="repair">🔧 Vehicle Repair</option>
                                <option value="reimbursement">💵 Reimbursement Request</option>
                                <option value="scheduled_maintenance">📅 Scheduled Maintenance (PMS)</option>
                            </select>
                        </div>
                        {{-- Vehicle --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select" required>
                                <option value="">— Select vehicle —</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->license_plate }} — {{ $v->make }} {{ $v->model }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Anomaly Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Describe the Issue / Request <span class="text-danger">*</span></label>
                            <textarea name="anomaly_description" class="form-control" rows="3" placeholder="e.g., Engine makes loud knocking sound when accelerating. Warning light is on." required maxlength="2000"></textarea>
                        </div>
                        {{-- Anomaly Date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Date Observed <span class="text-danger">*</span></label>
                            <input type="date" name="anomaly_date" class="form-control" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        {{-- Urgency --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Urgency Level <span class="text-danger">*</span></label>
                            <select name="urgency_level" class="form-select" required>
                                <option value="low">🟢 Low</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="critical">🔴 Critical</option>
                            </select>
                        </div>
                        {{-- Odometer --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Odometer Reading (km)</label>
                            <input type="number" name="odometer_reading" class="form-control" placeholder="e.g., 45200" min="0">
                        </div>
                        {{-- Reimbursement fields (hidden by default) --}}
                        <div class="col-md-6" id="reimbursementAmountField" style="display:none;">
                            <label class="form-label fw-semibold small">Reimbursement Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="reimbursement_amount" class="form-control" placeholder="e.g., 1200.00" step="0.01" min="0">
                        </div>
                        <div class="col-md-6" id="receiptNumberField" style="display:none;">
                            <label class="form-label fw-semibold small">Receipt / OR Number</label>
                            <input type="text" name="receipt_number" class="form-control" placeholder="e.g., OR-20241005-001" maxlength="100">
                        </div>
                        {{-- Remarks --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Additional Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Any other information the Fleet Manager should know..." maxlength="1000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium rounded-3 px-5 fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleReimbursementField(type) {
    const amountField  = document.getElementById('reimbursementAmountField');
    const receiptField = document.getElementById('receiptNumberField');
    if (type === 'reimbursement') {
        amountField.style.display  = '';
        receiptField.style.display = '';
    } else {
        amountField.style.display  = 'none';
        receiptField.style.display = 'none';
    }
}
</script>
@endsection
