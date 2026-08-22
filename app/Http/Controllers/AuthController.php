<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Process authentication request.
     */
    public function login(Request $request)
    {
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

        $roleMap = [
            'admin@hirna.ph' => ['name' => 'Hirna System Admin', 'role' => 'admin'],
            'admin@greengsm.com' => ['name' => 'Hirna System Admin', 'role' => 'admin'],
            'fleetmanager@hirna.ph' => ['name' => 'Alex Fleet Manager', 'role' => 'fleet_manager'],
            'fleetmanager@greengsm.com' => ['name' => 'Alex Fleet Manager', 'role' => 'fleet_manager'],
            'dispatcher@hirna.ph' => ['name' => 'Sarah Dispatcher', 'role' => 'dispatcher'],
            'dispatcher@greengsm.com' => ['name' => 'Sarah Dispatcher', 'role' => 'dispatcher'],
            'finance@hirna.ph' => ['name' => 'Marcus Finance Officer', 'role' => 'finance'],
            'finance@greengsm.com' => ['name' => 'Marcus Finance Officer', 'role' => 'finance'],
            'operations@hirna.ph' => ['name' => 'Elena Operations Manager', 'role' => 'operations'],
            'operations@greengsm.com' => ['name' => 'Elena Operations Manager', 'role' => 'operations'],
        ];

        $userInfo = $roleMap[$request->email] ?? ['name' => 'Hirna System Admin', 'role' => 'admin'];

        session([
            'user_id' => 1,
            'user_name' => $userInfo['name'],
            'user_email' => $request->email,
            'user_role' => $userInfo['role'],
        ]);

        $roleTitle = ucwords(str_replace('_', ' ', $userInfo['role']));
        return redirect()->route('dashboard')->with('success', "Signed in as {$userInfo['name']} ({$roleTitle}).");
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
            return redirect()->back()->with('success', "Active perspective switched to: " . $validRoles[$role]);
        }

        return redirect()->back();
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'user_role']);
        return redirect()->route('login')->with('success', 'You have been signed out of Green GSM Portal.');
    }
}
