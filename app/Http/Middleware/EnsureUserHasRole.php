<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
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

        // Check if user's role matches any allowed role
        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array($userRoleValue, $roles, true)) {
            abort(403, 'عذراً، ليس لديك الصلاحية الكافية للوصول إلى هذا القسم الطبي أو المالي.');
        }

        return $next($request);
    }
}
