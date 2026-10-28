<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Strictly allow only independent platform super admins who do not belong to any clinic
        if (! Auth::check() || ! Auth::user()->isSuperAdmin() || Auth::user()->clinic_id !== null) {
            abort(403, 'غير مصرح لك بالدخول إلى لوحة تحكم مدير التطبيق (App Manager). هذا القسم مخصص حصرياً لإدارة المنصة ومحظور تماماً على مستخدمي العيادات.');
        }

        return $next($request);
    }
}
