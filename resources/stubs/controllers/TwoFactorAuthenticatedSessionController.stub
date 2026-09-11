<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorAuthenticatedSessionController extends Controller
{
    public function __construct(
        protected TotpService $totp
    ) {}

    /**
     * Show the two-factor authentication challenge view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Attempt to authenticate the user using the two-factor code or recovery code.
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('login.id');
        if (! $userId) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        // 1. Authenticate via standard 6-digit TOTP code
        if ($request->filled('code')) {
            $code = str_replace(' ', '', (string) $request->input('code'));

            if (! $user->two_factor_secret || ! $this->totp->verify(decrypt($user->two_factor_secret), $code)) {
                throw ValidationException::withMessages([
                    'code' => ['The provided two-factor authentication code was invalid.'],
                ]);
            }
        }
        // 2. Authenticate via 1-time emergency recovery code
        elseif ($request->filled('recovery_code')) {
            $recoveryCode = trim((string) $request->input('recovery_code'));
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes ?? '[]'), true) ?: [];

            $foundIndex = null;
            foreach ($recoveryCodes as $index => $code) {
                if (hash_equals($code, $recoveryCode)) {
                    $foundIndex = $index;
                    break;
                }
            }

            if ($foundIndex === null) {
                throw ValidationException::withMessages([
                    'recovery_code' => ['The provided two-factor recovery code was invalid.'],
                ]);
            }

            // Consume and remove the used recovery code
            unset($recoveryCodes[$foundIndex]);
            $user->forceFill([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
            ])->save();
        } else {
            throw ValidationException::withMessages([
                'code' => ['Please provide your two-factor code or an emergency recovery code.'],
            ]);
        }

        // Clear temporary challenge session
        $remember = (bool) $request->session()->pull('login.remember', false);
        $request->session()->forget('login.id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended', '/dashboard');
        if (! is_string($intended) || ! str_starts_with($intended, '/') || str_starts_with($intended, '//') || str_starts_with($intended, '/\\')) {
            $intended = '/dashboard';
        }

        return redirect()->intended($intended);
    }
}
