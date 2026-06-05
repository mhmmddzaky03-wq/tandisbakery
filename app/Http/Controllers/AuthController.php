<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(string $role = 'admin')
    {
        if (! config('app.auth_enabled')) {
            return redirect()->route(match ($role) {
                'karyawan' => 'karyawan.dashboard',
                default => 'admin.dashboard',
            });
        }

        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        $supported = ['admin', 'karyawan'];
        if (! in_array($role, $supported, true)) {
            $role = 'admin';
        }

        return view('auth.login', ['role' => $role]);
    }

    public function login(Request $request)
    {
        if (! config('app.auth_enabled')) {
            return redirect()->route('admin.dashboard');
        }

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', 'in:admin,karyawan'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user) {
            return back()->withErrors([
                'username' => __('messages.auth.username_not_found'),
            ])->withInput($request->only('username', 'role'));
        }

        if ($user->role !== $credentials['role']) {
            return back()->withErrors([
                'username' => __('messages.auth.role_mismatch'),
            ])->withInput($request->only('username', 'role'));
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();

            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'password' => __('messages.auth.password_wrong'),
        ])->withInput($request->only('username', 'role'));
    }

    public function logout(Request $request)
    {
        if (! config('app.auth_enabled')) {
            return redirect()->route('admin.dashboard');
        }

        $role = Auth::check() ? Auth::user()->role : 'admin';
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route(match ($role) {
            'karyawan' => 'auth.login.karyawan',
            default => 'auth.login.admin',
        });
    }

    private function redirectUser(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role === 'karyawan') {
            return redirect()->intended(route('karyawan.dashboard'));
        }

        abort(403);
    }
}
