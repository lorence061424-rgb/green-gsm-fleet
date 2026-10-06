@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; background: #CE2029 !important;">HIRNA MOBILITY SOLUTIONS INC.</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-person-lines-fill text-danger me-1"></i> Employee Self-Service (ESS) Portal</span>
        </div>
        <h2 class="page-header-title mt-1">ESS — Vehicle Reservation Requests</h2>
        <p class="page-header-subtitle">Request a company vehicle for meetings, field visits, or official business. Track your request and see assigned vehicle details.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        @if($isManager)
            <a href="{{ route('ess.vehicles.queue') }}" class="btn btn-warning rounded-3 px-4 shadow-sm fw-bold">
                <i class="bi bi-inbox-fill me-1"></i> Dispatch Queue
            </a>
        @endif
        <button class="btn btn-premium rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#submitVehicleRequestModal">
            <i class="bi bi-plus-circle me-1"></i> Request a Vehicle
        </button>
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

{{-- Integration Badge --}}
<div class="alert alert-dark bg-dark text-white border-0 rounded-4 p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-diagram-3-fill text-success fs-4 me-2"></i>
            <div>
                <span class="fw-bold d-block text-white small">ESS ↔ VEHICLE RESERVATION & DISPATCH INTEGRATION (TEAM 7)</span>
                <span class="text-white-50 fw-medium" style="font-size: 11px;">Approved ESS requests auto-create a record on the Dispatch Calendar. You'll see your assigned vehicle and driver here.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-success text-white fw-bold px-3 py-2"><i class="bi bi-calendar-check me-1"></i> Auto Dispatch Calendar</span>
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-person-badge me-1"></i> Driver Assignment</span>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
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
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Pending</span>
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
                    <span class="text-muted small fw-bold text-uppercase">Approved</span>
                    <h3 class="fw-bold my-1 text-success">{{ $essRequests->where('status','approved')->count() }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-4 fs-4"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-card p-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Completed</span>
                    <h3 class="fw-bold my-1 text-secondary">{{ $essRequests->where('status','completed')->count() }}</h3>
                </div>
                <div class="bg-secondary-subtle text-secondary p-3 rounded-4 fs-4"><i class="bi bi-flag-fill"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Requests Table --}}
<div class="card premium-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-car-front text-danger me-2"></i> {{ $isManager ? 'All ESS Vehicle Requests' : 'My Vehicle Requests' }}</h5>
        <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background:#CE2029!important;">{{ $essRequests->count() }} Requests</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size:13px;">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    @if($isManager)<th>Employee</th>@endif
                    <th>Purpose</th>
                    <th>Destination</th>
                    <th>Date & Time</th>
                    <th>Passengers</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Assigned Vehicle / Driver</th>
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
                        @if($isManager)
                            <td>
                                <div class="fw-semibold">{{ $req->employee->name ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ ucfirst($req->employee->role ?? '') }}</div>
                            </td>
                        @endif
                        <td>
                            <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill" style="font-size:11px;">
                                <i class="bi {{ $icon }} me-1"></i>{{ $req->purpose_type_label }}
                            </span>
                            <div class="text-muted mt-1" style="font-size:11px;" title="{{ $req->purpose_description }}">{{ Str::limit($req->purpose_description, 40) }}</div>
                        </td>
                        <td class="fw-semibold">{{ $req->destination }}</td>
                        <td>
                            <div class="fw-semibold">{{ optional($req->reservation_date)->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $req->start_time }} → {{ $req->end_time }}</div>
                            <div class="text-muted" style="font-size:10px;">{{ $req->is_roundtrip ? '↩ Round Trip' : '→ One Way' }}</div>
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-2">{{ $req->num_passengers }} pax</span></td>
                        <td>
                            <span class="badge bg-{{ $urgColor }} px-2 py-1 rounded-pill text-uppercase fw-bold" style="font-size:10px;">
                                {{ $req->urgency_level === 'priority' ? '🔴 Priority' : '🟢 Routine' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }} border border-{{ $sc }} px-2 py-1 rounded-pill fw-bold" style="font-size:10px;">
                                {{ ucwords(str_replace('_',' ',$req->status)) }}
                            </span>
                            @if($req->status === 'rejected' && $req->rejection_reason)
                                <div class="text-danger mt-1" style="font-size:10px;" title="{{ $req->rejection_reason }}"><i class="bi bi-info-circle me-1"></i>{{ Str::limit($req->rejection_reason, 25) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($req->assignedVehicle)
                                <div class="fw-semibold text-success">{{ $req->assignedVehicle->license_plate }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $req->assignedVehicle->make }} {{ $req->assignedVehicle->model }}</div>
                                @if($req->assignedDriver && $req->assignedDriver->user)
                                    <div class="text-primary" style="font-size:11px;"><i class="bi bi-person-fill me-1"></i>{{ $req->assignedDriver->user->name }}</div>
                                @endif
                            @else
                                <span class="text-muted" style="font-size:12px;">— Pending Assignment —</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if(in_array($req->status, ['pending', 'approved']))
                                    <form method="POST" action="{{ route('ess.vehicles.cancel', $req->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 fw-bold" style="font-size:11px;"
                                            onclick="return confirm('Cancel this vehicle request?')">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                    </form>
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
                        <td colspan="{{ $isManager ? 10 : 9 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-car-front fs-1 d-block mb-2 opacity-25"></i>
                            No vehicle requests found.<br>
                            <small>Click "Request a Vehicle" to submit your first request.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ====================================================== --}}
{{-- MODAL: Submit Vehicle Reservation Request               --}}
{{-- ====================================================== --}}
<div class="modal fade" id="submitVehicleRequestModal" tabindex="-1" aria-labelledby="submitVehicleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold" id="submitVehicleLabel">
                        <i class="bi bi-car-front text-danger me-2"></i> Request a Company Vehicle
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Request a vehicle for meetings, field visits, airport transfers, or other official business.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('ess.vehicles.store') }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        {{-- Purpose Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Purpose Type <span class="text-danger">*</span></label>
                            <select name="purpose_type" class="form-select" required>
                                <option value="">— Select purpose —</option>
                                <option value="meeting">💼 Meeting / Official Business</option>
                                <option value="field_visit">🗺️ Field Visit / Inspection</option>
                                <option value="airport">✈️ Airport / Terminal Transfer</option>
                                <option value="inter_branch">🏢 Inter-Branch Travel</option>
                                <option value="errand">🛍️ Errand / Delivery</option>
                                <option value="emergency">🚨 Emergency Use</option>
                            </select>
                        </div>
                        {{-- Urgency --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Urgency Level <span class="text-danger">*</span></label>
                            <select name="urgency_level" class="form-select" required>
                                <option value="routine" selected>🟢 Routine</option>
                                <option value="priority">🔴 Priority / Urgent</option>
                            </select>
                        </div>
                        {{-- Purpose Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Purpose Description <span class="text-danger">*</span></label>
                            <textarea name="purpose_description" class="form-control" rows="2" placeholder="e.g., Board meeting with client at Shangri-La Hotel, Makati City" required maxlength="1000"></textarea>
                        </div>
                        {{-- Destination --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" class="form-control" placeholder="e.g., Shangri-La Makati, Edsa cor. Ayala Ave." required maxlength="255">
                        </div>
                        {{-- Passengers --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">No. of Passengers <span class="text-danger">*</span></label>
                            <input type="number" name="num_passengers" class="form-control" min="1" max="30" value="1" required>
                        </div>
                        {{-- Date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Reservation Date <span class="text-danger">*</span></label>
                            <input type="date" name="reservation_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        {{-- Start Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Departure Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        {{-- End Time --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Expected Return Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        {{-- Vehicle Preference --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Vehicle Type Preference</label>
                            <select name="vehicle_preference" class="form-select">
                                <option value="">— No preference —</option>
                                <option value="Sedan">Sedan</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van / Hi-Ace</option>
                                <option value="Truck">Pickup / Truck</option>
                                <option value="Bus">Bus / Coaster</option>
                            </select>
                        </div>
                        {{-- Round trip --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Trip Type</label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_roundtrip" id="roundtripYes" value="1" checked>
                                    <label class="form-check-label small fw-semibold" for="roundtripYes">↩ Round Trip</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_roundtrip" id="roundtripNo" value="0">
                                    <label class="form-check-label small fw-semibold" for="roundtripNo">→ One Way</label>
                                </div>
                            </div>
                        </div>
                        {{-- Remarks --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Additional Instructions / Remarks</label>
                            <textarea name="additional_remarks" class="form-control" rows="2" placeholder="e.g., Needs tinted windows, require GPS tracking, VIP passenger." maxlength="1000"></textarea>
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
@endsection
