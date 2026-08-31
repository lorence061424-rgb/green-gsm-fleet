@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge text-white px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px; background: #7F1D1D !important;">SUPERADMIN SECURITY CENTER</span>
            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-shield-lock-fill text-danger me-1"></i> Native ISO 25010 System Security</span>
        </div>
        <h2 class="page-header-title mt-1">Superadmin Security & User Access Control Center</h2>
        <p class="page-header-subtitle">Monitor brute-force rate-limiting, view live security incident feeds, manage RBAC role permissions, and perform one-click user unlocks.</p>
    </div>
    <div class="col-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-danger rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#quickUnlockModal" style="background: #CE2029 !important;">
            <i class="bi bi-unlock-fill me-1"></i> Unlock Account / IP
        </button>
        <form action="{{ route('admin.security.clear-logs') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear security audit logs?');">
            @csrf
            <button type="submit" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-trash me-1"></i> Clear Audit Logs
            </button>
        </form>
    </div>
</div>

<!-- Security Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 bg-success bg-opacity-10 text-success fw-medium" role="alert">
        <i class="bi bi-shield-check text-success fs-5 me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 bg-danger bg-opacity-10 text-danger fw-medium" role="alert">
        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Inter-System Security Status Banner -->
<div class="alert alert-dark bg-dark text-white border-0 rounded-4 p-3 mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-fill-check text-success fs-3 me-3"></i>
            <div>
                <span class="fw-bold d-block text-white small">NATIVE SECURITY DEFENSE SYSTEM ACTIVE</span>
                <span class="text-white-50 fw-medium" style="font-size: 11px;">Zero third-party apps required. Protected by RateLimiter brute-force blocks, anti-bot honeypot, HTTP security headers, and Bcrypt hashing.</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-success text-white fw-bold px-3 py-2"><i class="bi bi-check-circle me-1"></i> Rate Limiter: Active</span>
            <span class="badge bg-warning text-dark fw-bold px-3 py-2"><i class="bi bi-robot me-1"></i> Honeypot: Enabled</span>
            <span class="badge bg-info text-dark fw-bold px-3 py-2"><i class="bi bi-lock-fill me-1"></i> Headers: Enforced</span>
        </div>
    </div>
</div>

<!-- 4 Key Security Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-2">
                    <i class="bi bi-shield-exclamation fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Failed Login Strikes</span>
                    <small class="text-muted" style="font-size: 10px;">Password Failures</small>
                </div>
            </div>
            <h3 class="fw-bold text-warning mb-1">{{ number_format($totalFailedAttempts) }}</h3>
            <small class="text-muted" style="font-size: 11px;">Logged by RateLimiter</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-2">
                    <i class="bi bi-lock-fill fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Account Lockouts</span>
                    <small class="text-muted" style="font-size: 10px;">Brute-Force Triggers</small>
                </div>
            </div>
            <h3 class="fw-bold text-danger mb-1">{{ number_format($totalLockouts) }}</h3>
            <small class="text-muted" style="font-size: 11px;">Max 5 Strikes / 60s Block</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-dark bg-opacity-10 text-dark p-2 rounded-3 me-2">
                    <i class="bi bi-robot fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Bot Honeypot Traps</span>
                    <small class="text-muted" style="font-size: 10px;">Automated Scrapers</small>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ number_format($totalHoneypotBlocks) }}</h3>
            <small class="text-muted" style="font-size: 11px;">Silently Blocked</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card premium-card p-3 h-100 border-0 bg-white shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-2">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark small">Superadmin Unlocker</span>
                    <small class="text-muted" style="font-size: 10px;">Manual Bypass</small>
                </div>
            </div>
            <h3 class="fw-bold text-success mb-1">Ready</h3>
            <small class="text-muted" style="font-size: 11px;">1-Click Instant Lockout Reset</small>
        </div>
    </div>
</div>

<!-- Lockout Management & Live Audit Trail Table Container -->
<div class="row g-4 mb-4">
    <!-- User Roles & Restriction Summary Panel -->
    <div class="col-lg-4">
        <div class="card premium-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-danger me-2"></i> System Roles & RBAC Matrix</h5>
            <div class="list-group list-group-flush" style="font-size: 12.5px;">
                <div class="list-group-item px-0 py-2 border-0 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark"><i class="bi bi-shield-check text-danger me-1"></i> System Admin</strong>
                        <span class="badge bg-danger">Full Control</span>
                    </div>
                    <small class="text-muted d-block">Unrestricted access, user unlocks & security audit logs.</small>
                </div>
                <div class="list-group-item px-0 py-2 border-0 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark"><i class="bi bi-truck text-warning me-1"></i> Fleet Manager</strong>
                        <span class="badge bg-warning text-dark">Fleet & PMS</span>
                    </div>
                    <small class="text-muted d-block">Vehicle roster, driver assignment, and maintenance logs.</small>
                </div>
                <div class="list-group-item px-0 py-2 border-0 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark"><i class="bi bi-compass text-primary me-1"></i> Dispatcher</strong>
                        <span class="badge bg-primary">Dispatch</span>
                    </div>
                    <small class="text-muted d-block">Trip dispatches, route preview, and live GPS simulator.</small>
                </div>
                <div class="list-group-item px-0 py-2 border-0 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark"><i class="bi bi-cash-stack text-success me-1"></i> Finance Officer</strong>
                        <span class="badge bg-success">Financials</span>
                    </div>
                    <small class="text-muted d-block">Cost-Per-KM ledgers, fuel charging AP, and tariff savings.</small>
                </div>
                <div class="list-group-item px-0 py-2 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark"><i class="bi bi-speedometer2 text-info me-1"></i> Operations Manager</strong>
                        <span class="badge bg-info text-dark">Operations</span>
                    </div>
                    <small class="text-muted d-block">Terminal charging draw, driver safety ratings & operational queue.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Security Audit Log Table -->
    <div class="col-lg-8">
        <div class="card premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0"><i class="bi bi-journal-text text-danger me-2"></i> Security Incident Audit Log</h5>
                <span class="badge bg-dark text-white rounded-pill px-3 py-2">{{ $securityLogs->total() }} Total Incidents</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">
                            <th>TIMESTAMP</th>
                            <th>SECURITY EVENT</th>
                            <th>TARGET ACCOUNT / EMAIL</th>
                            <th>CLIENT IP</th>
                            <th>DETAILS & REASON</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($securityLogs as $log)
                            <tr>
                                <td style="font-size: 12px; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}
                                </td>
                                <td>
                                    @if($log->event_type === 'successful_login')
                                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i> Login Success</span>
                                    @elseif($log->event_type === 'failed_login')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-exclamation-triangle me-1"></i> Password Fail</span>
                                    @elseif($log->event_type === 'account_lockout')
                                        <span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-lock-fill me-1"></i> Account Lockout</span>
                                    @elseif($log->event_type === 'admin_unlock')
                                        <span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-unlock-fill me-1"></i> Admin Unlock</span>
                                    @elseif($log->event_type === 'bot_honeypot_blocked')
                                        <span class="badge bg-dark text-white rounded-pill px-3 py-1"><i class="bi bi-robot me-1"></i> Bot Trapped</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">{{ $log->event_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark small d-block">{{ $log->email ?: 'N/A' }}</strong>
                                </td>
                                <td>
                                    <code class="bg-light text-danger px-2 py-1 rounded small">{{ $log->ip_address ?: '127.0.0.1' }}</code>
                                </td>
                                <td style="font-size: 12px; max-width: 250px;">
                                    <span class="text-muted d-block text-truncate">{{ $log->details }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                                    No security incidents logged yet. All system authentication attempts are clean.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($securityLogs->hasPages())
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                    <small class="text-muted">Page {{ $securityLogs->currentPage() }} of {{ $securityLogs->lastPage() }}</small>
                    {{ $securityLogs->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Quick Unlock Account / Reset Rate Limiter -->
<div class="modal fade" id="quickUnlockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered rounded-4 overflow-hidden">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.security.unlock') }}" method="POST">
                @csrf
                <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #CE2029 0%, #7F1D1D 100%);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-unlock-fill me-2"></i> Superadmin Account Unlocker</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Enter the email address and/or IP address of the locked-out user to clear their rate-limiter strikes and immediately restore system access.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target User Email Address</label>
                        <select name="email" class="form-select rounded-3" required>
                            <option value="" disabled selected>-- Select User to Unlock --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->email }}">{{ $usr->name }} ({{ $usr->email }} &bull; Role: {{ ucfirst($usr->role ?? 'User') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Client IP Address (Optional)</label>
                        <input type="text" name="ip_address" value="127.0.0.1" class="form-control rounded-3" placeholder="e.g. 127.0.0.1">
                        <small class="text-muted">Defaults to 127.0.0.1 if left blank.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold px-4" style="background: #CE2029 !important;">
                        <i class="bi bi-unlock-fill me-1"></i> Unlock Account & Reset Rate Limit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
