@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="page-header-title">Preventive Maintenance Services (PMS)</h2>
        <p class="page-header-subtitle">Schedule engine tune-ups, log service expenses, and monitor vehicle health status.</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-premium d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#schedulePMSModal">
            <i class="bi bi-calendar-plus me-1"></i> Schedule Maintenance
        </button>
    </div>
</div>

<!-- Scheduled and Log PMS grid -->
<div class="card premium-card p-4">
    <h5 class="fw-bold mb-4"><i class="bi bi-wrench-adjustable text-primary me-2"></i> PMS Logs & Service Schedule</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-muted" style="font-size: 13px;">
                    <th>VEHICLE</th>
                    <th>SERVICE TYPE</th>
                    <th>DESCRIPTION</th>
                    <th>COST</th>
                    <th>SCHEDULED DATE</th>
                    <th>COMPLETION DATE</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $record->vehicle->make }} {{ $record->vehicle->model }}</span>
                            <small class="badge bg-secondary ms-1">{{ $record->vehicle->license_plate }}</small>
                        </td>
                        <td class="fw-bold text-dark">{{ $record->service_type }}</td>
                        <td style="font-size: 13px; max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            {{ $record->description ?: 'No details provided' }}
                        </td>
                        <td class="fw-bold">₱{{ number_format($record->cost, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->scheduled_date)->toFormattedDateString() }}</td>
                        <td>
                            {{ $record->completion_date ? \Carbon\Carbon::parse($record->completion_date)->toFormattedDateString() : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $record->status === 'completed' ? 'bg-success' : ($record->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($record->status !== 'completed')
                                <button class="btn btn-sm btn-outline-primary rounded-2 px-2" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $record->id }}">
                                    <i class="bi bi-arrow-left-right me-1"></i> Update Status
                                </button>
                            @else
                                <span class="text-muted small"><i class="bi bi-check2-all text-success me-1"></i> Finalized</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Update Status Modal -->
                    <div class="modal fade" id="updateStatusModal{{ $record->id }}" tabindex="-1" aria-labelledby="updateStatusLabel{{ $record->id }}" aria-hidden="true">
                        <div class="modal-dialog rounded-4 overflow-hidden">
                            <div class="modal-content border-0">
                                <form action="{{ route('maintenance.update-status', $record) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-secondary text-white border-0">
                                        <h5 class="modal-title fw-bold" id="updateStatusLabel{{ $record->id }}">Update PMS Status</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label" style="font-weight: 500;">Status</label>
                                            <select name="status" class="form-select rounded-3" id="statusSelect{{ $record->id }}" onchange="toggleCompletionDate({{ $record->id }});" required>
                                                <option value="scheduled" {{ $record->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                <option value="in_progress" {{ $record->status === 'in_progress' ? 'selected' : '' }}>In Progress (Vehicle Offline)</option>
                                                <option value="completed" {{ $record->status === 'completed' ? 'selected' : '' }}>Completed (Vehicle Released)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-weight: 500;">Final Cost (₱)</label>
                                            <input type="number" step="0.01" name="cost" value="{{ $record->cost }}" class="form-control rounded-3" required>
                                        </div>
                                        <div class="mb-3 d-none" id="completionDateDiv{{ $record->id }}">
                                            <label class="form-label" style="font-weight: 500;">Completion Date</label>
                                            <input type="date" name="completion_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-3 bg-light">
                                        <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-premium rounded-3">Save Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No preventive maintenance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Schedule PMS Modal -->
<div class="modal fade" id="schedulePMSModal" tabindex="-1" aria-labelledby="schedulePMSLabel" aria-hidden="true">
    <div class="modal-dialog rounded-4 overflow-hidden">
        <div class="modal-content border-0">
            <form action="{{ route('maintenance.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="schedulePMSLabel">Schedule Preventive Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Select Fleet Vehicle</label>
                            <select name="vehicle_id" class="form-select rounded-3" required>
                                <option value="" disabled selected>-- Choose Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->license_plate }} - Status: {{ $vehicle->status }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Service Type</label>
                            <input type="text" name="service_type" placeholder="e.g. Engine tune up, oil change, brake alignment" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Scheduled Date</label>
                            <input type="date" name="scheduled_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">Estimated Cost (₱)</label>
                            <input type="number" step="0.01" name="cost" placeholder="e.g. 2500" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Initial Booking Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="scheduled" selected>Scheduled</option>
                                <option value="in_progress">In Progress (Brings vehicle offline)</option>
                                <option value="completed">Completed (Brings vehicle online)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 500;">Service Details/Description</label>
                            <textarea name="description" rows="3" placeholder="Describe symptoms or replace part serial codes..." class="form-control rounded-3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium rounded-3">Schedule Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCompletionDate(recordId) {
        const select = document.getElementById('statusSelect' + recordId);
        const div = document.getElementById('completionDateDiv' + recordId);
        const dateInput = div.querySelector('input');
        
        if (select.value === 'completed') {
            div.classList.remove('d-none');
            dateInput.setAttribute('required', 'required');
        } else {
            div.classList.add('d-none');
            dateInput.removeAttribute('required');
        }
    }
</script>
@endsection
