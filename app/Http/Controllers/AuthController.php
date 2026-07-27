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
            'password' => 'required',
        ]);

        $roleMap = [
            'admin@greengsm.com' => ['name' => 'Green GSM System Admin', 'role' => 'admin'],
            'fleetmanager@greengsm.com' => ['name' => 'Alex Fleet Manager', 'role' => 'fleet_manager'],
            'dispatcher@greengsm.com' => ['name' => 'Sarah Dispatcher', 'role' => 'dispatcher'],
            'finance@greengsm.com' => ['name' => 'Marcus Finance Officer', 'role' => 'finance'],
            'operations@greengsm.com' => ['name' => 'Elena Operations Manager', 'role' => 'operations'],
        ];

        $userInfo = $roleMap[$request->email] ?? ['name' => 'Green GSM Admin', 'role' => 'admin'];

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
