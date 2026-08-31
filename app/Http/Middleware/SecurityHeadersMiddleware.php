<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach native enterprise HTTP security headers.
     * Zero third-party dependencies required.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent Clickjacking attacks inside unauthorized iframes
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable cross-site scripting (XSS) filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Strict referrer policy to prevent credential/URL leakage
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (restrict sensitive hardware access)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        return $response;
    }
}
