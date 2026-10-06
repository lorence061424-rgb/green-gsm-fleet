@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px; background: #CE2029 !important;">HIRNA MOBILITY SOLUTIONS INC.</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-wrench-adjustable text-danger me-1"></i> Fleet Maintenance & Repairs</span>
        </div>
        <h2 class="page-header-title mt-1">Preventive Maintenance Services (PMS)</h2>
        <p class="page-header-subtitle">Schedule engine tune-ups, log repair expenses, process incoming driver requests & reimbursements, and monitor vehicle availability.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-warning text-dark fw-bold rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#simulateIncomingMaintenanceModal">
            <i class="bi bi-plus-circle-dotted me-1"></i> Simulate Incoming Request
        </button>
        <button type="button" class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#schedulePMSModal" onclick="openScheduleModal();">
            <i class="bi bi-calendar-plus me-1"></i> Schedule Maintenance
        </button>
    </div>
</div>

<!-- Success / Flash Messages -->
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

<!-- Inter-System Integration Connections Badge Banner -->
<div class="alert alert-dark bg-dark text-white border-0 rounded-4 p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-diagram-3-fill text-warning fs-4 me-2"></i>
            <div>
                <span class="fw-bold d-block text-white small">HIRNA MOBILITY INTER-SYSTEM INTEGRATION PIPELINE</span>
                <span class="text-white-50 fw-medium" style="font-size: 11px;">Connected to peer enterprise systems for incoming driver anomaly reports, spare parts requisitions, and reimbursement GL forwarding.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><i class="bi bi-inbox-fill me-1"></i> Incoming Requests Feed (ESS/Driver)</span>
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-cash-stack me-1"></i> Team 5: Payroll & Reimbursements</span>
        </div>
    </div>
</div>

<!-- Navigation Tabs: Active Records vs. Incoming Requests Feed -->
<ul class="nav nav-pills mb-4 p-1 bg-white rounded-4 shadow-sm border border-secondary-subtle" style="max-width: 600px;">
    <li class="nav-item flex-fill text-center">
        <a class="nav-link rounded-3 fw-bold {{ ($tab ?? 'records') === 'records' ? 'active bg-danger' : 'text-dark' }}" 
           href="{{ route('maintenance.index', ['tab' => 'records', 'filter' => $filter]) }}"
           style="{{ ($tab ?? 'records') === 'records' ? 'background: #CE2029 !important;' : '' }}">
            <i class="bi bi-wrench-adjustable me-1"></i> Active PMS & Repairs
            <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $records->count() }}</span>
        </a>
    </li>
    <li class="nav-item flex-fill text-center">
        <a class="nav-link rounded-3 fw-bold {{ ($tab ?? 'records') === 'incoming' ? 'active bg-danger' : 'text-dark' }}" 
           href="{{ route('maintenance.index', ['tab' => 'incoming']) }}"
           style="{{ ($tab ?? 'records') === 'incoming' ? 'background: #CE2029 !important;' : '' }}">
            <i class="bi bi-inbox-fill me-1"></i> Incoming Requests
            @if(($pendingIncomingCount ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $pendingIncomingCount }} New</span>
            @endif
        </a>
    </li>
</ul>

@if(($tab ?? 'records') === 'incoming')
    <!-- ============================================================ -->
    <!-- TAB: INCOMING REPAIR & REIMBURSEMENT REQUESTS                -->
    <!-- ============================================================ -->
    <div class="card premium-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-inbox-fill text-danger me-2"></i> Incoming Maintenance & Reimbursement Requests</h5>
                <small class="text-muted">Review anomaly reports and expense claims submitted by drivers and employees. Accept to automatically convert into active PMS records.</small>
            </div>
            <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background: #CE2029 !important;">{{ $incomingRequests->count() }} Total Requests</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>#ID</th>
                        <th>REQUESTOR</th>
                        <th>VEHICLE</th>
                        <th>TYPE</th>
                        <th>ANOMALY / DESCRIPTION</th>
                        <th>DATE REPORTED</th>
                        <th>URGENCY</th>
                        <th>STATUS</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingRequests as $req)
                        @php
                            $urgencyColors = ['critical'=>'danger','high'=>'warning','medium'=>'info','low'=>'secondary'];
                            $uc = $urgencyColors[$req->urgency_level] ?? 'secondary';
                            $statusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','in_progress'=>'info','completed'=>'secondary'];
                            $sc = $statusColors[$req->status] ?? 'secondary';
                            $typeLabels = ['repair'=>'Vehicle Repair','reimbursement'=>'Reimbursement','scheduled_maintenance'=>'Scheduled PMS'];
                        @endphp
                        <tr>
                            <td><span class="fw-bold text-danger">#{{ $req->id }}</span></td>
                            <td>
                                <span class="fw-semibold d-block">{{ $req->employee->name ?? 'Hirna Employee' }}</span>
                                <small class="text-muted">{{ ucfirst($req->employee->role ?? 'Staff') }}</small>
                            </td>
                            <td>
                                @if($req->vehicle)
                                    <span class="fw-bold text-dark d-block">{{ $req->vehicle->make }} {{ $req->vehicle->model }}</span>
                                    <small class="badge bg-secondary text-white">{{ $req->vehicle->license_plate }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($req->request_type === 'reimbursement')
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-cash me-1"></i>Reimbursement
                                    </span>
                                    @if($req->reimbursement_amount)
                                        <small class="d-block text-success fw-bold mt-1">₱{{ number_format($req->reimbursement_amount, 2) }}</small>
                                    @endif
                                @elseif($req->request_type === 'scheduled_maintenance')
                                    <span class="badge bg-info-subtle text-info border border-info px-2 py-1 rounded-pill">
                                        <i class="bi bi-calendar-check me-1"></i>Scheduled PMS
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill">
                                        <i class="bi bi-wrench me-1"></i>Repair Request
                                    </span>
                                @endif
                            </td>
                            <td style="max-width: 250px;">
                                <div class="text-truncate fw-medium" title="{{ $req->anomaly_description }}">{{ $req->anomaly_description }}</div>
                                @if($req->receipt_number)
                                    <small class="text-muted"><i class="bi bi-receipt me-1"></i>Receipt: {{ $req->receipt_number }}</small>
                                @endif
                            </td>
                            <td class="text-muted">{{ optional($req->anomaly_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $uc }} text-uppercase px-2 py-1 rounded-pill" style="font-size: 10px;">
                                    {{ ucfirst($req->urgency_level) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }} border border-{{ $sc }} px-2 py-1 rounded-pill fw-bold">
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                                @if($req->maintenance_record_id)
                                    <small class="d-block text-muted mt-1" style="font-size: 10px;">PMS Record #{{ $req->maintenance_record_id }}</small>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($req->status === 'pending')
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-success rounded-3 px-2 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#acceptRequestModal{{ $req->id }}">
                                            <i class="bi bi-check-lg me-1"></i> Accept & Create PMS
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectRequestModal{{ $req->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small"><i class="bi bi-check2-all text-success me-1"></i> Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No incoming repair or reimbursement requests in the queue.<br>
                                <small>Click <strong>"Simulate Incoming Request"</strong> to test this feature.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <!-- ============================================================ -->
    <!-- TAB: ACTIVE PMS & REPAIR RECORDS (STANDARD VIEW)             -->
    <!-- ============================================================ -->
    <!-- Vehicle Repair History Filter Bar -->
    <div class="card premium-card p-3 mb-4 border-0 shadow-sm bg-light">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold text-dark me-2" style="font-size: 13px;"><i class="bi bi-funnel-fill text-danger me-1"></i> Repair History Filter:</span>
                <a href="{{ route('maintenance.index', ['tab' => 'records']) }}" class="btn btn-sm {{ empty($filter) ? 'btn-danger text-white' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                    <i class="bi bi-grid-fill me-1"></i> All Records
                </a>
                <a href="{{ route('maintenance.index', ['tab' => 'records', 'filter' => 'maintenance']) }}" class="btn btn-sm {{ $filter === 'maintenance' ? 'btn-danger text-white' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                    <i class="bi bi-wrench me-1"></i> Maintenance (PMS)
                </a>
                <a href="{{ route('maintenance.index', ['tab' => 'records', 'filter' => 'active']) }}" class="btn btn-sm {{ $filter === 'active' ? 'btn-danger text-white' : 'btn-outline-success' }} rounded-pill px-3 fw-bold">
                    <i class="bi bi-gear-wide-connected me-1"></i> Active Repairs
                </a>
                <a href="{{ route('maintenance.index', ['tab' => 'records', 'filter' => 'inactive']) }}" class="btn btn-sm {{ $filter === 'inactive' ? 'btn-danger text-white' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold">
                    <i class="bi bi-archive me-1"></i> Inactive / Completed
                </a>
            </div>
            <span class="text-muted small">Showing {{ $records->count() }} records</span>
        </div>
    </div>

    <!-- Scheduled and Log PMS Table Container -->
    <div class="card premium-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable text-danger me-2"></i> Vehicle Repair & PMS History</h5>
            <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background: #CE2029 !important;">{{ $records->count() }} Filtered Records</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted" style="font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        <th>VEHICLE</th>
                        <th>SERVICE TYPE</th>
                        <th>DESCRIPTION</th>
                        <th>ESTIMATED COST</th>
                        <th>SCHEDULED DATE</th>
                        <th>COMPLETION DATE</th>
                        <th>MAINTENANCE STATUS</th>
                        <th class="text-end">ACTION / UPDATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>
                                <span class="fw-bold d-block text-dark">{{ $record->vehicle->make ?? 'Vehicle' }} {{ $record->vehicle->model ?? '' }}</span>
                                <small class="badge bg-secondary text-white mt-1">{{ $record->vehicle->license_plate ?? 'N/A' }}</small>
                            </td>
                            <td class="fw-bold text-dark">{{ $record->service_type }}</td>
                            <td style="font-size: 13px; max-width: 220px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $record->description ?: 'Routine Maintenance' }}
                            </td>
                            <td class="fw-bold text-danger">₱{{ number_format($record->cost, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->scheduled_date)->toFormattedDateString() }}</td>
                            <td>
                                {{ $record->completion_date ? \Carbon\Carbon::parse($record->completion_date)->toFormattedDateString() : 'Pending' }}
                            </td>
                            <td>
                                @if($record->status === 'completed')
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>
                                    <small class="d-block text-success fw-bold mt-1" style="font-size: 10.5px;"><i class="bi bi-check2-circle me-1"></i> Fleet Active / Available</small>
                                @elseif($record->status === 'in_progress')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-gear-fill me-1"></i> In Progress</span>
                                    <small class="d-block text-danger fw-bold mt-1" style="font-size: 10.5px;"><i class="bi bi-exclamation-triangle-fill me-1"></i> Vehicle Maintenance (Not Available)</small>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-clock-fill me-1"></i> Scheduled</span>
                                    <small class="d-block text-muted mt-1" style="font-size: 10.5px;">Pending PMS Service</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    @if($record->status !== 'in_progress')
                                        <form action="{{ route('maintenance.update-status', $record) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="in_progress">
                                            <input type="hidden" name="cost" value="{{ $record->cost }}">
                                            <button type="submit" class="btn btn-sm btn-warning text-dark rounded-3 px-2 py-1 fw-bold shadow-sm" style="font-size: 11px;" title="Set Maintenance In Progress (Takes vehicle offline to Maintenance)">
                                                ⚙️ Set In Progress
                                            </button>
                                        </form>
                                    @endif
                                    @if($record->status !== 'completed')
                                        <form action="{{ route('maintenance.update-status', $record) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <input type="hidden" name="cost" value="{{ $record->cost }}">
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 px-2 py-1 fw-bold shadow-sm" style="font-size: 11px;" title="Mark Maintenance Completed (Releases vehicle to Active)">
                                                ✅ Mark Completed
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1 fw-medium" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $record->id }}" style="font-size: 11px;">
                                        <i class="bi bi-pencil-square me-1"></i> Edit Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-tools fs-1 d-block mb-2 text-secondary"></i>
                                No maintenance service records logged yet. Click <strong>Schedule Maintenance</strong> to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- ============================================================ -->
<!-- MODAL: SCHEDULE NEW PMS MAINTENANCE                          -->
<!-- ============================================================ -->
<div class="modal fade" id="schedulePMSModal" tabindex="-1" aria-labelledby="schedulePMSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-danger text-white p-4" style="background: linear-gradient(135deg, #CE2029 0%, #7F1D1D 100%) !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                        <i class="bi bi-wrench-adjustable-circle fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="schedulePMSModalLabel">Schedule Preventive Maintenance (PMS)</h5>
                        <p class="mb-0 text-white-50" style="font-size: 13px;">Create a scheduled or active repair record for a fleet vehicle.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('maintenance.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Select Vehicle</label>
                            <select name="vehicle_id" id="modalVehicleSelect" class="form-select rounded-3" required>
                                <option value="">-- Choose Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->license_plate }} - {{ $vehicle->make }} {{ $vehicle->model }} ({{ ucfirst($vehicle->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
                            <select id="serviceTypeCategorySelect" class="form-select rounded-3 mb-2" required onchange="handleServiceTypeSelect(this)">
                                <option value="">-- Choose Standard Service Type --</option>
                                <optgroup label="🔧 Preventive Maintenance Services (PMS)">
                                    <option value="Engine Tune-up & Oil Change">Engine Tune-up & Oil Change</option>
                                    <option value="Routine Oil & Filter Replacement">Routine Oil & Filter Replacement</option>
                                    <option value="Tire Alignment & Wheel Balancing">Tire Alignment & Wheel Balancing</option>
                                    <option value="Brake Pad Replacement & Inspection">Brake Pad Replacement & Inspection</option>
                                    <option value="Air Conditioning & Cabin Filter Cleaning">Air Conditioning & Cabin Filter Cleaning</option>
                                    <option value="Transmission & Fluid Flush">Transmission & Fluid Flush</option>
                                    <option value="Battery Health Check & Replacement">Battery Health Check & Replacement</option>
                                </optgroup>
                                <optgroup label="⚙️ Corrective Repairs & Anomaly Fixes">
                                    <option value="Engine Knocking & Anomaly Repair">Engine Knocking & Anomaly Repair</option>
                                    <option value="Suspension & Shock Absorber Repair">Suspension & Shock Absorber Repair</option>
                                    <option value="Electrical System & Sensor Diagnostic">Electrical System & Sensor Diagnostic</option>
                                    <option value="Emergency Tire Puncture & Vulcanizing">Emergency Tire Puncture & Vulcanizing</option>
                                    <option value="Reimbursement Repair Claim">Reimbursement Repair Claim</option>
                                </optgroup>
                                <option value="custom">✏️ Other / Custom Service Type...</option>
                            </select>
                            <input type="text" name="service_type" id="serviceTypeTextInput" placeholder="Selected service type or custom technician notes..." class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Scheduled Service Date</label>
                            <input type="date" name="scheduled_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estimated Cost (₱)</label>
                            <input type="number" step="0.01" name="cost" placeholder="e.g. 3500.00" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Initial Maintenance Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="scheduled" selected>📅 Scheduled (Pending Service)</option>
                                <option value="in_progress">⚙️ In Progress (Sets vehicle offline to Maintenance)</option>
                                <option value="completed">✅ Completed (Releases vehicle to Active)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Service Notes / Details</label>
                            <textarea name="description" rows="3" placeholder="Specify symptoms, spare parts requisitions, or technician notes..." class="form-control rounded-3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold" style="background: #CE2029 !important;">
                        <i class="bi bi-calendar-check me-1"></i> Confirm & Schedule PMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: SIMULATE INCOMING REQUEST (FOR DEMO & DEFENSE)        -->
<!-- ============================================================ -->
<div class="modal fade" id="simulateIncomingMaintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-warning text-dark p-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-inbox-fill fs-3 me-2"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Simulate Incoming Maintenance / Reimbursement Request</h5>
                        <small class="text-muted">Simulate an employee or driver sending an anomaly report or reimbursement claim.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('maintenance.incoming.sample') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Request Type</label>
                            <select name="request_type" class="form-select rounded-3" required onchange="toggleSimReimbursement(this.value)">
                                <option value="repair" selected>🔧 Vehicle Anomaly / Repair Report</option>
                                <option value="reimbursement">💵 Emergency Repair Reimbursement Claim</option>
                                <option value="scheduled_maintenance">📅 Scheduled PMS Request</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vehicle</label>
                            <select name="vehicle_id" class="form-select rounded-3" required>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->license_plate }} - {{ $v->make }} {{ $v->model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Urgency Level</label>
                            <select name="urgency_level" class="form-select rounded-3" required>
                                <option value="low">🟢 Low</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="critical">🔴 Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="simReimbursementDiv" style="display: none;">
                            <label class="form-label fw-bold">Reimbursement Amount (₱)</label>
                            <input type="number" step="0.01" name="reimbursement_amount" placeholder="e.g. 1500.00" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Anomaly / Request Description</label>
                            <textarea name="anomaly_description" rows="3" class="form-control rounded-3" placeholder="Describe the vehicle issue observed by the driver..." required>Driver reported abnormal knocking sound in the front engine compartment and soft brake feel during descent.</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3">
                        <i class="bi bi-send-fill me-1"></i> Send Request to Queue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ACCEPT & REJECT MODALS FOR EACH INCOMING REQUEST             -->
<!-- ============================================================ -->
@if(isset($incomingRequests))
    @foreach($incomingRequests->where('status', 'pending') as $req)
        <!-- Accept Modal -->
        <div class="modal fade" id="acceptRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 bg-success text-white p-3">
                        <h6 class="modal-title fw-bold"><i class="bi bi-check-circle me-1"></i> Accept Request #{{ $req->id }} & Convert to PMS</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('maintenance.incoming.accept', $req) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="p-2 bg-light rounded-3 mb-3 small">
                                <strong>Vehicle:</strong> {{ $req->vehicle->license_plate ?? 'N/A' }} ({{ $req->vehicle->make ?? '' }} {{ $req->vehicle->model ?? '' }})<br>
                                <strong>Issue:</strong> {{ $req->anomaly_description }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Assigned Repair Motorshop</label>
                                <input type="text" name="assigned_shop" class="form-control rounded-3" placeholder="e.g. Mabuhay Auto Repair Shop" value="Mabuhay Auto Repair Shop">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Estimated Cost (₱)</label>
                                    <input type="number" step="0.01" name="cost" class="form-control rounded-3" value="{{ $req->reimbursement_amount ?? 3500.00 }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Service Date</label>
                                    <input type="date" name="scheduled_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Maintenance Status</label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="scheduled">📅 Scheduled (Pending)</option>
                                    <option value="in_progress" selected>⚙️ In Progress (Sets vehicle offline)</option>
                                    <option value="completed">✅ Completed</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold small">Manager Remarks</label>
                                <textarea name="remarks" rows="2" class="form-control rounded-3" placeholder="Technician instructions..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3 bg-light">
                            <button type="button" class="btn btn-outline-secondary rounded-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold rounded-3 btn-sm">
                                <i class="bi bi-check2-circle me-1"></i> Accept & Convert to PMS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectRequestModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 bg-danger text-white p-3">
                        <h6 class="modal-title fw-bold"><i class="bi bi-x-circle me-1"></i> Reject Request #{{ $req->id }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('maintenance.incoming.reject', $req) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <label class="form-label fw-bold small">Reason for Rejection</label>
                            <textarea name="rejection_reason" rows="3" class="form-control rounded-3" placeholder="Explain why the maintenance or reimbursement claim is declined..." required></textarea>
                        </div>
                        <div class="modal-footer border-0 p-3 bg-light">
                            <button type="button" class="btn btn-outline-secondary rounded-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-bold rounded-3 btn-sm">
                                <i class="bi bi-trash me-1"></i> Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Update Details Modal for Existing PMS Records -->
@foreach($records as $record)
    <div class="modal fade" id="updateStatusModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 bg-danger text-white p-3" style="background: #CE2029 !important;">
                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-1"></i> Edit Maintenance #{{ $record->id }}</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('maintenance.update-status', $record) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Maintenance Status</label>
                            <select name="status" id="statusSelect{{ $record->id }}" class="form-select rounded-3" onchange="toggleCompletionDate({{ $record->id }})" required>
                                <option value="scheduled" {{ $record->status === 'scheduled' ? 'selected' : '' }}>📅 Scheduled (Pending)</option>
                                <option value="in_progress" {{ $record->status === 'in_progress' ? 'selected' : '' }}>⚙️ In Progress (Vehicle Offline)</option>
                                <option value="completed" {{ $record->status === 'completed' ? 'selected' : '' }}>✅ Completed (Vehicle Active)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Service Cost (₱)</label>
                            <input type="number" step="0.01" name="cost" class="form-control rounded-3" value="{{ $record->cost }}" required>
                        </div>
                        <div class="mb-3 {{ $record->status === 'completed' ? '' : 'd-none' }}" id="completionDateDiv{{ $record->id }}">
                            <label class="form-label fw-bold small">Completion Date</label>
                            <input type="date" name="completion_date" class="form-control rounded-3" value="{{ $record->completion_date ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light">
                        <button type="button" class="btn btn-outline-secondary rounded-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger fw-bold rounded-3 btn-sm" style="background: #CE2029 !important;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@section('scripts')
<script>
function toggleCompletionDate(recordId) {
    const statusSelect = document.getElementById('statusSelect' + recordId);
    const div = document.getElementById('completionDateDiv' + recordId);
    if (statusSelect && div) {
        if (statusSelect.value === 'completed') {
            div.classList.remove('d-none');
        } else {
            div.classList.add('d-none');
        }
    }
}

function toggleSimReimbursement(type) {
    const div = document.getElementById('simReimbursementDiv');
    if (div) {
        div.style.display = (type === 'reimbursement') ? 'block' : 'none';
    }
}

function handleServiceTypeSelect(selectEl) {
    const textInput = document.getElementById('serviceTypeTextInput');
    if (!textInput) return;
    if (selectEl.value === 'custom') {
        textInput.value = '';
        textInput.placeholder = 'Enter custom service type notes...';
        textInput.focus();
    } else if (selectEl.value) {
        textInput.value = selectEl.value;
    }
}

window.filterPmsTable = function() {
    const inputEl = document.getElementById('pmsSearchInput');
    if (!inputEl) return;
    const input = inputEl.value.toLowerCase().trim();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const text = (row.textContent || row.innerText || '').toLowerCase();
        row.style.display = (!input || text.includes(input)) ? '' : 'none';
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('pmsSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterPmsTable();
            }
        });
        searchInput.addEventListener('input', filterPmsTable);
    }
});
</script>
@endsection
