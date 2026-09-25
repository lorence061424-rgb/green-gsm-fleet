<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Models\SecurityLogArchive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SecurityController extends Controller
{
    /**
     * Auto-heal database schema to ensure status column exists in users table.
     */
    protected function ensureStatusColumnExists(): void
    {
        try {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'status')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('status')->default('active')->nullable()->after('role');
                });
            }
        } catch (\Throwable $e) {
            Log::warning("Could not auto-create status column: " . $e->getMessage());
        }
    }

    /**
     * Auto-heal database schema to ensure security_log_archives table exists.
     */
    protected function ensureArchiveTableExists(): void
    {
        try {
            if (!Schema::hasTable('security_log_archives')) {
                Schema::create('security_log_archives', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('original_log_id')->nullable();
                    $table->string('event_type');
                    $table->string('email')->nullable();
                    $table->string('ip_address')->nullable();
                    $table->string('user_agent')->nullable();
                    $table->text('details')->nullable();
                    $table->timestamp('original_created_at')->nullable();
                    $table->timestamp('archived_at')->useCurrent();
                    $table->string('archived_by')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::warning("Could not auto-create security_log_archives table: " . $e->getMessage());
        }
    }

    /**
     * Display the Superadmin Security & User Access Control Center.
     */
    public function index()
    {
        $this->ensureStatusColumnExists();
        $this->ensureArchiveTableExists();

        $securityLogs = SecurityLog::latest()->paginate(15);
        $archivedLogs = SecurityLogArchive::latest()->paginate(15, ['*'], 'archived_page');
        $totalArchivedCount = SecurityLogArchive::count();

        $dbUsers = User::all();
        
        // Hirna Mobility official system role accounts
        $defaultUsers = collect([
            (object)['id' => 1, 'name' => 'Hirna System Admin', 'email' => 'hirna admin', 'job_title' => 'Chief Technology Officer & Admin', 'role' => 'admin', 'status' => 'active', 'avatar_url' => 'https://ui-avatars.com/api/?name=Hirna+System+Admin&background=CE2029&color=fff&size=128'],
            (object)['id' => 2, 'name' => 'Alex Fleet Manager', 'email' => 'hirna fleet', 'job_title' => 'Head of Fleet Operations', 'role' => 'fleet_manager', 'status' => 'active', 'avatar_url' => 'https://ui-avatars.com/api/?name=Alex+Fleet+Manager&background=F59E0B&color=fff&size=128'],
            (object)['id' => 3, 'name' => 'Sarah Dispatcher', 'email' => 'hirna dispatcher', 'job_title' => 'Lead Telematics Dispatcher', 'role' => 'dispatcher', 'status' => 'active', 'avatar_url' => 'https://ui-avatars.com/api/?name=Sarah+Dispatcher&background=10B981&color=fff&size=128'],
            (object)['id' => 4, 'name' => 'Marcus Finance Officer', 'email' => 'hirna finance', 'job_title' => 'Senior Financial Controller', 'role' => 'finance', 'status' => 'active', 'avatar_url' => 'https://ui-avatars.com/api/?name=Marcus+Finance+Officer&background=3B82F6&color=fff&size=128'],
            (object)['id' => 5, 'name' => 'Elena Operations Manager', 'email' => 'hirna operations', 'job_title' => 'Depot & Charging Operations Director', 'role' => 'operations', 'status' => 'active', 'avatar_url' => 'https://ui-avatars.com/api/?name=Elena+Operations+Manager&background=8B5CF6&color=fff&size=128'],
        ]);

        $allUsers = $dbUsers->concat($defaultUsers)->unique('email');

        // Pre-fetch latest security logs by email in bulk to avoid N+1 DB queries
        $latestLogsByEmail = SecurityLog::select('email', 'event_type', 'created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('email')
            ->keyBy('email');

        // Dynamically evaluate lockout status for each user account
        $users = $allUsers->map(function ($usr) use ($latestLogsByEmail) {
            $email = Str::lower($usr->email);
            $cleanInput = str_replace([' ', '_', '-', '@', '.'], '', $email);

            $keyUser = Str::transliterate("login_lockout:user_{$usr->id}");
            $keyInput = Str::transliterate("login_lockout:input_{$cleanInput}");

            $attemptsUser = RateLimiter::attempts($keyUser);
            $attemptsInput = RateLimiter::attempts($keyInput);

            $maxAttempts = max($attemptsUser, $attemptsInput);
            $isLocked = RateLimiter::tooManyAttempts($keyUser, 3) || RateLimiter::tooManyAttempts($keyInput, 3);

            // Check latest audit log for un-cleared lockout from bulk pre-fetched logs
            $lastLog = $latestLogsByEmail->get($email);
            if ($lastLog && $lastLog->event_type === 'account_lockout') {
                $isLocked = true;
                $maxAttempts = 3;
            } elseif ($lastLog && in_array($lastLog->event_type, ['admin_unlock', 'successful_login'])) {
                $isLocked = false;
                $maxAttempts = 0;
            }

            $usr->is_locked = $isLocked;
            $usr->attempts_count = $maxAttempts;
            return $usr;
        });

        $lockedUsersCount = $users->where('is_locked', true)->count();
        $totalFailedAttempts = SecurityLog::where('event_type', 'failed_login')->count();
        $totalLockouts = SecurityLog::where('event_type', 'account_lockout')->count();
        $totalHoneypotBlocks = SecurityLog::where('event_type', 'bot_honeypot_blocked')->count();
        $recentLockouts = SecurityLog::where('event_type', 'account_lockout')->latest()->take(10)->get();

        return view('admin.security', compact(
            'securityLogs',
            'archivedLogs',
            'totalArchivedCount',
            'users',
            'lockedUsersCount',
            'totalFailedAttempts',
            'totalLockouts',
            'totalHoneypotBlocks',
            'recentLockouts'
        ));
    }

    /**
     * Create new user account from Superadmin Security Console.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:50',
            'email' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_.\-@]+$/',
                'unique:users,email',
            ],
            'job_title' => 'nullable|string|max:100',
            'phone_number' => [
                'required',
                'string',
                'regex:/^09\d{9}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&~^()_+\-=\[\]{};\':"\\\\|,.<>\/?]/',
            ],
            'role' => 'required|string|in:admin,fleet_manager,dispatcher,finance,operations,driver',
        ], [
            'name.required' => 'Full Name is required.',
            'name.min' => 'Full Name must be at least 2 characters long.',
            'name.max' => 'Full Name must not exceed 50 characters.',
            'email.required' => 'Username / Email Address is required.',
            'email.min' => 'Username / Email Address must be at least 3 characters long.',
            'email.max' => 'Username / Email Address must not exceed 30 characters.',
            'email.regex' => 'Username / Email Address can only contain letters, numbers, underscores (_), periods (.), hyphens (-), and @ without spaces or invalid characters.',
            'email.unique' => 'This email address or username is already registered.',
            'phone_number.required' => 'Phone Number is required.',
            'phone_number.regex' => 'Phone Number must be numbers only, exactly 11 digits in Philippine mobile format starting with 09 (e.g. 09171234567).',
            'password.required' => 'Initial Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password must not exceed 100 characters.',
            'password.regex' => 'Password fails complexity rules: Must include 1 Uppercase (A-Z), 1 Lowercase (a-z), 1 Number (0-9), and 1 Special Character.',
            'role.required' => 'Please select a System Role & Permissions.',
            'role.in' => 'Selected system role is invalid.',
        ]);

        $this->ensureStatusColumnExists();

        $user = User::create([
            'name' => ucwords(Str::lower(trim($validated['name']))),
            'email' => trim(Str::lower($validated['email'])),
            'job_title' => $validated['job_title'] ? trim($validated['job_title']) : null,
            'phone_number' => trim($validated['phone_number']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        SecurityLog::create([
            'event_type' => 'admin_create_user',
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => "Superadmin (" . session('user_email', 'admin@hirna.ph') . ") created new account: {$user->name} ({$user->role})",
        ]);

        Log::info("SECURITY AUDIT: Superadmin created user {$user->email} ({$user->role})");

        return redirect()->back()->with('success', "👤 User Account '{$user->name}' ({$user->email}) created successfully with role '" . ucfirst($user->role) . "'.");
    }

    /**
     * One-Click Superadmin Account & IP Unlock feature.
     * Instantly resets RateLimiter brute-force counters for any specified email or IP address.
     */
    public function unlockUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'ip_address' => 'nullable|string',
        ]);

        $email = trim(Str::lower($request->email));
        $cleanInput = str_replace([' ', '_', '-', '@', '.'], '', $email);
        $clientIp = trim($request->ip_address ?? $request->ip());

        $usr = User::where('email', $email)->orWhereRaw("LOWER(REPLACE(email, ' ', '')) = ?", [$cleanInput])->first();

        // Clear all rate limiter key variations for guaranteed lockout removal
        $keysToClear = [
            Str::transliterate("login_lockout:input_{$cleanInput}"),
            Str::transliterate("login_lockout:input_{$cleanInput}|" . $clientIp),
            Str::transliterate("login_lockout:input_{$cleanInput}|127.0.0.1"),
            Str::transliterate("login_lockout:input_{$cleanInput}|" . $request->ip()),
            Str::transliterate($email . '|' . $clientIp),
            Str::transliterate($email . '|127.0.0.1'),
            Str::transliterate($email . '|' . $request->ip()),
            Str::transliterate($email),
            $clientIp,
            '127.0.0.1',
            $request->ip(),
        ];

        if ($usr) {
            $keysToClear[] = Str::transliterate("login_lockout:user_{$usr->id}");
            $keysToClear[] = Str::transliterate("login_lockout:user_{$usr->id}|" . $clientIp);
            $keysToClear[] = Str::transliterate("login_lockout:user_{$usr->id}|127.0.0.1");
            $keysToClear[] = Str::transliterate("login_lockout:user_{$usr->id}|" . $request->ip());
        }

        foreach ($keysToClear as $k) {
            if (!empty($k)) {
                RateLimiter::clear($k);
            }
        }

        // Record Audit Event
        SecurityLog::create([
            'event_type' => 'admin_unlock',
            'email' => $email,
            'ip_address' => $clientIp,
            'user_agent' => $request->userAgent(),
            'details' => "Superadmin (" . session('user_email', 'admin@hirna.ph') . ") manually unlocked account and cleared brute-force rate limiter.",
        ]);

        Log::info("SECURITY AUDIT: Superadmin unlocked account {$email} (IP: {$clientIp})");

        return redirect()->back()->with('success', "🔓 Account Unlocked! Rate limiter lockout completely cleared for {$email}.");
    }

    /**
     * Toggle user account status between Active and Deactivated.
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $this->ensureStatusColumnExists();

        try {
            $usr = User::find($id);
            if (!$usr) {
                // Check fallback default system role accounts (IDs 1-5)
                $defaultUsers = [
                    1 => ['name' => 'Hirna System Admin', 'email' => 'hirna admin', 'role' => 'admin'],
                    2 => ['name' => 'Alex Fleet Manager', 'email' => 'hirna fleet', 'role' => 'fleet_manager'],
                    3 => ['name' => 'Sarah Dispatcher', 'email' => 'hirna dispatcher', 'role' => 'dispatcher'],
                    4 => ['name' => 'Marcus Finance Officer', 'email' => 'hirna finance', 'role' => 'finance'],
                    5 => ['name' => 'Elena Operations Manager', 'email' => 'hirna operations', 'role' => 'operations'],
                ];

                if (isset($defaultUsers[$id])) {
                    $def = $defaultUsers[$id];
                    $usr = User::firstOrCreate(
                        ['email' => $def['email']],
                        [
                            'name' => $def['name'],
                            'role' => $def['role'],
                            'password' => Hash::make('HirnaPass2026!'),
                            'status' => 'active'
                        ]
                    );
                }
            }

            if (!$usr) {
                return redirect()->back()->with('error', 'User account not found in database.');
            }

            $currentEmail = session('user_email', 'admin@hirna.ph');
            if (Str::lower($usr->email) === Str::lower($currentEmail)) {
                return redirect()->back()->with('error', 'Security Policy: You cannot deactivate your own active superadmin session.');
            }

            $newStatus = ($usr->status === 'inactive' || $usr->status === 'deactivated') ? 'active' : 'deactivated';
            $usr->status = $newStatus;
            $usr->save();

            SecurityLog::create([
                'event_type' => 'admin_toggle_status',
                'email' => $usr->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => "Superadmin (" . session('user_email', 'admin@hirna.ph') . ") changed account status for {$usr->name} to '{$newStatus}'",
            ]);

            Log::info("SECURITY AUDIT: Superadmin updated account status for {$usr->email} to {$newStatus}");

            $statusBadge = $newStatus === 'active' ? 'Activated 🟢' : 'Deactivated ⛔';
            return redirect()->back()->with('success', "User account '{$usr->name}' ({$usr->email}) status updated to {$statusBadge}.");
        } catch (\Throwable $e) {
            Log::error("Error toggling user status: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to toggle user status: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a user account from Superadmin Security Center.
     */
    public function deleteUser(Request $request, $id)
    {
        try {
            $usr = User::find($id);
            if (!$usr) {
                return redirect()->back()->with('error', 'User account not found in database.');
            }

            $currentEmail = session('user_email', 'admin@hirna.ph');
            if (Str::lower($usr->email) === Str::lower($currentEmail)) {
                return redirect()->back()->with('error', 'Security Policy Protection: You cannot delete your own active superadmin account.');
            }

            $deletedEmail = $usr->email;
            $deletedName = $usr->name;
            $usr->delete();

            SecurityLog::create([
                'event_type' => 'admin_delete_user',
                'email' => $deletedEmail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => "Superadmin (" . session('user_email', 'admin@hirna.ph') . ") permanently deleted user account: {$deletedName} ({$deletedEmail})",
            ]);

            Log::info("SECURITY AUDIT: Superadmin deleted user account {$deletedEmail}");

            return redirect()->back()->with('success', "🗑️ User account '{$deletedName}' ({$deletedEmail}) deleted successfully.");
        } catch (\Throwable $e) {
            Log::error("Error deleting user: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to delete user account: ' . $e->getMessage());
        }
    }

    /**
     * Safely archive active security audit logs into database table 'security_log_archives'.
     */
    public function archiveLogs(Request $request)
    {
        $this->ensureArchiveTableExists();

        try {
            $logs = SecurityLog::all();
            $count = $logs->count();

            if ($count === 0) {
                return redirect()->back()->with('error', '⚠️ No active security incident logs available to archive.');
            }

            $adminEmail = session('user_email', 'admin@hirna.ph');

            foreach ($logs as $log) {
                SecurityLogArchive::create([
                    'original_log_id' => $log->id,
                    'event_type' => $log->event_type,
                    'email' => $log->email,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'details' => $log->details,
                    'original_created_at' => $log->created_at,
                    'archived_at' => now(),
                    'archived_by' => $adminEmail,
                ]);
            }

            // Truncate active security logs table after safe archiving
            SecurityLog::truncate();

            // Record archiving event audit log
            SecurityLog::create([
                'event_type' => 'admin_archive_logs',
                'email' => $adminEmail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => "Superadmin archived {$count} active security audit log(s) into database table 'security_log_archives'.",
            ]);

            Log::info("SECURITY AUDIT: Superadmin archived {$count} security logs to security_log_archives table.");

            return redirect()->back()->with('success', "📦 Successfully archived {$count} security audit log record(s) into database table 'security_log_archives'.");
        } catch (\Throwable $e) {
            Log::error("Error archiving security logs: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive security logs: ' . $e->getMessage());
        }
    }
}
