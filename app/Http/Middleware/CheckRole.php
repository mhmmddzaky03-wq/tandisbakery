<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login.admin');
        }

        $userRole = auth()->user()->role;
        if (!in_array($userRole, $roles, true)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            } elseif ($userRole === 'basket') {
                return redirect()->route('basket.dashboard');
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
