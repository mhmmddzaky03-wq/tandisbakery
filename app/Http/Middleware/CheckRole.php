<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! config('app.auth_enabled')) {
            $role = $roles[0] ?? 'admin';
            $user = User::where('role', $role)->first();
            if (! $user) {
                abort(503, "User role \"{$role}\" tidak ditemukan. Jalankan: php artisan db:seed");
            }
            Auth::login($user);

            return $next($request);
        }

        if (! auth()->check()) {
            return redirect()->guest(route('auth.login.admin'));
        }

        $userRole = auth()->user()->role;
        if (!in_array($userRole, $roles, true)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
