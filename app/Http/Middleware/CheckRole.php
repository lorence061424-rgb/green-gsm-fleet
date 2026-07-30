<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request for Role-Based Access Control (RBAC).
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Auto-provision default admin session if unauthenticated on initial visit
        if (!session()->has('user_id')) {
            session([
                'user_id' => 1,
                'user_role' => 'admin',
                'user_name' => 'Green GSM Admin',
                'user_email' => 'admin@greengsm.com'
            ]);
        }

        $userRole = session('user_role', 'admin');

        // Admin has full access to everything or when no specific roles are required
        if ($userRole === 'admin' || empty($roles)) {
            return $next($request);
        }

        // Check if current user role matches permitted roles for this route
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Return error notification if access is restricted
        return redirect()->back()->with('error', 'Access Restricted! Your active role (' . strtoupper(str_replace('_', ' ', $userRole)) . ') does not have permission to perform this action.');
    }
}
