<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the medical portal login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('clinic.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt with brute-force rate limiting.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Check Rate Limiter (Max 5 attempts per minute)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "تم تجاوز عدد محاولات الدخول المسموح بها. يرجى الانتظار لمدة {$seconds} ثانية والمحاولة مجدداً.",
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'هذا الحساب معطل حالياً من قِبل إدارة العيادة.',
                ]);
            }

            // Regenerate session to protect against session fixation attacks
            $request->session()->regenerate();

            return redirect()->intended(route('clinic.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => 'بيانات الدخول غير صحيحة. يرجى التحقق من البريد الإلكتروني أو كلمة المرور.',
        ]);
    }

    /**
     * Securely log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح وأمان.');
    }
}
