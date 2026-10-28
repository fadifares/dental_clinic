<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->clinic_id) {
                $clinic = $user->clinic;

                if ($clinic && $clinic->status === 'suspended') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'تم تجميد حساب هذه العيادة مؤقتاً. يرجى مراجعة إدارة المنصة.');
                }
            }
        }

        return $next($request);
    }
}
