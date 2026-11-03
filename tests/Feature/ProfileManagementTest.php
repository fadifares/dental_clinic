<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic;

    protected User $clinicUser;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        $this->clinic = Clinic::create([
            'id' => 1,
            'name' => 'العيادة التجريبية',
            'slug' => 'test-clinic',
            'status' => 'active',
            'max_users' => 10,
        ]);

        $this->clinicUser = User::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'د. أحمد محمود',
            'email' => 'doctor@clinic.com',
            'phone' => '01012345678',
            'password' => Hash::make('Password123#'),
            'role' => UserRole::Doctor,
            'is_active' => true,
            'is_primary_owner' => false,
            'is_super_admin' => false,
        ]);

        $this->superAdmin = User::create([
            'clinic_id' => null,
            'name' => 'FADI D ABUHAMDA',
            'email' => 'fady2200@gmail.com',
            'password' => Hash::make('Password123#'),
            'role' => UserRole::Admin,
            'is_active' => true,
            'is_primary_owner' => false,
            'is_super_admin' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');

        $clinicResponse = $this->get('/clinic/profile');
        $clinicResponse->assertRedirect('/login');

        $adminResponse = $this->get('/appmanager/profile');
        $adminResponse->assertRedirect('/login');
    }

    public function test_clinic_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->clinicUser)->get('/clinic/profile');

        $response->assertStatus(200);
        $response->assertSee('الملف الشخصي وأمان الحساب');
        $response->assertSee('د. أحمد محمود');
        $response->assertSee('doctor@clinic.com');
        $response->assertSee('01012345678');
        $response->assertSee('العيادة التجريبية');
    }

    public function test_super_admin_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/appmanager/profile');

        $response->assertStatus(200);
        $response->assertSee('الملف الشخصي وأمان الحساب');
        $response->assertSee('FADI D ABUHAMDA');
        $response->assertSee('fady2200@gmail.com');
        $response->assertSee('مدير عام منصة SaaS');
    }

    public function test_profile_shortcut_redirects_correctly_based_on_role(): void
    {
        $clinicResponse = $this->actingAs($this->clinicUser)->get('/profile');
        $clinicResponse->assertRedirect('/clinic/profile');

        $superResponse = $this->actingAs($this->superAdmin)->get('/profile');
        $superResponse->assertRedirect('/appmanager/profile');
    }

    public function test_user_can_update_personal_information(): void
    {
        $response = $this->actingAs($this->clinicUser)->put('/clinic/profile/info', [
            'name' => 'د. أحمد محمود المحدّث',
            'email' => 'updated.doctor@clinic.com',
            'phone' => '01299998888',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->clinicUser->refresh();
        $this->assertEquals('د. أحمد محمود المحدّث', $this->clinicUser->name);
        $this->assertEquals('updated.doctor@clinic.com', $this->clinicUser->email);
        $this->assertEquals('01299998888', $this->clinicUser->phone);
    }

    public function test_user_cannot_update_to_an_email_already_taken_by_another_user(): void
    {
        User::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'طبيب آخر',
            'email' => 'other@clinic.com',
            'password' => Hash::make('Password123#'),
            'role' => UserRole::Doctor,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->clinicUser)->put('/clinic/profile/info', [
            'name' => 'د. أحمد',
            'email' => 'other@clinic.com', // Duplicate email
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->clinicUser)->put('/clinic/profile/password', [
            'current_password' => 'Password123#',
            'password' => 'NewSecretPass2026#',
            'password_confirmation' => 'NewSecretPass2026#',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->clinicUser->refresh();
        $this->assertTrue(Hash::check('NewSecretPass2026#', $this->clinicUser->password));
    }

    public function test_user_cannot_change_password_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->clinicUser)->put('/clinic/profile/password', [
            'current_password' => 'WrongPassword!',
            'password' => 'NewSecretPass2026#',
            'password_confirmation' => 'NewSecretPass2026#',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $this->clinicUser->refresh();
        $this->assertTrue(Hash::check('Password123#', $this->clinicUser->password));
    }
}
