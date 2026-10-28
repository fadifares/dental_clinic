<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AppManagerController extends Controller
{
    /**
     * Display Super Admin Dashboard with platform-wide KPIs and clinics listing.
     */
    public function index(): View
    {
        $stats = [
            'total_clinics' => Clinic::count(),
            'active_clinics' => Clinic::where('status', 'active')->count(),
            'suspended_clinics' => Clinic::where('status', 'suspended')->count(),
            'total_users' => User::count(),
            'total_patients' => Patient::withoutGlobalScope('clinic')->count(),
            'total_appointments' => Appointment::withoutGlobalScope('clinic')->count(),
            'total_revenue' => Invoice::withoutGlobalScope('clinic')->sum('paid_amount'),
        ];

        $clinics = Clinic::with(['primaryOwner'])
            ->withCount(['users as active_users_count' => fn ($q) => $q->where('is_active', true)])
            ->withCount(['users as total_users_count'])
            ->latest()
            ->paginate(15);

        return view('appmanager.index', compact('stats', 'clinics'));
    }

    /**
     * Store a newly created clinic and its initial Primary Owner Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:clinics,slug'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'max_users' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:active,suspended'],
            'notes' => ['nullable', 'string'],
            // Primary Owner Details
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'owner_password' => ['required', 'string', Password::min(8)],
        ]);

        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']).'-'.Str::random(4);

        // Ensure unique slug
        if (Clinic::where('slug', $slug)->exists()) {
            $slug .= '-'.rand(100, 999);
        }

        DB::transaction(function () use ($validated, $slug) {
            $clinic = Clinic::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'max_users' => $validated['max_users'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create Primary Owner User
            $owner = new User([
                'clinic_id' => $clinic->id,
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'role' => UserRole::Admin,
                'is_active' => true,
                'is_super_admin' => false,
                'is_primary_owner' => true,
            ]);
            $owner->roles = [UserRole::Admin->value];
            $owner->save();
        });

        return redirect()->route('appmanager.index')
            ->with('success', "تم إنشاء العيادة '{$validated['name']}' وحساب المسؤول الرئيسي الخاص بها بنجاح.");
    }

    /**
     * Update clinic subscription limits, status, or details.
     */
    public function update(Request $request, Clinic $clinic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:clinics,slug,'.$clinic->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'max_users' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:active,suspended'],
            'notes' => ['nullable', 'string'],
        ]);

        $clinic->update($validated);

        return redirect()->route('appmanager.index')
            ->with('success', "تم تحديث بيانات العيادة '{$clinic->name}' وتعيين حد المستخدمين إلى ({$clinic->max_users}) بنجاح.");
    }

    /**
     * Delete or decommission a clinic.
     */
    public function destroy(Clinic $clinic): RedirectResponse
    {
        // Protect Clinic #1 from accidental deletion
        if ($clinic->id === 1) {
            return redirect()->route('appmanager.index')
                ->with('error', 'لا يمكن حذف العيادة الافتراضية الأولى للنظام لأنها تحتوي على البيانات التاريخية الأساسية.');
        }

        $clinicName = $clinic->name;
        $clinic->delete();

        return redirect()->route('appmanager.index')
            ->with('success', "تم حذف العيادة '{$clinicName}' وكافة سجلاتها بنجاح.");
    }

    /**
     * Display detailed statistical dashboard for a specific clinic.
     */
    public function clinicStats(Clinic $clinic, ClinicAnalyticsController $analyticsController): View
    {
        $analytics = $analyticsController->buildClinicAnalyticsData($clinic);

        return view('appmanager.clinic-stats', compact('clinic', 'analytics'));
    }

    /**
     * Execute migrations safely on shared hosting from the Super Admin UI.
     */
    public function runMigrations(): RedirectResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->route('appmanager.index')
                ->with('success', 'تم تنفيذ ترقية قاعدة البيانات بنجاح: '.nl2br(e($output)));
        } catch (\Throwable $e) {
            return redirect()->route('appmanager.index')
                ->with('error', 'فشل في تشغيل الترحيل: '.$e->getMessage());
        }
    }
}
