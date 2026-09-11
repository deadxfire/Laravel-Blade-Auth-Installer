<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        if ($user && ! empty($user->two_factor_secret) && ! empty($user->two_factor_confirmed_at)) {
            Auth::guard('web')->logout();
            $request->session()->put('login.id', $user->id);
            $request->session()->put('login.remember', $request->boolean('remember'));

            return redirect()->route('two-factor.login');
        }

        $request->session()->regenerate();

        // Prevent open redirects by enforcing internal relative path
        $intended = $request->session()->pull('url.intended', '/dashboard');
        if (! is_string($intended) || ! str_starts_with($intended, '/') || str_starts_with($intended, '//') || str_starts_with($intended, '/\\')) {
            $intended = '/dashboard';
        }

        return redirect()->to($intended);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
