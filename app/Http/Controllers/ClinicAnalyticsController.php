<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicPageVisit;
use App\Models\ClinicUserActivity;
use App\Models\DentalChart;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClinicAnalyticsController extends Controller
{
    /**
     * Show analytics page for the currently authenticated clinic staff.
     */
    public function index(Request $request): View
    {
        $clinic = auth()->user()->clinic;
        if (! $clinic) {
            abort(404, 'العيادة غير موجودة.');
        }

        $analytics = $this->buildClinicAnalyticsData($clinic);

        return view('clinic.analytics.index', compact('clinic', 'analytics'));
    }

    /**
     * Build all required and complementary statistical metrics for a clinic.
     *
     * @return array<string, mixed>
     */
    public function buildClinicAnalyticsData(Clinic $clinic): array
    {
        $clinicId = $clinic->id;

        // ==============================================================
        // 1. مؤشر حركة الدخول والنشاط اليومي (Daily Logins & Hourly Activity)
        // ==============================================================
        $last14Days = collect(range(13, 0))->map(function ($daysAgo) {
            return Carbon::today()->subDays($daysAgo)->format('Y-m-d');
        });

        $driver = DB::connection()->getDriverName();
        $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%m-%d', created_at)" : 'DATE(created_at)';
        $hourExpr = $driver === 'sqlite' ? "CAST(strftime('%H', created_at) AS INTEGER)" : 'HOUR(created_at)';

        // Daily login counts
        $loginActivities = ClinicUserActivity::where('clinic_id', $clinicId)
            ->where('action_type', 'login')
            ->where('created_at', '>=', Carbon::today()->subDays(13)->startOfDay())
            ->select(DB::raw("{$dateExpr} as log_date"), DB::raw('COUNT(*) as total'))
            ->groupBy('log_date')
            ->pluck('total', 'log_date');

        // Total daily actions (all activity types)
        $dailyActionCounts = ClinicUserActivity::where('clinic_id', $clinicId)
            ->where('created_at', '>=', Carbon::today()->subDays(13)->startOfDay())
            ->select(DB::raw("{$dateExpr} as act_date"), DB::raw('COUNT(*) as total'))
            ->groupBy('act_date')
            ->pluck('total', 'act_date');

        $dailyActivityChart = [
            'dates' => $last14Days->values()->toArray(),
            'logins' => $last14Days->map(fn ($d) => (int) ($loginActivities[$d] ?? 0))->values()->toArray(),
            'actions' => $last14Days->map(fn ($d) => (int) ($dailyActionCounts[$d] ?? 0))->values()->toArray(),
        ];

        // Today's hourly distribution (peak hours indicator)
        $hourlyDistribution = ClinicUserActivity::where('clinic_id', $clinicId)
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->select(DB::raw("{$hourExpr} as hr"), DB::raw('COUNT(*) as count'))
            ->groupBy('hr')
            ->pluck('count', 'hr');

        $hourlyChart = [];
        for ($i = 8; $i <= 23; $i++) {
            $formattedHour = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
            $hourlyChart['labels'][] = $formattedHour;
            $hourlyChart['data'][] = (int) ($hourlyDistribution[$i] ?? 0);
        }

        // ==============================================================
        // 2. أكثر الصفحات التي يتم زيارتها داخل العيادة (Top Visited Pages)
        // ==============================================================
        $totalVisits = ClinicPageVisit::where('clinic_id', $clinicId)->count();

        $topPages = ClinicPageVisit::where('clinic_id', $clinicId)
            ->select('url_path', 'page_title', DB::raw('COUNT(*) as visit_count'), DB::raw('MAX(created_at) as last_visited_at'))
            ->groupBy('url_path', 'page_title')
            ->orderByDesc('visit_count')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($totalVisits) {
                $item->percentage = $totalVisits > 0 ? round(($item->visit_count / $totalVisits) * 100, 1) : 0;

                return $item;
            });

        // Visits breakdown by HTTP method (GET vs Mutations)
        $actionTypesBreakdown = ClinicUserActivity::where('clinic_id', $clinicId)
            ->select('action_type', DB::raw('COUNT(*) as count'))
            ->groupBy('action_type')
            ->pluck('count', 'action_type')
            ->toArray();

        // ==============================================================
        // 3. مستخدمو العيادة ونشاطهم (Users & Productivity Breakdown)
        // ==============================================================
        $users = User::where('clinic_id', $clinicId)
            ->withCount([
                'clinicUserActivities as total_actions_count' => fn ($q) => $q->where('clinic_id', $clinicId),
                'clinicUserActivities as today_actions_count' => fn ($q) => $q->where('clinic_id', $clinicId)->where('created_at', '>=', Carbon::today()->startOfDay()),
            ])
            ->orderBy('id', 'asc')
            ->get();

        // Recent live activity feed for this clinic
        $recentActivities = ClinicUserActivity::where('clinic_id', $clinicId)
            ->with('user')
            ->latest('created_at')
            ->limit(15)
            ->get();

        // ==============================================================
        // 4. إحصائيات إضافية ذكية للعيادة (Medical & Financial KPIs)
        // ==============================================================
        $patientsTotal = Patient::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->count();
        $patientsThisMonth = Patient::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        $appointmentsTotal = Appointment::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->count();
        $appointmentsCompleted = Appointment::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)
            ->where('status', 'completed')
            ->count();
        $appointmentsCancelled = Appointment::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)
            ->where('status', 'cancelled')
            ->count();

        $totalRevenue = (float) Invoice::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->sum('paid_amount');
        $totalExpenses = (float) Expense::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        $dentalChartsCount = DentalChart::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->count();
        $labOrdersCount = LabOrder::withoutGlobalScope('clinic')->where('clinic_id', $clinicId)->count();

        return [
            'total_visits' => $totalVisits,
            'daily_activity_chart' => $dailyActivityChart,
            'hourly_chart' => $hourlyChart,
            'top_pages' => $topPages,
            'action_types_breakdown' => $actionTypesBreakdown,
            'users' => $users,
            'recent_activities' => $recentActivities,
            'kpis' => [
                'patients_total' => $patientsTotal,
                'patients_this_month' => $patientsThisMonth,
                'appointments_total' => $appointmentsTotal,
                'appointments_completed' => $appointmentsCompleted,
                'appointments_cancelled' => $appointmentsCancelled,
                'appointment_completion_rate' => $appointmentsTotal > 0 ? round(($appointmentsCompleted / $appointmentsTotal) * 100, 1) : 0,
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
                'dental_charts_count' => $dentalChartsCount,
                'lab_orders_count' => $labOrdersCount,
                'active_users' => $users->where('is_active', true)->count(),
                'max_users' => $clinic->max_users,
            ],
        ];
    }
}
