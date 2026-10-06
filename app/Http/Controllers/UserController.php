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
            'admins' => $users->filter(fn ($u) => $u->isAdmin())->count(),
            'doctors' => $users->filter(fn ($u) => $u->isDoctor())->count(),
            'receptionists' => $users->filter(fn ($u) => $u->isReceptionist())->count(),
            'accountants' => $users->filter(fn ($u) => $u->isAccountant())->count(),
            'multi_role' => $users->filter(fn ($u) => count($u->getRolesArray()) > 1)->count(),
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
                'description' => 'إنشاء موظفين جدد، تعيين مجموعات الصلاحيات المتعددة، وتعطيل الحسابات',
                'admin' => true,
                'doctor' => false,
                'receptionist' => false,
                'accountant' => false,
            ],
        ];

        return view('clinic.users.index', compact('users', 'stats', 'matrix'));
    }

    /**
     * Store a newly created user in storage with support for multiple roles/groups.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => [new Enum(UserRole::class)],
            'role' => ['nullable', new Enum(UserRole::class)],
            'password' => ['required', 'string', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'speciality' => ['nullable', 'string', 'max:255'],
        ]);

        $roles = $validated['roles'] ?? (isset($validated['role']) ? [$validated['role']] : ['receptionist']);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);
        $user->roles = $roles;
        $user->save();

        // If newly created user has Doctor role, link or create Doctor profile if requested
        if ($user->isDoctor() && ! empty($validated['speciality'])) {
            Doctor::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $validated['phone'] ?? '0500000000',
                'speciality' => $validated['speciality'],
                'commission_rate' => 30.00,
                'is_active' => true,
            ]);
        }

        $labels = $user->getRoleLabelsString();

        return redirect()->route('clinic.users.index')
            ->with('success', "تم إضافة المستخدم '{$user->name}' بنجاح وتعيينه للمجموعات: ({$labels}).");
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
     * Update user details and multiple roles/groups.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => [new Enum(UserRole::class)],
            'role' => ['nullable', new Enum(UserRole::class)],
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        $roles = $validated['roles'] ?? (isset($validated['role']) ? [$validated['role']] : $user->getRolesArray());

        // Security check: Admin cannot revoke their own admin role to prevent lockout
        if ($user->id === auth()->id() && ! in_array(UserRole::Admin->value, $roles, true)) {
            return redirect()->route('clinic.users.index')
                ->with('error', 'لا يمكنك إزالة صلاحية المدير عن حسابك الشخصي منعاً لفقدان السيطرة على النظام.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->roles = $roles;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $labels = $user->getRoleLabelsString();

        return redirect()->route('clinic.users.index')
            ->with('success', "تم تحديث بيانات وصلاحيات '{$user->name}' وتعيين المجموعات: ({$labels}).");
    }
}
