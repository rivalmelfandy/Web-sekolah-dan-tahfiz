<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi berdasarkan nip_nis + password (Auth::attempt).
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('nip_nis', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectFor(Auth::user()));
        }

        return back()
            ->withInput($request->only('nip_nis'))
            ->withErrors(['nip_nis' => 'NIP/NIS atau password salah.']);
    }

    /**
     * Logout pengguna yang sedang login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Tentukan rute tujuan setelah login berdasarkan peran pengguna.
     */
    private function redirectFor(User $user): string
    {
        return match ($user->role) {
            UserRole::Admin => route('admin.dashboard'),
            UserRole::Ustadz => route('ustadz.dashboard'),
            UserRole::Siswa => route('siswa.dashboard'),
        };
    }
}
