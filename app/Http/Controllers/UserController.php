<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system users, role breakdown, and RBAC matrix.
     */
    public function index(): View
    {
        $users = User::latest()->get();

        $stats = [
            'total' => $users->count(),
            'admins' => $users->where('role', UserRole::Admin)->count(),
            'doctors' => $users->where('role', UserRole::Doctor)->count(),
            'receptionists' => $users->where('role', UserRole::Receptionist)->count(),
            'accountants' => $users->where('role', UserRole::Accountant)->count(),
            'active' => $users->where('is_active', true)->count(),
            'inactive' => $users->where('is_active', false)->count(),
        ];

        // Permissions Matrix Definition
        $matrix = [
            [
                'module' => 'لوحة المؤشرات والإحصائيات العامة',
                'description' => 'الاطلاع على أعداد المواعيد والعمليات ومؤشرات الأداء',
                'admin' => true,
                'doctor' => true,
                'receptionist' => true,
                'accountant' => true,
            ],
            [
                'module' => 'إدارة المواعيد والاستقبال',
                'description' => 'حجز المواعيد، تغيير الحالة (انتظار/دخل/اكتمل)، وتأكيد الزيارات',
                'admin' => true,
                'doctor' => true,
                'receptionist' => true,
                'accountant' => false,
            ],
            [
                'module' => 'السجل الطبي ومخطط الأسنان (Odontogram)',
                'description' => 'تعديل حالات الأسنان (تسوس، حشوات، زراعة) وخطط العلاج السريرية',
                'admin' => true,
                'doctor' => true,
                'receptionist' => false,
                'accountant' => false,
            ],
            [
                'module' => 'طلبيات معامل الأسنان والتركيبات',
                'description' => 'إرسال مقاسات وتيجان وزيركون ومتابعة جاهزية المعمل',
                'admin' => true,
                'doctor' => true,
                'receptionist' => false,
                'accountant' => false,
            ],
            [
                'module' => 'الفواتير والتحصيل المالي والضرائب',
                'description' => 'إصدار الفواتير الضريبية، السندات، تقارير الدخل، وأقساط المرضى',
                'admin' => true,
                'doctor' => false,
                'receptionist' => false,
                'accountant' => true,
            ],
            [
                'module' => 'إدارة المستخدمين والصلاحيات والرقابة الأمنية',
                'description' => 'إنشاء موظفين جدد، تعديل الصلاحيات، تعطيل الحسابات، ومراجعة الأمان',
                'admin' => true,
                'doctor' => false,
                'receptionist' => false,
                'accountant' => false,
            ],
        ];

        return view('clinic.users.index', compact('users', 'stats', 'matrix'));
    }

    /**
     * Store a newly created user in storage with strict password & role validation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', new Enum(UserRole::class)],
            'password' => ['required', 'string', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'speciality' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // If newly created user is a Doctor, link or create Doctor profile if requested
        if ($user->role === UserRole::Doctor && ! empty($validated['speciality'])) {
            Doctor::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $validated['phone'] ?? '0500000000',
                'speciality' => $validated['speciality'],
                'commission_rate' => 30.00,
                'is_active' => true,
            ]);
        }

        return redirect()->route('clinic.users.index')
            ->with('success', "تم إضافة المستخدم '{$user->name}' بصلاحية ({$user->role->label()}) بنجاح.");
    }

    /**
     * Toggle the active status of a user (suspend / reactivate).
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Security check: Admin cannot deactivate their own logged-in account
        if ($user->id === auth()->id()) {
            return redirect()->route('clinic.users.index')
                ->with('error', 'لا يمكنك تعطيل حسابك الشخصي الذي تستخدمه لتسجيل الدخول حالياً لأسباب أمنية.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'تنشيط' : 'تعطيل وتجميد وصول';

        return redirect()->route('clinic.users.index')
            ->with('success', "تم {$statusText} المستخدم '{$user->name}' بنجاح.");
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', new Enum(UserRole::class)],
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        // Security check: Admin cannot revoke their own admin role to prevent lockout
        if ($user->id === auth()->id() && $validated['role'] !== UserRole::Admin->value) {
            return redirect()->route('clinic.users.index')
                ->with('error', 'لا يمكنك إزالة صلاحية المدير عن حسابك الشخصي منعاً لفقدان السيطرة على النظام.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('clinic.users.index')
            ->with('success', "تم تحديث بيانات وصلاحيات '{$user->name}' بنجاح.");
    }
}
