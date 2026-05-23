<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin(string $role = 'admin')
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        $supported = ['admin', 'karyawan', 'basket'];
        if (!in_array($role, $supported, true)) {
            $role = 'admin';
        }

        if ($role === 'basket') {
            return view('auth.login-basket');
        }

        return view('auth.login', ['role' => $role]);
    }

    /**
     * Authenticate login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', 'in:admin,karyawan,basket'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => __('Username tidak ditemukan.'),
            ])->withInput($request->only('username', 'role'));
        }

        if ($user->role !== $credentials['role']) {
            return back()->withErrors([
                'username' => __('Role pengguna tidak cocok dengan login yang dipilih.'),
            ])->withInput($request->only('username', 'role'));
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'password' => __('Password salah.'),
        ])->withInput($request->only('username', 'role'));
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        $role = Auth::check() ? Auth::user()->role : 'admin';
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route(match ($role) {
            'karyawan' => 'auth.login.karyawan',
            'basket' => 'auth.login.basket',
            default => 'auth.login.admin',
        });
    }

    /**
     * Helper to redirect based on user role.
     */
    private function redirectUser(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->role === 'karyawan') {
            return redirect()->intended(route('karyawan.dashboard'));
        } elseif ($user->role === 'basket') {
            return redirect()->intended(route('basket.dashboard'));
        }
        abort(403);
    }
}
