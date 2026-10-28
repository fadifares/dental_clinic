<?php

namespace App\Http\Middleware;

use App\Models\ClinicPageVisit;
use App\Models\ClinicUserActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class TrackClinicActivity
{
    /**
     * Map common route names to readable Arabic titles.
     *
     * @var array<string, string>
     */
    protected static array $routeTitles = [
        'clinic.dashboard' => 'لوحة التحكم والعيادة اليومية',
        'clinic.patients.index' => 'سجل المرضى والملفات الطبية',
        'clinic.patients.show' => 'الملف الطبي للمريض',
        'clinic.appointments.index' => 'جدول المواعيد والاستقبال',
        'clinic.billing.index' => 'الفواتير والتحصيل المالي',
        'clinic.billing.show' => 'معاينة الفاتورة الضريبية',
        'clinic.labs.index' => 'طلبيات معامل الأسنان والتركيبات',
        'clinic.expenses.index' => 'المصروفات وسندات الصرف',
        'clinic.users.index' => 'إدارة الموظفين والمستخدمين',
        'clinic.settings.index' => 'إعدادات النظام والعيادة',
        'clinic.analytics' => 'مؤشرات الأداء وإحصائيات العيادة',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track authenticated requests with clinic context
        if (Auth::check()) {
            $user = Auth::user();
            $clinicId = $user->clinic_id;

            if ($clinicId) {
                $routeName = Route::currentRouteName() ?? '';
                $method = $request->method();
                $path = $request->path();

                // 1. Track GET page visits (exclude ajax search or json endpoints to prevent noise)
                if ($method === 'GET' && ! $request->ajax() && ! $request->wantsJson()) {
                    $pageTitle = self::$routeTitles[$routeName] ?? $this->deriveTitleFromPath($path);

                    // Insert asynchronously / silently
                    try {
                        ClinicPageVisit::create([
                            'clinic_id' => $clinicId,
                            'user_id' => $user->id,
                            'route_name' => $routeName,
                            'url_path' => '/'.ltrim($path, '/'),
                            'page_title' => $pageTitle,
                            'method' => $method,
                            'ip_address' => $request->ip(),
                            'user_agent' => substr((string) $request->userAgent(), 0, 255),
                            'created_at' => now(),
                        ]);
                    } catch (\Throwable $e) {
                        // Fail silently so user experience is never interrupted
                    }
                }

                // 2. Track Data Mutations (POST, PUT, PATCH, DELETE)
                if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && ! $request->routeIs('logout')) {
                    $actionType = match ($method) {
                        'POST' => 'create',
                        'PUT', 'PATCH' => 'update',
                        'DELETE' => 'delete',
                        default => 'action',
                    };

                    $desc = $this->buildActionDescription($routeName, $method, $user->name);

                    try {
                        ClinicUserActivity::create([
                            'clinic_id' => $clinicId,
                            'user_id' => $user->id,
                            'action_type' => $actionType,
                            'description' => $desc,
                            'details' => [
                                'route' => $routeName,
                                'path' => '/'.ltrim($path, '/'),
                            ],
                            'ip_address' => $request->ip(),
                            'created_at' => now(),
                        ]);
                    } catch (\Throwable $e) {
                        // Fail silently
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Derive a user-friendly title from URL path when route name is generic.
     */
    protected function deriveTitleFromPath(string $path): string
    {
        if (str_contains($path, 'patients')) {
            return 'سجل المرضى';
        }
        if (str_contains($path, 'appointments')) {
            return 'إدارة المواعيد';
        }
        if (str_contains($path, 'billing')) {
            return 'الفواتير والحسابات';
        }
        if (str_contains($path, 'labs')) {
            return 'معامل الأسنان';
        }
        if (str_contains($path, 'expenses')) {
            return 'المصروفات';
        }
        if (str_contains($path, 'users')) {
            return 'المستخدمون والصلاحيات';
        }
        if (str_contains($path, 'settings')) {
            return 'إعدادات النظام';
        }

        return 'لوحة العيادة';
    }

    /**
     * Generate an Arabic human-readable action description.
     */
    protected function buildActionDescription(string $routeName, string $method, string $userName): string
    {
        return match ($routeName) {
            'clinic.patients.store' => "قام {$userName} بتسجيل مريض جديد في النظام",
            'clinic.patients.update' => "قام {$userName} بتحديث بيانات مريض",
            'clinic.appointments.store' => "قام {$userName} بحجز موعد كشف جديد",
            'clinic.appointments.status' => "قام {$userName} بتحديث حالة موعد",
            'clinic.appointments.destroy' => "قام {$userName} بإلغاء / حذف موعد",
            'clinic.tooth.update' => "قام {$userName} بتسجيل إجراء علاجي على مخطط الأسنان",
            'clinic.billing.store' => "قام {$userName} بإصدار فاتورة علاجية جديدة",
            'clinic.billing.payment' => "قام {$userName} بتسجيل دفعة مالية على فاتورة",
            'clinic.labs.store' => "قام {$userName} بإرسال طلبية جديدة إلى المعمل",
            'clinic.labs.status' => "قام {$userName} بتحديث حالة طلبية معمل",
            'clinic.expenses.store' => "قام {$userName} بتسجيل سند صرف / مصروف جديد",
            'clinic.users.store' => "قام {$userName} بإنشاء حساب مستخدم جديد",
            'clinic.users.toggle' => "قام {$userName} بتعديل حالة تفعيل حساب موظف",
            'clinic.settings.update' => "قام {$userName} بتحديث الإعدادات العامة للعيادة",
            default => "قام {$userName} بإجراء عملية ({$method}) على النظام",
        };
    }
}
