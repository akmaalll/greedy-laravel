<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function showLogin()
    {
        return view('pages.authentications.auth-login-basic');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        if (! \Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Password salah']);
        }

        if (! $user->role_id || ! $user->role) {
            return back()->withErrors(['email' => 'Akun belum punya role']);
        }

        Auth::loginUsingId($user->id, $request->filled('remember'));

        // paksa tulis session
        session()->put('auth_password_confirmed_at', time());
        session()->put('login_time', now());

        $request->session()->regenerate();
        $request->session()->save();

        // Double ensure
        app('session')->save();

        \Log::info('LOGIN CPANEL DEBUG', [
            'session_id' => session()->getId(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        // Log aktivitas logout sebelum logout
        $this->activityLogService->logLogout();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegister()
    {
        return view('pages.authentications.auth-register-basic');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = User::create([
            'name' => strtoupper($request->name),
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => \Hash::make($request->password),
            'role_id' => 3,
        ]);

        Auth::login($user);

        return redirect()->intended('/');
    }
}
