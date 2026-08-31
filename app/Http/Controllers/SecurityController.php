<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    /**
     * Display the Superadmin Security & User Access Control Center.
     */
    public function index()
    {
        $securityLogs = SecurityLog::latest()->paginate(15);
        
        $dbUsers = User::all();
        
        // Comprehensive user list combining DB users and default system role accounts
        $defaultUsers = collect([
            (object)['id' => 1, 'name' => 'Hirna System Admin', 'email' => 'admin@hirna.ph', 'role' => 'admin', 'status' => 'active'],
            (object)['id' => 2, 'name' => 'Alex Fleet Manager', 'email' => 'fleetmanager@hirna.ph', 'role' => 'fleet_manager', 'status' => 'active'],
            (object)['id' => 3, 'name' => 'Sarah Dispatcher', 'email' => 'dispatcher@hirna.ph', 'role' => 'dispatcher', 'status' => 'active'],
            (object)['id' => 4, 'name' => 'Marcus Finance Officer', 'email' => 'finance@hirna.ph', 'role' => 'finance', 'status' => 'active'],
            (object)['id' => 5, 'name' => 'Elena Operations Manager', 'email' => 'operations@hirna.ph', 'role' => 'operations', 'status' => 'active'],
            (object)['id' => 6, 'name' => 'Green GSM System Admin', 'email' => 'admin@greengsm.com', 'role' => 'admin', 'status' => 'active'],
            (object)['id' => 7, 'name' => 'Green GSM Fleet Manager', 'email' => 'fleetmanager@greengsm.com', 'role' => 'fleet_manager', 'status' => 'active'],
            (object)['id' => 8, 'name' => 'Green GSM Dispatcher', 'email' => 'dispatcher@greengsm.com', 'role' => 'dispatcher', 'status' => 'active'],
        ]);

        $users = $dbUsers->concat($defaultUsers)->unique('email');

        $totalFailedAttempts = SecurityLog::where('event_type', 'failed_login')->count();
        $totalLockouts = SecurityLog::where('event_type', 'account_lockout')->count();
        $totalHoneypotBlocks = SecurityLog::where('event_type', 'bot_honeypot_blocked')->count();
        $recentLockouts = SecurityLog::where('event_type', 'account_lockout')->latest()->take(10)->get();

        return view('admin.security', compact(
            'securityLogs',
            'users',
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&~^()_+\-=\[\]{};\':"\\\\|,.<>\/?]/',
            ],
            'role' => 'required|string|in:admin,fleet_manager,dispatcher,finance,operations,driver',
        ], [
            'email.unique' => 'This email address is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password fails complexity rules: Must include 1 Uppercase (A-Z), 1 Lowercase (a-z), 1 Number (0-9), and 1 Special Character.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => trim(Str::lower($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
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
        $clientIp = trim($request->ip_address ?? $request->ip());

        // Clear all rate limiter key variations for guaranteed lockout removal
        $keysToClear = [
            Str::transliterate($email . '|' . $clientIp),
            Str::transliterate($email . '|127.0.0.1'),
            Str::transliterate($email . '|' . $request->ip()),
            Str::transliterate($email),
            $clientIp,
            '127.0.0.1',
            $request->ip(),
        ];

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
     * Clear old security logs.
     */
    public function clearLogs()
    {
        SecurityLog::truncate();
        
        SecurityLog::create([
            'event_type' => 'admin_unlock',
            'email' => session('user_email', 'admin@hirna.ph'),
            'ip_address' => request()->ip(),
            'details' => 'Security audit log database truncated by Superadmin.',
        ]);

        return redirect()->back()->with('success', 'Security audit logs cleared successfully.');
    }
}
