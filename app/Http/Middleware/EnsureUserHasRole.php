<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request and ensure user has one of the allowed roles.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'حسابك معطل أو غير مصرح له بالدخول.',
            ]);
        }

        // Admins have universal access across the clinic system
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the allowed roles
        if (! $user->hasAnyRole($roles)) {
            abort(403, 'عذراً، ليس لديك الصلاحية الكافية للوصول إلى هذا القسم الطبي أو المالي.');
        }

        return $next($request);
    }
}
