<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaaSManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // Ensure Clinic #1 exists
        $clinic = Clinic::firstOrCreate(['id' => 1], [
            'name' => 'العيادة الأولى',
            'slug' => 'clinic-1',
            'status' => 'active',
            'max_users' => 10,
        ]);

        // Ensure Primary Owner exists as clinic manager only (NOT super admin)
        User::firstOrCreate(['email' => 'owner@clinic1.com'], [
            'clinic_id' => $clinic->id,
            'name' => 'المدير الرئيسي',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_primary_owner' => true,
            'is_super_admin' => false,
        ]);
    }

    public function test_super_admin_can_access_appmanager_dashboard(): void
    {
        $superAdmin = User::create([
            'name' => 'FADI D ABUHAMDA',
            'email' => 'fady2200@gmail.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_super_admin' => true,
            'clinic_id' => null,
        ]);

        $response = $this->actingAs($superAdmin)->get('/appmanager');
        $response->assertStatus(200);
        $response->assertSee('لوحة تحكم مدير التطبيق');
        $response->assertSee('إجمالي العيادات المسجلة');
    }

    public function test_clinic_manager_is_forbidden_from_appmanager_and_does_not_see_it_in_menu(): void
    {
        $clinicAdmin = User::where('email', 'owner@clinic1.com')->first();

        // 1. Forbidden from accessing /appmanager
        $response = $this->actingAs($clinicAdmin)->get('/appmanager');
        $response->assertStatus(403);

        // 2. Cannot see App Manager in clinic sidebar navigation
        $responseClinic = $this->actingAs($clinicAdmin)->get('/clinic');
        $responseClinic->assertStatus(200);
        $responseClinic->assertDontSee('لوحة مدير المنصة');
        $responseClinic->assertDontSee('/appmanager');
    }

    public function test_appmaniger_typo_route_redirects_to_appmanager(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin2@saas.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_super_admin' => true,
            'clinic_id' => 1,
        ]);

        $response = $this->actingAs($superAdmin)->get('/appmaniger');
        $response->assertRedirect('/appmanager');
    }

    public function test_super_admin_can_create_new_clinic_with_primary_owner(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin3@saas.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_super_admin' => true,
            'clinic_id' => 1,
        ]);

        $response = $this->actingAs($superAdmin)->post('/appmanager/clinics', [
            'name' => 'عيادة النجوم للأسنان',
            'slug' => 'stars-dental',
            'phone' => '0551122334',
            'email' => 'contact@starsdental.com',
            'address' => 'الرياض - حي الملز',
            'max_users' => 6,
            'status' => 'active',
            'owner_name' => 'د. خالد النجم',
            'owner_email' => 'dr.khaled@starsdental.com',
            'owner_password' => 'SecurePass123#',
        ]);

        $response->assertRedirect('/appmanager');
        $this->assertDatabaseHas('clinics', [
            'name' => 'عيادة النجوم للأسنان',
            'slug' => 'stars-dental',
            'max_users' => 6,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'dr.khaled@starsdental.com',
            'is_primary_owner' => 1,
        ]);
    }

    public function test_super_admin_can_update_clinic_max_users_limit(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin4@saas.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_super_admin' => true,
            'clinic_id' => 1,
        ]);

        $clinic = Clinic::first();

        $response = $this->actingAs($superAdmin)->put('/appmanager/clinics/'.$clinic->id, [
            'name' => $clinic->name,
            'slug' => $clinic->slug,
            'max_users' => 25,
            'status' => 'active',
            'phone' => '0501234567',
        ]);

        $response->assertRedirect('/appmanager');
        $this->assertDatabaseHas('clinics', [
            'id' => $clinic->id,
            'max_users' => 25,
        ]);
    }

    public function test_user_limit_quota_is_enforced_when_creating_users(): void
    {
        $clinic = Clinic::first();
        $clinic->update(['max_users' => 2]);

        // Clinic already has at least 1 active user (primary owner)
        $primaryOwner = User::where('clinic_id', $clinic->id)->where('is_active', true)->first();

        // Add 1 user to reach quota of 2
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'موظف 2',
            'email' => 'staff2@clinic.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Receptionist,
            'is_active' => true,
        ]);

        // Attempting to add a 3rd user should be blocked
        $response = $this->actingAs($primaryOwner)->post('/clinic/users', [
            'name' => 'موظف 3 الزائد',
            'email' => 'staff3@clinic.com',
            'password' => 'Password123#',
            'role' => UserRole::Receptionist->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', [
            'email' => 'staff3@clinic.com',
        ]);
    }

    public function test_primary_owner_cannot_be_deactivated(): void
    {
        $clinic = Clinic::first();
        $primaryOwner = User::where('clinic_id', $clinic->id)->where('is_primary_owner', true)->first();

        $anotherAdmin = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Second Admin',
            'email' => 'secondadmin@clinic.com',
            'password' => bcrypt('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_primary_owner' => false,
        ]);

        $response = $this->actingAs($anotherAdmin)->patch('/clinic/users/'.$primaryOwner->id.'/toggle');
        $response->assertRedirect('/clinic/users');
        $response->assertSessionHas('error');

        $this->assertTrue((bool) $primaryOwner->fresh()->is_active);
    }

    public function test_page_visits_and_user_activities_are_tracked_and_displayed(): void
    {
        $clinic = Clinic::first();
        $user = User::where('clinic_id', $clinic->id)->first();

        // 1. Visit patients page
        $this->actingAs($user)->get('/clinic/patients');

        $this->assertDatabaseHas('clinic_page_visits', [
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'url_path' => '/clinic/patients',
        ]);

        // 2. Perform a mutation (store expense)
        $this->actingAs($user)->post('/clinic/expenses', [
            'title' => 'أدوات تعقيم',
            'category' => 'materials',
            'amount' => 350.00,
            'expense_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('clinic_user_activities', [
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'action_type' => 'create',
        ]);

        // 3. Analytics page displays the tracked stats
        $responseAnalytics = $this->actingAs($user)->get('/clinic/analytics');
        $responseAnalytics->assertStatus(200);
        $responseAnalytics->assertSee('مؤشر حركة الدخول والنشاط اليومي');
        $responseAnalytics->assertSee('أكثر الصفحات والشاشات زيارة داخل العيادة');
        $responseAnalytics->assertSee('مستخدمو العيادة ومعدل نشاطهم وإنتاجيتهم');
    }
}
