@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px; background: #CE2029 !important;">HIRNA MOBILITY SOLUTIONS INC.</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-calendar-event text-danger me-1"></i> Vehicle Reservation and Dispatch System (VRDS)</span>
        </div>
        <h2 class="page-header-title mt-1">Vehicle Reservation and Dispatch</h2>
        <p class="page-header-subtitle">Schedule, approve, and track Hirna Vehicle vehicle bookings, driver assignments, and incoming employee trip requests.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-warning text-dark fw-bold rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#simulateIncomingVehicleModal">
            <i class="bi bi-plus-circle-dotted me-1"></i> Simulate Incoming Request
        </button>
        <div class="input-group" style="max-width: 320px;">
            <input type="text" id="reservationSearchInput" class="form-control rounded-start-3 border-secondary-subtle" placeholder="Search reservation, plate, driver..." onkeyup="filterReservationsTable()">
            <button class="btn btn-danger rounded-end-3 fw-bold" type="button" onclick="filterReservationsTable()" style="background: #CE2029 !important;">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
        <button class="btn btn-premium rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newReservationModal">
            <i class="bi bi-plus-circle me-1"></i> New Reservation
        </button>
    </div>
</div>

<!-- Flash Messages -->
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
            <i class="bi bi-diagram-3-fill text-success fs-4 me-2"></i>
            <div>
                <span class="fw-bold d-block text-white small">INTER-SYSTEM INTEGRATION PIPELINE (TEAM 7 &bull; VRDS)</span>
                <span class="text-white-50 fw-medium" style="font-size: 11px;">Connected to peer enterprise systems for incoming employee booking requests, shift rosters, and dispatch telemetry.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><i class="bi bi-inbox-fill me-1"></i> Incoming Booking Feed (ESS)</span>
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-clock-history me-1"></i> Team 2: HRMS Shift Rosters</span>
            <span class="badge bg-success text-white fw-bold px-3 py-2"><i class="bi bi-building-check me-1"></i> Team 8: Facilities Bookings</span>
        </div>
    </div>
</div>

<!-- Navigation Tabs: Calendar & Bookings vs. Incoming Requests Feed -->
<ul class="nav nav-pills mb-4 p-1 bg-white rounded-4 shadow-sm border border-secondary-subtle" style="max-width: 600px;">
    <li class="nav-item flex-fill text-center">
        <a class="nav-link rounded-3 fw-bold {{ ($tab ?? 'calendar') === 'calendar' ? 'active bg-danger' : 'text-dark' }}" 
           href="{{ route('reservations.index', ['tab' => 'calendar']) }}"
           style="{{ ($tab ?? 'calendar') === 'calendar' ? 'background: #CE2029 !important;' : '' }}">
            <i class="bi bi-calendar3 me-1"></i> Dispatch Calendar & Bookings
            <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $reservations->count() }}</span>
        </a>
    </li>
    <li class="nav-item flex-fill text-center">
        <a class="nav-link rounded-3 fw-bold {{ ($tab ?? 'calendar') === 'incoming' ? 'active bg-danger' : 'text-dark' }}" 
           href="{{ route('reservations.index', ['tab' => 'incoming']) }}"
           style="{{ ($tab ?? 'calendar') === 'incoming' ? 'background: #CE2029 !important;' : '' }}">
            <i class="bi bi-inbox-fill me-1"></i> Incoming Requests
            @if(($pendingIncomingCount ?? 0) > 0)
                <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $pendingIncomingCount }} New</span>
            @endif
        </a>
    </li>
</ul>

@if(($tab ?? 'calendar') === 'incoming')
    <!-- ============================================================ -->
    <!-- TAB: INCOMING VEHICLE BOOKING REQUESTS (ESS FEED)            -->
    <!-- ============================================================ -->
    <div class="card premium-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-inbox-fill text-danger me-2"></i> Incoming Employee Vehicle Requests (ESS Feed)</h5>
                <small class="text-muted">Review booking requests submitted by company staff for meetings, site visits, and airport transfers. Approve to automatically assign a vehicle and schedule the dispatch.</small>
            </div>
            <span class="badge bg-danger text-white rounded-pill px-3 py-2" style="background: #CE2029 !important;">{{ $incomingRequests->count() }} Total Requests</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>#ID</th>
                        <th>REQUESTOR</th>
                        <th>PURPOSE & DETAILS</th>
                        <th>DESTINATION</th>
                        <th>DATE & TIME</th>
                        <th>PAX</th>
                        <th>PREFERENCE</th>
                        <th>URGENCY</th>
                        <th>STATUS</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingRequests as $req)
                        @php
                            $purposeIcons = ['meeting'=>'bi-briefcase','field_visit'=>'bi-map','airport'=>'bi-airplane','inter_branch'=>'bi-building','errand'=>'bi-bag','emergency'=>'bi-exclamation-triangle'];
                            $icon = $purposeIcons[$req->purpose_type] ?? 'bi-car-front';
                            $urgencyBadgeMap = [
                                'priority' => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'border' => '#F87171', 'label' => 'Priority', 'icon' => 'bi-exclamation-octagon-fill'],
                                'routine'  => ['bg' => '#ECFDF5', 'text' => '#065F46', 'border' => '#10B981', 'label' => 'Routine', 'icon' => 'bi-check-circle-fill'],
                            ];
                            $statusBadgeMap = [
                                'pending'   => ['bg' => '#FEF3C7', 'text' => '#92400E', 'border' => '#F59E0B', 'icon' => 'bi-hourglass-split', 'label' => 'Pending Review'],
                                'approved'  => ['bg' => '#DCFCE7', 'text' => '#166534', 'border' => '#22C55E', 'icon' => 'bi-check-circle-fill', 'label' => 'Approved & Dispatched'],
                                'rejected'  => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'border' => '#EF4444', 'icon' => 'bi-x-circle-fill', 'label' => 'Rejected'],
                                'completed' => ['bg' => '#F1F5F9', 'text' => '#334155', 'border' => '#94A3B8', 'icon' => 'bi-flag-fill', 'label' => 'Completed'],
                                'cancelled' => ['bg' => '#F1F5F9', 'text' => '#64748B', 'border' => '#CBD5E1', 'icon' => 'bi-slash-circle', 'label' => 'Cancelled'],
                            ];
                            $uStyle = $urgencyBadgeMap[$req->urgency_level] ?? $urgencyBadgeMap['routine'];
                            $sStyle = $statusBadgeMap[$req->status] ?? $statusBadgeMap['pending'];
                        @endphp
                        <tr>
                            <td><span class="fw-bold text-danger">#{{ $req->id }}</span></td>
                            <td>
                                <span class="fw-semibold d-block text-dark">{{ $req->employee->name ?? 'Hirna Employee' }}</span>
                                <small class="text-muted">{{ ucfirst($req->employee->role ?? 'Staff') }}</small>
                            </td>
                            <td>
                                <span class="badge px-2 py-1 rounded-pill mb-1 fw-bold" style="background: #EFF6FF; color: #1E40AF; border: 1px solid #3B82F6;">
                                    <i class="bi {{ $icon }} me-1"></i>{{ $req->purpose_type_label }}
                                </span>
                                <div class="text-truncate text-dark fw-medium" style="max-width: 220px;" title="{{ $req->purpose_description }}">{{ $req->purpose_description }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $req->destination }}</span>
                                <small class="d-block text-muted">{{ $req->is_roundtrip ? '↩ Round Trip' : '→ One Way' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ optional($req->reservation_date)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $req->start_time }} - {{ $req->end_time }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">{{ $req->num_passengers }} pax</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark">{{ $req->vehicle_preference ?: 'Any Vehicle' }}</span>
                            </td>
                            <td>
                                <span class="badge px-2 py-1 rounded-pill fw-bold" style="background: {{ $uStyle['bg'] }}; color: {{ $uStyle['text'] }}; border: 1px solid {{ $uStyle['border'] }}; font-size: 11px;">
                                    <i class="bi {{ $uStyle['icon'] }} me-1"></i> {{ $uStyle['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: {{ $sStyle['bg'] }}; color: {{ $sStyle['text'] }}; border: 1px solid {{ $sStyle['border'] }}; font-size: 11.5px;">
                                    <i class="bi {{ $sStyle['icon'] }} me-1"></i> {{ $sStyle['label'] }}
                                </span>
                                @if($req->reservation_id)
                                    <small class="d-block text-success fw-bold mt-1" style="font-size: 10px;">
                                        <i class="bi bi-calendar-check me-1"></i>Booking #{{ $req->reservation_id }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($req->status === 'pending')
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-success rounded-3 px-2 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#approveVehicleModal{{ $req->id }}">
                                            <i class="bi bi-check-lg me-1"></i> Assign & Dispatch
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectVehicleModal{{ $req->id }}">
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
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No incoming vehicle booking requests in the queue.<br>
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
    <!-- TAB: CALENDAR & EXISTING RESERVATIONS (STANDARD VIEW)        -->
    <!-- ============================================================ -->
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card premium-card p-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Total Booking Requests</span>
                        <h3 class="fw-bold my-1">{{ $reservations->count() }}</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4 fs-4">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card p-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Pending Approval</span>
                        <h3 class="fw-bold my-1 text-warning">{{ $reservations->where('status', 'pending')->count() }}</h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-4 fs-4">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card p-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Approved & Active</span>
                        <h3 class="fw-bold my-1 text-success">{{ $reservations->where('status', 'approved')->count() }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4 fs-4">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card p-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Available Vehicles Today</span>
                        <h3 class="fw-bold my-1 text-info">{{ $vehicles->count() }}</h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-4 fs-4">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP SECTION: Full-Width Visual Vehicle Schedule Calendar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card premium-card border-0 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-calendar3 text-success me-2"></i> Hirna Vehicle Schedule Calendar</h5>
                        <p class="small text-muted mb-0">Click any date to inspect reserved and available Hirna cars in real time.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 fs-6">
                        <i class="bi bi-broadcast me-1"></i> Live VRDS Schedule Sync
                    </span>
                </div>
                
                <div id="reservationCalendar" style="min-height: 420px;"></div>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION: 2-Column Split (Reservations Table + Availability Lookup) -->
    <div class="row g-4">
        <!-- Reservations List Table -->
        <div class="col-lg-8">
            <div class="card premium-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Reservation Records</h5>
                    <span class="badge bg-light text-dark border">VRDS Real-Time Schedule</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Vehicle</th>
                                <th>Purpose</th>
                                <th>Date & Time</th>
                                <th>Assigned Driver</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $res)
                            <tr>
                                <td>
                                    <div>
                                        <strong class="d-block text-dark">{{ $res->vehicle->license_plate ?? 'N/A' }}</strong>
                                        <small class="text-muted">{{ $res->vehicle->make ?? '' }} {{ $res->vehicle->model ?? '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $res->purpose }}</span>
                                    @if($res->remarks)
                                    <br><small class="text-muted">{{ Str::limit($res->remarks, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="bi bi-calendar3 me-1 text-primary"></i> {{ \Carbon\Carbon::parse($res->reservation_date)->format('M d, Y') }}
                                        <br>
                                        <i class="bi bi-clock me-1 text-muted"></i> {{ $res->start_time }} - {{ $res->end_time }}
                                    </div>
                                </td>
                                <td>
                                    @if($res->driver)
                                        <span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i> {{ $res->driver->user->name ?? 'Driver #'.$res->driver_id }}</span>
                                    @else
                                        <span class="text-muted small">Auto-Assign on Dispatch</span>
                                    @endif
                                </td>
                                <td>
                                    @if($res->status == 'approved')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Approved</span>
                                    @elseif($res->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                    @elseif($res->status == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                    @elseif($res->status == 'completed')
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"><i class="bi bi-flag me-1"></i> Completed</span>
                                    @else
                                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">{{ ucfirst($res->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                     @if($res->status == 'pending')
                                         <form action="{{ route('reservations.update-status', $res->id) }}" method="POST" class="d-inline">
                                             @csrf
                                             <input type="hidden" name="status" value="approved">
                                             <button type="submit" class="btn btn-sm btn-outline-success rounded-3 me-1">
                                                 <i class="bi bi-check-lg"></i> Approve
                                             </button>
                                         </form>
                                         <form action="{{ route('reservations.update-status', $res->id) }}" method="POST" class="d-inline">
                                             @csrf
                                             <input type="hidden" name="status" value="rejected">
                                             <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                 <i class="bi bi-x-lg"></i> Reject
                                             </button>
                                         </form>
                                      @elseif($res->status == 'approved')
                                      <form action="{{ route('reservations.update-status', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm marking Reservation #{{ $res->id }} as Completed?');">
                                          @csrf
                                          <input type="hidden" name="status" value="completed">
                                          <button type="submit" class="btn btn-sm btn-outline-secondary rounded-3">
                                              Mark Completed
                                          </button>
                                      </form>
                                      @else
                                      <span class="text-muted small">No actions</span>
                                      @endif
                                 </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No vehicle reservations submitted yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Real-Time Availability Check Panel -->
        <div class="col-lg-4">
            <div class="card premium-card border-0 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-subtle text-primary p-2 rounded-3 me-2">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Check Availability by Date</h6>
                        <small class="text-muted">Instant slot inspector</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Select Inspection Date</label>
                    <div class="input-group">
                        <input type="date" id="checkDateInput" class="form-control rounded-start-3" value="{{ date('Y-m-d') }}">
                        <button class="btn btn-primary rounded-end-3" type="button" id="checkAvailBtn" onclick="checkAvailability()">
                            <i class="bi bi-search me-1"></i> Check
                        </button>
                    </div>
                </div>

                <div id="availabilityResults" class="p-3 bg-light rounded-4 border border-secondary-subtle">
                    <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-check-circle-fill text-success me-1"></i> Available Fleet Vehicles:</h6>
                    <div id="availableVehiclesList" class="mb-3">
                        @foreach($vehicles as $v)
                            <div class="badge bg-success text-white px-3 py-2 me-1 mb-1 rounded-3 shadow-sm d-inline-flex align-items-center">
                                <i class="bi bi-ev-front-fill me-1"></i> {{ $v->license_plate }} ({{ $v->make }} {{ $v->model }})
                            </div>
                        @endforeach
                    </div>

                    <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-x-circle-fill text-danger me-1"></i> Reserved / Busy Vehicles:</h6>
                    <div id="reservedVehiclesList">
                        <span class="text-muted small fw-semibold">No vehicles reserved on this date.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ============================================================ -->
<!-- MODAL: CREATE MANUAL RESERVATION                             -->
<!-- ============================================================ -->
<div class="modal fade" id="newReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-danger text-white p-4" style="background: linear-gradient(135deg, #CE2029 0%, #7F1D1D 100%) !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                        <i class="bi bi-calendar-plus fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">New Vehicle Reservation</h5>
                        <small class="text-white-50">Create an official vehicle booking for trip dispatch.</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select rounded-3" required>
                                <option value="">-- Choose Vehicle --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->license_plate }} - {{ $v->make }} {{ $v->model }} ({{ $v->type ?? 'Sedan' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Assign Driver (Optional)</label>
                            <select name="driver_id" class="form-select rounded-3">
                                <option value="">-- Auto-assign / Self-driven --</option>
                                @foreach($drivers as $d)
                                    @if($d->user)
                                        <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Purpose of Travel <span class="text-danger">*</span></label>
                            <select id="purposeCategorySelect" class="form-select rounded-3 mb-2" required onchange="handlePurposeCategorySelect(this)">
                                <option value="">-- Choose Purpose Category --</option>
                                <option value="💼 Executive &amp; Client Meeting">💼 Executive &amp; Client Meeting</option>
                                <option value="🗺️ Regional Field Inspection &amp; Site Audit">🗺️ Regional Field Inspection &amp; Site Audit</option>
                                <option value="✈️ Airport Reception &amp; Guest Transfer (NAIA)">✈️ Airport Reception &amp; Guest Transfer (NAIA)</option>
                                <option value="🏢 Inter-Branch Operations &amp; Facility Transit">🏢 Inter-Branch Operations &amp; Facility Transit</option>
                                <option value="🛍️ Official Logistics &amp; Documents Errand">🛍️ Official Logistics &amp; Documents Errand</option>
                                <option value="🚨 Emergency Business Transport">🚨 Emergency Business Transport</option>
                                <option value="custom">✏️ Other / Custom Purpose...</option>
                            </select>
                            <input type="text" name="purpose" id="purposeTextInput" class="form-control rounded-3" placeholder="Specify destination or trip details (e.g., Client Meeting at Shangri-La Makati)..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Reservation Date <span class="text-danger">*</span></label>
                            <input type="date" name="reservation_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control rounded-3" value="08:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control rounded-3" value="17:00" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Additional Remarks</label>
                            <textarea name="remarks" rows="2" class="form-control rounded-3" placeholder="Special requirements, passenger count, destinations..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold rounded-3" style="background: #CE2029 !important;">
                        <i class="bi bi-check-circle me-1"></i> Submit Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: SIMULATE INCOMING VEHICLE REQUEST (FOR DEMO)          -->
<!-- ============================================================ -->
<div class="modal fade" id="simulateIncomingVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 bg-warning text-dark p-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-inbox-fill fs-3 me-2"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Simulate Incoming Employee Vehicle Request</h5>
                        <small class="text-muted">Simulate an employee requesting a vehicle for business travel via ESS.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reservations.incoming.sample') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Purpose Type</label>
                            <select name="purpose_type" class="form-select rounded-3" required>
                                <option value="meeting" selected>💼 Executive / Client Meeting</option>
                                <option value="field_visit">🗺️ Regional Field Inspection</option>
                                <option value="airport">✈️ Airport Reception / Transfer</option>
                                <option value="inter_branch">🏢 Inter-Branch Operations</option>
                                <option value="emergency">🚨 Emergency Transport</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Urgency Level</label>
                            <select name="urgency_level" class="form-select rounded-3" required>
                                <option value="routine">🟢 Routine</option>
                                <option value="priority" selected>🔴 Priority</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Trip Details</label>
                            <input type="text" name="purpose_description" class="form-control rounded-3" value="Client presentation and commercial contract signing with regional enterprise partners." required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Destination</label>
                            <input type="text" name="destination" class="form-control rounded-3" value="Grand Hyatt Hotel, 8th Avenue, BGC Taguig" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">No. of Passengers</label>
                            <input type="number" name="num_passengers" class="form-control rounded-3" value="3" min="1" max="30" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Date</label>
                            <input type="date" name="reservation_date" class="form-control rounded-3" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Start Time</label>
                            <input type="time" name="start_time" class="form-control rounded-3" value="09:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">End Time</label>
                            <input type="time" name="end_time" class="form-control rounded-3" value="14:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Vehicle Preference</label>
                            <select name="vehicle_preference" class="form-select rounded-3">
                                <option value="SUV" selected>SUV</option>
                                <option value="Van">Van / Hi-Ace</option>
                                <option value="Sedan">Sedan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-3">
                        <i class="bi bi-send-fill me-1"></i> Send Request to Dispatch Queue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- APPROVE & REJECT MODALS FOR INCOMING VEHICLE REQUESTS        -->
<!-- ============================================================ -->
@if(isset($incomingRequests))
    @foreach($incomingRequests->where('status', 'pending') as $req)
        <!-- Approve Modal -->
        <div class="modal fade" id="approveVehicleModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-0 bg-success text-white p-3">
                        <h6 class="modal-title fw-bold"><i class="bi bi-check-circle me-1"></i> Approve & Assign Dispatch — Request #{{ $req->id }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reservations.incoming.approve', $req) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-3 mb-3 small">
                                <strong>Requestor:</strong> {{ $req->employee->name ?? 'Employee' }} | 
                                <strong>Purpose:</strong> {{ $req->purpose_description }} | 
                                <strong>Date:</strong> {{ optional($req->reservation_date)->format('M d, Y') }} ({{ $req->start_time }} - {{ $req->end_time }}) | 
                                <strong>Destination:</strong> {{ $req->destination }}
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Assign Vehicle <span class="text-danger">*</span></label>
                                    <select name="assigned_vehicle_id" class="form-select rounded-3" required>
                                        <option value="">-- Select Available Vehicle --</option>
                                        @foreach($vehicles as $v)
                                            <option value="{{ $v->id }}">{{ $v->license_plate }} - {{ $v->make }} {{ $v->model }} ({{ $v->type ?? 'Vehicle' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Assign Driver</label>
                                    <select name="assigned_driver_id" class="form-select rounded-3">
                                        <option value="">-- Auto-assign on trip start --</option>
                                        @foreach($drivers as $d)
                                            @if($d->user)
                                                <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Dispatch Notes / Remarks</label>
                                    <textarea name="remarks" rows="2" class="form-control rounded-3" placeholder="Special dispatch instructions..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3 bg-light">
                            <button type="button" class="btn btn-outline-secondary rounded-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-bold rounded-3 btn-sm">
                                <i class="bi bi-calendar-check me-1"></i> Approve & Add to Calendar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectVehicleModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-0 bg-danger text-white p-3">
                        <h6 class="modal-title fw-bold"><i class="bi bi-x-circle me-1"></i> Reject Request #{{ $req->id }}</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reservations.incoming.reject', $req) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <label class="form-label fw-bold small">Reason for Rejection</label>
                            <textarea name="rejection_reason" rows="3" class="form-control rounded-3" placeholder="e.g. No vehicles available at requested time slot. Please reschedule." required></textarea>
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

@endsection

@section('scripts')
<script>
function checkAvailability() {
    const dateInput = document.getElementById('checkDateInput');
    const btn = document.getElementById('checkAvailBtn');
    if (!dateInput) return;
    const selectedDate = dateInput.value;
    if (!selectedDate) {
        alert('Please pick a date first.');
        return;
    }

    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';
    }

    fetch(`{{ route('reservations.check-availability') }}?date=${selectedDate}`)
        .then(res => res.json())
        .then(data => {
            const availList = document.getElementById('availableVehiclesList');
            const resList = document.getElementById('reservedVehiclesList');
            if (availList) availList.innerHTML = '';
            if (resList) resList.innerHTML = '';

            if (!data.available || data.available.length === 0) {
                if (availList) availList.innerHTML = '<span class="text-muted small fw-semibold">No vehicles available on this date.</span>';
            } else {
                data.available.forEach(v => {
                    if (availList) {
                        availList.innerHTML += `<div class="badge bg-success text-white px-3 py-2 me-1 mb-1 rounded-3 shadow-sm d-inline-flex align-items-center"><i class="bi bi-ev-front-fill me-1"></i> ${v.license_plate} (${v.make} ${v.model})</div> `;
                    }
                });
            }

            if (!data.reserved || data.reserved.length === 0) {
                if (resList) resList.innerHTML = '<span class="text-muted small fw-semibold">No vehicles reserved on this date.</span>';
            } else {
                data.reserved.forEach(v => {
                    if (resList) {
                        resList.innerHTML += `<div class="badge bg-danger text-white px-3 py-2 me-1 mb-1 rounded-3 shadow-sm d-inline-flex align-items-center"><i class="bi bi-x-circle-fill me-1"></i> ${v.license_plate} (${v.make} ${v.model})</div> `;
                    }
                });
            }

            if (window.reservationCalendarInstance && data.date) {
                window.reservationCalendarInstance.gotoDate(data.date);
            }
        })
        .catch(err => {
            console.error('Availability check failed:', err);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
}

function handlePurposeCategorySelect(selectEl) {
    const textInput = document.getElementById('purposeTextInput');
    if (!textInput) return;
    if (selectEl.value === 'custom') {
        textInput.value = '';
        textInput.placeholder = 'Enter custom purpose of travel...';
        textInput.focus();
    } else if (selectEl.value) {
        textInput.value = selectEl.value;
    }
}

window.filterReservationsTable = function() {
    const inputEl = document.getElementById('reservationSearchInput');
    if (!inputEl) return;
    const input = inputEl.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
};

function initScheduleCalendar() {
    const calendarEl = document.getElementById('reservationCalendar');
    if (calendarEl && typeof FullCalendar !== 'undefined') {
        if (window.reservationCalendarInstance) {
            try { window.reservationCalendarInstance.destroy(); } catch(e) {}
            window.reservationCalendarInstance = null;
        }

        const eventsData = @json($calendarEvents ?? []);

        window.reservationCalendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            themeSystem: 'bootstrap5',
            events: eventsData,
            dateClick: function(info) {
                const checkInput = document.getElementById('checkDateInput');
                if (checkInput) {
                    checkInput.value = info.dateStr;
                    checkAvailability();
                }
            }
        });
        window.reservationCalendarInstance.render();
    }
}
window.initScheduleCalendar = initScheduleCalendar;

document.addEventListener('DOMContentLoaded', function() {
    initScheduleCalendar();
});
</script>
@endsection
