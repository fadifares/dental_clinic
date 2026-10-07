<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\DentalChart;
use App\Models\Doctor;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentalClinicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public dental clinic homepage renders successfully without authentication.
     */
    public function test_public_website_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('دنتال');
        $response->assertSee('حجز موعد كشف أونلاين');
    }

    /**
     * Test unauthenticated access to clinic ERP is redirected to login (Security Check).
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/clinic');

        $response->assertRedirect('/login');
    }

    /**
     * Test login page renders with roles demo switcher.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('تسجيل الدخول');
        $response->assertSee('admin@dentalcare.com');
    }

    /**
     * Test admin can access all clinic sections.
     */
    public function test_admin_can_access_all_sections(): void
    {
        $admin = User::create([
            'name' => 'د. أحمد السالم',
            'email' => 'admin@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $doctor = Doctor::create([
            'name' => 'د. أحمد السالم',
            'speciality' => 'استشاري جراحة وزراعة الأسنان',
        ]);

        $patient = Patient::create([
            'name' => 'فهد العتيبي',
            'file_number' => 'PT-1001',
            'phone' => '0501234567',
        ]);

        $response = $this->actingAs($admin)->get('/clinic');
        $response->assertStatus(200);
        $response->assertSee('لوحة القيادة والتشغيل اليومي');

        $responseBilling = $this->actingAs($admin)->get('/clinic/billing');
        $responseBilling->assertStatus(200);

        $responseLabs = $this->actingAs($admin)->get('/clinic/labs');
        $responseLabs->assertStatus(200);
    }

    /**
     * Test role-based authorization: Receptionist CANNOT access financial billing (403 Forbidden).
     */
    public function test_receptionist_cannot_access_billing_records(): void
    {
        $receptionist = User::create([
            'name' => 'منى العتيبي',
            'email' => 'reception@dentalcare.com',
            'role' => UserRole::Receptionist,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($receptionist)->get('/clinic/billing');

        $response->assertStatus(403);
    }

    /**
     * Test role-based authorization: Accountant CAN access billing, but CANNOT modify odontogram.
     */
    public function test_accountant_can_access_billing_but_cannot_modify_odontogram(): void
    {
        $accountant = User::create([
            'name' => 'طارق الحربي',
            'email' => 'accounting@dentalcare.com',
            'role' => UserRole::Accountant,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'فهد العتيبي',
            'file_number' => 'PT-1001',
            'phone' => '0501234567',
        ]);

        // Can access billing
        $responseBilling = $this->actingAs($accountant)->get('/clinic/billing');
        $responseBilling->assertStatus(200);

        // Cannot update clinical tooth chart (403)
        $responseTooth = $this->actingAs($accountant)->postJson('/clinic/tooth-update', [
            'patient_id' => $patient->id,
            'tooth_number' => 16,
            'condition' => 'crown',
        ]);
        $responseTooth->assertStatus(403);
    }

    /**
     * Test doctor can update tooth condition in dental chart.
     */
    public function test_doctor_can_update_tooth_condition(): void
    {
        $doctorUser = User::create([
            'name' => 'د. سارة المنصور',
            'email' => 'doctor@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'فهد العتيبي',
            'file_number' => 'PT-1001',
            'phone' => '0501234567',
        ]);

        $response = $this->actingAs($doctorUser)->postJson('/clinic/tooth-update', [
            'patient_id' => $patient->id,
            'tooth_number' => 16,
            'condition' => 'crown',
            'notes' => 'Crown restoration',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('dental_charts', [
            'patient_id' => $patient->id,
            'tooth_number' => 16,
            'condition' => 'crown',
        ]);
    }

    /**
     * Test only Admin can access users and permissions module.
     */
    public function test_only_admin_can_access_user_management(): void
    {
        $doctor = User::create([
            'name' => 'د. خالد القحطاني',
            'email' => 'dr.khalid@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'المدير العام',
            'email' => 'admin.chief@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        // Doctor is blocked (403 Forbidden)
        $this->actingAs($doctor)->get('/clinic/users')->assertStatus(403);

        // Admin can access successfully (200 OK)
        $this->actingAs($admin)->get('/clinic/users')->assertStatus(200);
    }

    /**
     * Test admin can create a new user with valid role and password.
     */
    public function test_admin_can_create_new_user(): void
    {
        $admin = User::create([
            'name' => 'المدير العام',
            'email' => 'admin.root@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post('/clinic/users', [
            'name' => 'هدى العيسى',
            'email' => 'huda@dentalcare.com',
            'role' => 'receptionist',
            'password' => 'SecurePass2026#',
        ]);

        $response->assertRedirect('/clinic/users');
        $this->assertDatabaseHas('users', [
            'email' => 'huda@dentalcare.com',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    /**
     * Test admin cannot deactivate their own logged in account.
     */
    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = User::create([
            'name' => 'المدير الأصلي',
            'email' => 'master@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch('/clinic/users/'.$admin->id.'/toggle');
        $response->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    /**
     * Test a user with multiple roles (Receptionist + Accountant) can access both modules.
     */
    public function test_user_with_multiple_roles_can_access_both_reception_and_billing(): void
    {
        $dualUser = User::create([
            'name' => 'هند الدوسري',
            'email' => 'hind.dual@dentalcare.com',
            'password' => bcrypt('Password123#'),
            'roles' => ['receptionist', 'accountant'],
            'is_active' => true,
        ]);

        $this->assertTrue($dualUser->isReceptionist());
        $this->assertTrue($dualUser->isAccountant());
        $this->assertFalse($dualUser->isDoctor());
        $this->assertFalse($dualUser->isAdmin());

        // Can access appointments (as receptionist)
        $this->actingAs($dualUser)->get('/clinic/appointments')->assertStatus(200);

        // Can access billing (as accountant)
        $this->actingAs($dualUser)->get('/clinic/billing')->assertStatus(200);

        // Cannot access lab orders (restricted to doctor/admin) -> 403
        $this->actingAs($dualUser)->get('/clinic/labs')->assertStatus(403);

        // Cannot access user management (restricted to admin) -> 403
        $this->actingAs($dualUser)->get('/clinic/users')->assertStatus(403);
    }

    /**
     * Test admin can create user with multiple roles array.
     */
    public function test_admin_can_create_user_with_multiple_roles(): void
    {
        $admin = User::create([
            'name' => 'المدير العام',
            'email' => 'admin.chief2@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post('/clinic/users', [
            'name' => 'سارة العلي',
            'email' => 'sara.ali@dentalcare.com',
            'roles' => ['receptionist', 'accountant'],
            'password' => 'SecurePass2026#',
        ]);

        $response->assertRedirect('/clinic/users');

        $created = User::where('email', 'sara.ali@dentalcare.com')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->isReceptionist());
        $this->assertTrue($created->isAccountant());
        $this->assertEquals(['receptionist', 'accountant'], $created->getRolesArray());
    }

    /**
     * Test FDI chart renders accurately in patient profile and doctor dashboard views.
     */
    public function test_fdi_chart_renders_correctly_in_patient_and_doctor_views(): void
    {
        $doctorUser = User::create([
            'name' => 'د. حسام الدين',
            'email' => 'dr.hossam@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $doctor = Doctor::create([
            'name' => 'د. حسام الدين',
            'speciality' => 'طب وجراحة الفم والأسنان',
            'email' => 'dr.hossam@dentalcare.com',
            'phone' => '0501112233',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'ماجد الشمري',
            'file_number' => 'PT-FDI-001',
            'phone' => '0555554433',
        ]);

        // Add tooth conditions
        DentalChart::create([
            'patient_id' => $patient->id,
            'tooth_number' => 16,
            'condition' => 'caries',
            'notes' => 'Occlusal decay',
        ]);
        DentalChart::create([
            'patient_id' => $patient->id,
            'tooth_number' => 21,
            'condition' => 'filled',
            'notes' => 'Composite restoration',
        ]);

        // 1. Verify Patient Profile FDI view
        $responsePatient = $this->actingAs($doctorUser)->get("/clinic/patients/{$patient->id}");
        $responsePatient->assertStatus(200);
        $responsePatient->assertSee('data-tooth-id="16"', false);
        $responsePatient->assertSee('data-status="caries"', false);
        $responsePatient->assertSee('data-tooth-id="21"', false);
        $responsePatient->assertSee('data-status="filled"', false);

        // 2. Verify Doctor Dashboard FDI view
        $responseDocDashboard = $this->actingAs($doctorUser)->get("/clinic?patient_id={$patient->id}");
        $responseDocDashboard->assertStatus(200);
        $responseDocDashboard->assertSee('data-tooth-id="16"', false);
        $responseDocDashboard->assertSee('data-status="caries"', false);
        $responseDocDashboard->assertSee('data-tooth-id="21"', false);
        $responseDocDashboard->assertSee('data-status="filled"', false);
    }

    /**
     * Test authorized staff can settle an invoice in full or record partial payment.
     */
    public function test_invoice_settlement_and_partial_payment(): void
    {
        $accountant = User::create([
            'name' => 'طارق المحاسب',
            'email' => 'tariq.acc@dentalcare.com',
            'role' => UserRole::Accountant,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'ريان الغامدي',
            'file_number' => 'PT-TEST-004',
            'phone' => '0540001122',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-TEST',
            'patient_id' => $patient->id,
            'subtotal' => 450.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 450.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 450.00,
            'payment_method' => 'cash',
            'status' => 'unpaid',
        ]);

        // 1. Partial payment: 200 SAR
        $responsePartial = $this->actingAs($accountant)->post("/clinic/billing/{$invoice->id}/payment", [
            'payment_amount' => 200.00,
            'payment_method' => 'card',
        ]);

        $responsePartial->assertSessionHas('success');
        $invoice->refresh();
        $this->assertEquals(200.00, $invoice->paid_amount);
        $this->assertEquals(250.00, $invoice->remaining_amount);
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertEquals('card', $invoice->payment_method);

        // 2. Final settlement: remaining 250 SAR
        $responseFull = $this->actingAs($accountant)->post("/clinic/billing/{$invoice->id}/payment", [
            'payment_amount' => 250.00,
            'payment_method' => 'cash',
        ]);

        $responseFull->assertSessionHas('success');
        $invoice->refresh();
        $this->assertEquals(450.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->remaining_amount);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Test invoice creation and printable A4 invoice receipt view.
     */
    public function test_invoice_creation_and_printable_view(): void
    {
        $accountant = User::create([
            'name' => 'سارة المحاسبة',
            'email' => 'sara.acc@dentalcare.com',
            'role' => UserRole::Accountant,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'محمد أحمد',
            'file_number' => 'PT-TEST-005',
            'phone' => '0551122334',
        ]);

        // 1. Create invoice
        $createResponse = $this->actingAs($accountant)->post('/clinic/billing', [
            'patient_id' => $patient->id,
            'subtotal' => 1200.00,
            'discount' => 200.00,
            'tax' => 0.00,
            'paid_amount' => 500.00,
            'payment_method' => 'card',
        ]);

        $createResponse->assertSessionHas('success');
        $invoice = Invoice::where('patient_id', $patient->id)->latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(1200.00, $invoice->subtotal);
        $this->assertEquals(200.00, $invoice->discount);
        $this->assertEquals(1000.00, $invoice->total);
        $this->assertEquals(500.00, $invoice->paid_amount);
        $this->assertEquals(500.00, $invoice->remaining_amount);
        $this->assertEquals('partially_paid', $invoice->status);

        // 2. View printable A4 invoice
        $showResponse = $this->actingAs($accountant)->get("/clinic/billing/{$invoice->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($invoice->invoice_number);
        $showResponse->assertSee('محمد أحمد');
        $showResponse->assertSee('ج.م');
        $showResponse->assertSee('A4 Portrait');
        $showResponse->assertSee('طباعة الفاتورة (A4)');
    }

    /**
     * Test recording, listing, and deleting clinic expenses.
     */
    public function test_clinic_expenses_management(): void
    {
        $accountant = User::create([
            'name' => 'طارق المحاسب',
            'email' => 'tariq.expenses@dentalcare.com',
            'role' => UserRole::Accountant,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $doctor = User::create([
            'name' => 'د. خالد الطبيب',
            'email' => 'khaled.doc@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        // 1. Doctor is blocked from expenses (403 Forbidden)
        $this->actingAs($doctor)->get('/clinic/expenses')->assertStatus(403);

        // 2. Accountant can view expenses index
        $responseIndex = $this->actingAs($accountant)->get('/clinic/expenses');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('سجل مصاريف ونفقات العيادة');
        $responseIndex->assertSee('تسجيل مصروف جديد');

        // 3. Store a new expense
        $responseStore = $this->actingAs($accountant)->post('/clinic/expenses', [
            'title' => 'شراء كراتين بنج وحشوات كمبوزيت',
            'category' => 'materials',
            'amount' => 850.00,
            'expense_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'invoice_reference' => 'INV-SUPPLIER-101',
            'notes' => 'توريد من شركة المدار للمستلزمات الطبية',
        ]);

        $responseStore->assertSessionHas('success');
        $expense = Expense::where('title', 'شراء كراتين بنج وحشوات كمبوزيت')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(850.00, $expense->amount);
        $this->assertEquals('materials', $expense->category);
        $this->assertEquals('cash', $expense->payment_method);
        $this->assertEquals($accountant->id, $expense->user_id);

        // 4. Delete the expense
        $responseDelete = $this->actingAs($accountant)->delete("/clinic/expenses/{$expense->id}");
        $responseDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
