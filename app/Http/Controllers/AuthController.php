<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display the secure login page.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Process authentication request with enterprise native security defenses:
     * - Anti-Bot Honeypot Trap
     * - Rate Limiting & Brute-Force Lockout
     * - Password Complexity Validation
     * - Bcrypt Hash Verification
     * - Session Fixation Regeneration
     * - Security Audit Trail Logging
     */
    public function login(Request $request)
    {
        // 1. Anti-Bot Honeypot Check (Silently reject automated scrapers)
        if ($request->filled('hirna_security_hp')) {
            Log::warning("SECURITY ALERT: Bot honeypot triggered from IP: {$request->ip()}");
            return back()->with('error', 'Automated submission detected and blocked by security filters.');
        }

        // 2. Rate Limiting / Brute-Force Lockout Defense
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning("SECURITY LOCKOUT: IP {$request->ip()} locked out due to multiple failed login attempts on {$request->email}");
            return back()->with('error', "Security Lockout: Too many failed login attempts. Please wait {$seconds} seconds before trying again.");
        }

        // 3. Strict Input & Password Complexity Validation
        $request->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&~^()_+\-=\[\]{};\':"\\\\|,.<>\/?]/',
            ],
        ], [
            'password.min' => 'Security Error: Password must be at least 8 characters long.',
            'password.regex' => 'Security Error: Password fails complexity rules. Must include at least 1 Capital Letter (A-Z), 1 Lowercase Letter (a-z), 1 Digit (0-9), and 1 Special Character (e.g., @$!%*#?).',
        ]);

        // 4. Authenticate Against Database User Records
        $user = User::where('email', $request->email)->first();
        $authenticated = false;

        if ($user && Hash::check($request->password, $user->password)) {
            $authenticated = true;
            $userName = $user->name;
            $userRole = $user->role ?? 'admin';
            $userId = $user->id;
        } else {
            // Fallback for demo role accounts
            $roleMap = [
                'admin@hirna.ph' => ['name' => 'Hirna System Admin', 'role' => 'admin'],
                'fleetmanager@hirna.ph' => ['name' => 'Alex Fleet Manager', 'role' => 'fleet_manager'],
                'dispatcher@hirna.ph' => ['name' => 'Sarah Dispatcher', 'role' => 'dispatcher'],
                'finance@hirna.ph' => ['name' => 'Marcus Finance Officer', 'role' => 'finance'],
                'operations@hirna.ph' => ['name' => 'Elena Operations Manager', 'role' => 'operations'],
            ];

            if (isset($roleMap[$request->email]) && $request->password === 'Password@123') {
                $authenticated = true;
                $userName = $roleMap[$request->email]['name'];
                $userRole = $roleMap[$request->email]['role'];
                $userId = 1;
            }
        }

        // 5. Handle Authentication Outcome
        if ($authenticated) {
            // Clear brute-force rate limiter on success
            RateLimiter::clear($throttleKey);

            // Session Fixation Defense: Regenerate session ID
            $request->session()->regenerate();

            session([
                'user_id' => $userId,
                'user_name' => $userName,
                'user_email' => $request->email,
                'user_role' => $userRole,
            ]);

            // Audit Trail Log
            Log::info("SECURITY AUDIT: Successful login for {$request->email} ({$userRole}) from IP {$request->ip()}");

            $roleTitle = ucwords(str_replace('_', ' ', $userRole));
            return redirect()->route('dashboard')->with('success', "Signed in as {$userName} ({$roleTitle}).");
        }

        // Failed Login: Record Strike in RateLimiter and Log Alert
        RateLimiter::hit($throttleKey, 60);
        $attemptsLeft = RateLimiter::remaining($throttleKey, 5);

        Log::warning("SECURITY ALERT: Failed login attempt for {$request->email} from IP {$request->ip()}. {$attemptsLeft} attempts remaining.");

        return back()->with('error', "Invalid credentials. You have {$attemptsLeft} login attempt(s) remaining before temporary lockout.");
    }

    /**
     * Switch active role live during demo.
     */
    public function switchRole(Request $request)
    {
        $role = $request->get('role', 'admin');
        $validRoles = [
            'admin' => 'System Administrator',
            'fleet_manager' => 'Fleet Manager',
            'dispatcher' => 'Dispatcher',
            'finance' => 'Finance Officer',
            'operations' => 'Operations Manager',
        ];

        if (array_key_exists($role, $validRoles)) {
            session(['user_role' => $role]);
            Log::info("SECURITY AUDIT: Perspective switched to '{$role}' by user " . session('user_email'));
            return redirect()->back()->with('success', "Active perspective switched to: " . $validRoles[$role]);
        }

        return redirect()->back();
    }

    /**
     * Secure Logout: Invalidate session and regenerate CSRF token.
     */
    public function logout(Request $request)
    {
        $userEmail = session('user_email', 'User');
        Log::info("SECURITY AUDIT: User {$userEmail} logged out from IP: {$request->ip()}");

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been securely signed out of the Hirna Portal.');
    }
}
