<?php

namespace App\Http\Controllers;

use App\Models\ClinicUserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and security settings view.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $layout = $user->isSuperAdmin() ? 'appmanager.layout' : 'layouts.clinic';

        $recentActivities = $user->clinic_id
            ? $user->clinicUserActivities()->latest()->take(8)->get()
            : collect();

        return view('profile.show', compact('user', 'layout', 'recentActivities'));
    }

    /**
     * Update the user's personal information (Name, Email, Phone).
     */
    public function updateInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'حقل الاسم الكامل مطلوب.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل من قبل حساب آخر.',
        ]);

        $user->update($validated);

        if ($user->clinic_id) {
            ClinicUserActivity::create([
                'clinic_id' => $user->clinic_id,
                'user_id' => $user->id,
                'action_type' => 'profile_updated',
                'description' => "قام {$user->name} بتحديث بيانات حسابه الشخصية",
                'ip_address' => $request->ip(),
            ]);
        }

        return back()->with('success', 'تم تحديث البيانات الشخصية بنجاح.');
    }

    /**
     * Update the user's password securely.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية التي أدخلتها غير صحيحة.',
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'يجب ألا تقل كلمة المرور الجديدة عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور الجديدة غير متطابق.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($user->clinic_id) {
            ClinicUserActivity::create([
                'clinic_id' => $user->clinic_id,
                'user_id' => $user->id,
                'action_type' => 'password_changed',
                'description' => "قام {$user->name} بتغيير كلمة المرور الخاصة بحسابه",
                'ip_address' => $request->ip(),
            ]);
        }

        return back()->with('success', 'تم تغيير كلمة المرور وتأمين الحساب بنجاح.');
    }
}
