@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; background: #CE2029 !important;">FLEET MANAGEMENT</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-clipboard2-check text-danger me-1"></i> Dispatch Queue</span>
        </div>
        <h2 class="page-header-title mt-1">ESS Vehicle Requests — Dispatch Queue</h2>
        <p class="page-header-subtitle">Review and approve employee vehicle reservation requests. Assign a vehicle and driver — the dispatch calendar updates automatically.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ess.vehicles.index') }}" class="btn btn-outline-secondary rounded-3 px-4">
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
                    <span class="text-muted small fw-bold text-uppercase">Pending Approval</span>
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
                    <span class="text-muted small fw-bold text-uppercase">Total Requests</span>
                    <h3 class="fw-bold my-1">{{ $essRequests->count() }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-4 fs-4"><i class="bi bi-car-front"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Available Vehicles</span>
                    <h3 class="fw-bold my-1 text-success">{{ $vehicles->count() }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-4 fs-4"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card premium-card p-3 mb-4 border-0 shadow-sm bg-light">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="fw-bold text-dark me-2" style="font-size:13px;"><i class="bi bi-funnel-fill text-danger me-1"></i> Status:</span>
        @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
            <a href="{{ route('ess.vehicles.queue', ['filter'=>$val, 'urgency'=>$urgency]) }}"
               class="btn btn-sm {{ ($filter === $val || ($val === 'all' && $filter === 'all')) ? 'btn-danger text-white' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                {{ $label }}
            </a>
        @endforeach
        <span class="ms-3 fw-bold text-dark me-2" style="font-size:13px;"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Urgency:</span>
        @foreach(['all'=>'All','priority'=>'🔴 Priority','routine'=>'🟢 Routine'] as $val => $label)
            <a href="{{ route('ess.vehicles.queue', ['filter'=>$filter, 'urgency'=>$val]) }}"
               class="btn btn-sm {{ $urgency === $val ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Queue Table --}}
<div class="card premium-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-car-front text-danger me-2"></i> ESS Vehicle Request Queue</h5>
        <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background:#CE2029!important;">{{ $essRequests->count() }} Requests</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size:13px;">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Employee</th>
                    <th>Purpose</th>
                    <th>Destination</th>
                    <th>Date & Time</th>
                    <th>Pax</th>
                    <th>Preference</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($essRequests as $req)
                    @php
                        $purposeIcons = ['meeting'=>'bi-briefcase','field_visit'=>'bi-map','airport'=>'bi-airplane','inter_branch'=>'bi-building','errand'=>'bi-bag','emergency'=>'bi-exclamation-triangle'];
                        $icon = $purposeIcons[$req->purpose_type] ?? 'bi-car-front';
                        $urgColor = $req->urgency_level === 'priority' ? 'danger' : 'secondary';
                        $statusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','completed'=>'secondary','cancelled'=>'dark'];
                        $sc = $statusColors[$req->status] ?? 'secondary';
                    @endphp
                    <tr>
                        <td><span class="fw-bold text-danger">#{{ $req->id }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $req->employee->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ ucfirst($req->employee->role ?? '') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill" style="font-size:11px;">
                                <i class="bi {{ $icon }} me-1"></i>{{ $req->purpose_type_label }}
                            </span>
                        </td>
                        <td class="fw-semibold" style="max-width:130px;" title="{{ $req->destination }}">{{ Str::limit($req->destination, 30) }}</td>
                        <td>
                            <div class="fw-semibold">{{ optional($req->reservation_date)->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $req->start_time }} → {{ $req->end_time }}</div>
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-2">{{ $req->num_passengers }}</span></td>
                        <td class="text-muted" style="font-size:12px;">{{ $req->vehicle_preference ?: '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $urgColor }} px-2 py-1 rounded-pill text-uppercase fw-bold" style="font-size:10px;">
                                {{ $req->urgency_level === 'priority' ? '🔴 Priority' : '🟢 Routine' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }} border border-{{ $sc }} px-2 py-1 rounded-pill fw-bold" style="font-size:10px;">
                                {{ ucwords(str_replace('_',' ',$req->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($req->status === 'pending')
                                    <button class="btn btn-sm btn-success rounded-pill px-2 fw-bold" style="font-size:11px;"
                                        data-bs-toggle="modal" data-bs-target="#approveVehicleModal{{ $req->id }}">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-2 fw-bold" style="font-size:11px;"
                                        data-bs-toggle="modal" data-bs-target="#rejectVehicleModal{{ $req->id }}">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                @endif
                                @if($req->reservation_id)
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2" style="font-size:10px;">
                                        <i class="bi bi-calendar-check me-1"></i>Res. #{{ $req->reservation_id }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-car-front fs-1 d-block mb-2 opacity-25"></i>
                            No ESS vehicle requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Approve Modals ─────────────────────────────────────── --}}
@foreach($essRequests->where('status','pending') as $req)
<div class="modal fade" id="approveVehicleModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle text-success me-2"></i>Approve & Assign — ESS Request #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ess.vehicles.approve', $req->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="alert alert-info rounded-3 border-0 mb-3" style="font-size:12px;">
                        <strong>{{ $req->employee->name ?? 'Employee' }}</strong> requests a vehicle for
                        <strong>{{ $req->purpose_type_label }}</strong> on
                        <strong>{{ optional($req->reservation_date)->format('M d, Y') }}</strong>
                        ({{ $req->start_time }} – {{ $req->end_time }}) to
                        <strong>{{ $req->destination }}</strong>. {{ $req->num_passengers }} passenger(s).
                        @if($req->vehicle_preference) Preferred: {{ $req->vehicle_preference }}. @endif
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assign Vehicle <span class="text-danger">*</span></label>
                            <select name="assigned_vehicle_id" class="form-select" required>
                                <option value="">— Select vehicle —</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->license_plate }} — {{ $v->make }} {{ $v->model }} ({{ $v->type ?? 'Vehicle' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assign Driver</label>
                            <select name="assigned_driver_id" class="form-select">
                                <option value="">— No driver (self-driven) —</option>
                                @foreach($drivers as $d)
                                    @if($d->user)
                                        <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded-3" style="font-size:12px;">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Approving will automatically create a <strong>Vehicle Reservation record</strong> on the Dispatch Calendar. The employee will be notified.
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
<div class="modal fade" id="rejectVehicleModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle text-danger me-2"></i>Reject Vehicle Request #{{ $req->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ess.vehicles.reject', $req->id) }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="e.g., No vehicles available on requested date. Please select another date." required maxlength="1000"></textarea>
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

@endsection
