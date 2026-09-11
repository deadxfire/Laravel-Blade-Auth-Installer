<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $normalizedEmail = Str::lower(trim($request->email));

        Password::sendResetLink([
            'email' => $normalizedEmail,
        ]);

        // Anti-enumeration defense: always show uniform success message
        return back()->with('status', 'If an account exists with that email, a password reset link has been sent.');
    }
}
