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
            'doctors_type' => $users->filter(fn ($u) => $u->user_type === 'doctor')->count(),
            'administrative_type' => $users->filter(fn ($u) => $u->user_type === 'administrative')->count(),
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
        if (! $request->has('user_type')) {
            $roles = (array) $request->input('roles', []);
            if ($request->input('role') === 'doctor' || in_array('doctor', $roles, true)) {
                $request->merge(['user_type' => 'doctor']);
            } else {
                $request->merge(['user_type' => 'administrative']);
            }
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_type' => ['required', 'in:doctor,administrative'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => [new Enum(UserRole::class)],
            'role' => ['nullable', new Enum(UserRole::class)],
            'password' => ['required', 'string', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'speciality' => ['nullable', 'string', 'max:255'],
        ]);

        $defaultRole = $validated['user_type'] === 'doctor' ? 'doctor' : 'receptionist';
        $roles = $validated['roles'] ?? (isset($validated['role']) ? [$validated['role']] : [$defaultRole]);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'phone' => $validated['phone'] ?? null,
            'speciality' => $validated['speciality'] ?? null,
            'is_active' => true,
        ]);
        $user->roles = $roles;
        $user->save();

        // If user is a Doctor type, link or create Doctor profile
        if ($user->user_type === 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $validated['phone'] ?? '0500000000',
                'speciality' => $validated['speciality'] ?? 'طبيب أسنان عام',
                'commission_rate' => 30.00,
                'is_active' => true,
            ]);
        }

        $labels = $user->getRoleLabelsString();
        $typeLabel = $user->getUserTypeLabel();

        return redirect()->route('clinic.users.index')
            ->with('success', "تم إضافة المستخدم '{$user->name}' بنجاح كـ ({$typeLabel}) وتعيينه للمجموعات: ({$labels}).");
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

        // Synchronize linked Doctor profile status
        Doctor::where('user_id', $user->id)->update([
            'is_active' => $user->is_active,
        ]);

        $statusText = $user->is_active ? 'تنشيط' : 'تعطيل وتجميد وصول';

        return redirect()->route('clinic.users.index')
            ->with('success', "تم {$statusText} المستخدم '{$user->name}' بنجاح.");
    }

    /**
     * Update user details and multiple roles/groups.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (! $request->has('user_type')) {
            $request->merge(['user_type' => $user->user_type ?? 'administrative']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'user_type' => ['required', 'in:doctor,administrative'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => [new Enum(UserRole::class)],
            'role' => ['nullable', new Enum(UserRole::class)],
            'password' => ['nullable', 'string', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'speciality' => ['nullable', 'string', 'max:255'],
        ]);

        $roles = $validated['roles'] ?? (isset($validated['role']) ? [$validated['role']] : $user->getRolesArray());

        // Security check: Admin cannot revoke their own admin role to prevent lockout
        if ($user->id === auth()->id() && ! in_array(UserRole::Admin->value, $roles, true)) {
            return redirect()->route('clinic.users.index')
                ->with('error', 'لا يمكنك إزالة صلاحية المدير عن حسابك الشخصي منعاً لفقدان السيطرة على النظام.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_type = $validated['user_type'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->speciality = $validated['speciality'] ?? $user->speciality;
        $user->roles = $roles;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Synchronize Doctor profile
        if ($user->user_type === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($doctor) {
                $doctor->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? $doctor->phone,
                    'speciality' => $user->speciality ?? $doctor->speciality,
                    'is_active' => $user->is_active,
                ]);
            } else {
                Doctor::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '0500000000',
                    'speciality' => $user->speciality ?? 'طبيب أسنان عام',
                    'commission_rate' => 30.00,
                    'is_active' => $user->is_active,
                ]);
            }
        } else {
            // If converted to administrative, deactivate doctor profile so they don't appear in treating doctors
            Doctor::where('user_id', $user->id)->update([
                'is_active' => false,
            ]);
        }

        $labels = $user->getRoleLabelsString();
        $typeLabel = $user->getUserTypeLabel();

        return redirect()->route('clinic.users.index')
            ->with('success', "تم تحديث بيانات وصلاحيات '{$user->name}' كـ ({$typeLabel}) وتعيين المجموعات: ({$labels}).");
    }
}
