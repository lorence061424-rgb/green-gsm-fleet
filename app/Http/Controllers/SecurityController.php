<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;
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
        
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([
                (object)['id' => 1, 'name' => 'Hirna System Admin', 'email' => 'admin@hirna.ph', 'role' => 'admin', 'status' => 'active'],
                (object)['id' => 2, 'name' => 'Alex Fleet Manager', 'email' => 'fleetmanager@hirna.ph', 'role' => 'fleet_manager', 'status' => 'active'],
                (object)['id' => 3, 'name' => 'Sarah Dispatcher', 'email' => 'dispatcher@hirna.ph', 'role' => 'dispatcher', 'status' => 'active'],
                (object)['id' => 4, 'name' => 'Marcus Finance Officer', 'email' => 'finance@hirna.ph', 'role' => 'finance', 'status' => 'active'],
                (object)['id' => 5, 'name' => 'Elena Operations Manager', 'email' => 'operations@hirna.ph', 'role' => 'operations', 'status' => 'active'],
            ]);
        }

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
     * One-Click Superadmin Account & IP Unlock feature.
     * Instantly resets RateLimiter brute-force counters for the specified email and IP address.
     */
    public function unlockUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'ip_address' => 'nullable|string',
        ]);

        $email = trim($request->email);
        $ip = trim($request->ip_address ?? $request->ip());

        // Construct rate limiter throttle key
        $throttleKey = Str::transliterate(Str::lower($email) . '|' . $ip);
        $emailOnlyKey = Str::transliterate(Str::lower($email));

        // Clear RateLimiter keys
        RateLimiter::clear($throttleKey);
        RateLimiter::clear($emailOnlyKey);

        // Record Audit Event
        SecurityLog::create([
            'event_type' => 'admin_unlock',
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'details' => "Superadmin (" . session('user_email', 'admin@hirna.ph') . ") manually unlocked account and cleared brute-force rate limiter.",
        ]);

        Log::info("SECURITY AUDIT: Superadmin unlocked account {$email} (IP: {$ip})");

        return redirect()->back()->with('success', "🔓 Account Unlocked! Security rate-limiter lockout cleared for {$email}.");
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
