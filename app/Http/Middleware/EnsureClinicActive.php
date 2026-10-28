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

            // 1. Platform Super Admin is completely separated from clinic operations
            if ($user->clinic_id === null) {
                if ($user->isSuperAdmin()) {
                    return redirect()->route('appmanager.index')
                        ->with('error', 'حساب مدير المنصة مخصص لإدارة التطبيق فقط ومفصول بالكامل عن السجلات والملفات الطبية للعيادات.');
                }

                Auth::logout();

                return redirect()->route('login')->with('error', 'هذا الحساب غير مسجل ضمن أي عيادة طبية.');
            }

            // 2. Ensure clinic is active
            $clinic = $user->clinic;
            if (! $clinic || $clinic->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'تم تجميد حساب هذه العيادة مؤقتاً. يرجى مراجعة إدارة المنصة.');
            }
        }

        return $next($request);
    }
}
