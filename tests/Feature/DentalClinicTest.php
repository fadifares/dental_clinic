<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\DentalChart;
use App\Models\Doctor;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Setting;
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

    /**
     * Test doctor's name in doctors table is synchronized when their user name is updated.
     */
    public function test_doctor_name_is_synchronized_when_user_name_changes(): void
    {
        $admin = User::create([
            'name' => 'مدير النظام الرئيسي',
            'email' => 'admin.sync@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $doctorUser = User::create([
            'name' => 'د. محمد المهدي القديم',
            'email' => 'dr.mahdi@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'د. محمد المهدي القديم',
            'email' => 'dr.mahdi@dentalcare.com',
            'speciality' => 'علاج الجذور والعصب',
            'commission_rate' => 30.00,
            'is_active' => true,
        ]);

        // 1. Update user via HTTP request in UserController
        $response = $this->actingAs($admin)->put("/clinic/users/{$doctorUser->id}", [
            'name' => 'د. محمد المهدي البروفيسور',
            'email' => 'dr.mahdi@dentalcare.com',
            'roles' => ['doctor'],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $doctorUser->id,
            'name' => 'د. محمد المهدي البروفيسور',
        ]);
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => 'د. محمد المهدي البروفيسور',
        ]);

        // 2. Direct Eloquent update triggers model event sync
        $doctorUser->refresh();
        $doctorUser->name = 'د. محمد المهدي الاستشاري';
        $doctorUser->save();

        $this->assertEquals('د. محمد المهدي الاستشاري', $doctor->fresh()->name);

        // 3. Test unlinked legacy doctor matching by email gets updated and linked
        $legacyUser = User::create([
            'name' => 'د. سمير القديم',
            'email' => 'dr.samir@dentalcare.com',
            'role' => UserRole::Doctor,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $legacyDoctor = Doctor::create([
            'user_id' => null,
            'name' => 'د. سمير القديم',
            'email' => 'dr.samir@dentalcare.com',
            'speciality' => 'تقويم أسنان',
            'commission_rate' => 25.00,
            'is_active' => true,
        ]);

        $legacyUser->update(['name' => 'د. سمير الجديد']);
        $this->assertEquals('د. سمير الجديد', $legacyDoctor->fresh()->name);
        $this->assertEquals($legacyUser->id, $legacyDoctor->fresh()->user_id);
    }

    /**
     * Test clinic settings page, currency, and payment methods configuration.
     */
    public function test_settings_page_and_currency_payment_methods_management(): void
    {
        $admin = User::create([
            'name' => 'مدير النظام المالي',
            'email' => 'admin.settings@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $receptionist = User::create([
            'name' => 'موظفة استقبال',
            'email' => 'reception.settings@dentalcare.com',
            'role' => UserRole::Receptionist,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        // 1. Non-admin is forbidden (403)
        $this->actingAs($receptionist)->get('/clinic/settings')->assertStatus(403);

        // 2. Admin can access settings page
        $response = $this->actingAs($admin)->get('/clinic/settings');
        $response->assertStatus(200);
        $response->assertSee('إعدادات النظام والعيادة');
        $response->assertSee('العملة والتهيئة المالية');
        $response->assertSee('قنوات وطرق الدفع والتحصيل');

        // 3. Admin can update currency and payment methods
        $responseUpdate = $this->actingAs($admin)->put('/clinic/settings', [
            'currency_symbol' => 'ر.س',
            'currency_name' => 'ريال سعودي',
            'currency_code' => 'SAR',
            'currency_position' => 'after',
            'default_payment_method' => 'card',
            'clinic_name' => 'مركز ابتسامة المستقبل لطب الأسنان',
            'clinic_phone' => '0555555555',
            'tax_number' => '310999999900003',
            'tax_rate' => 15.00,
            'methods' => [
                'cash' => [
                    'enabled' => '1',
                    'name' => 'نقداً (كاش)',
                    'description' => 'دفع مباشر',
                ],
                'card' => [
                    'enabled' => '1',
                    'name' => 'مدى وبطاقات بنكية',
                    'description' => 'أجهزة نقاط البيع',
                ],
                'bank_transfer' => [
                    'enabled' => '1',
                    'name' => 'حوالة بنكية سريعة',
                    'description' => 'حساب الراجحي والأهلي',
                ],
                'installments' => [
                    'enabled' => '0',
                    'name' => 'أقساط',
                    'description' => 'معطلة مؤقتاً',
                ],
            ],
        ]);

        $responseUpdate->assertSessionHas('success');

        // Verify stored settings and helper methods
        $this->assertEquals('ر.س', Setting::currencySymbol());
        $this->assertEquals('SAR', Setting::currencyCode());
        $this->assertEquals('ريال سعودي', Setting::currencyName());
        $this->assertEquals('1,500.00 ر.س', Setting::formatMoney(1500));

        $paymentMethods = Setting::paymentMethods();
        $this->assertTrue($paymentMethods['card']['enabled']);
        $this->assertTrue($paymentMethods['card']['is_default']);
        $this->assertFalse($paymentMethods['installments']['enabled']);
        $this->assertEquals('مدى وبطاقات بنكية', $paymentMethods['card']['name']);
    }

    /**
     * Test invoice and receipt voucher printouts display dynamic clinic settings information.
     */
    public function test_invoice_and_receipt_voucher_display_clinic_settings_information(): void
    {
        Setting::set('clinic_name', 'مجمع النخبة التخصصي لطب الأسنان', 'clinic');
        Setting::set('tax_number', '399999999900003', 'clinic');
        Setting::set('clinic_phone', '+966 11 222 3333', 'clinic');
        Setting::set('clinic_address', 'طريق الأمير محمد بن عبدالعزيز، الرياض', 'clinic');
        Setting::set('currency_symbol', 'ر.س', 'financial');

        $admin = User::create([
            'name' => 'مدير الحسابات',
            'email' => 'billing.test@dentalcare.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'name' => 'سلطان بن عبدالعزيز',
            'phone' => '0501112233',
            'gender' => 'male',
            'date_of_birth' => '1990-05-15',
            'file_number' => 'PT-9988',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-9988',
            'patient_id' => $patient->id,
            'subtotal' => 1200.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 1200.00,
            'paid_amount' => 1200.00,
            'remaining_amount' => 0.00,
            'payment_method' => 'card',
            'status' => 'paid',
        ]);

        // 1. Official Tax Invoice View
        $responseInvoice = $this->actingAs($admin)->get("/clinic/billing/{$invoice->id}");
        $responseInvoice->assertStatus(200);
        $responseInvoice->assertSee('مجمع النخبة التخصصي لطب الأسنان');
        $responseInvoice->assertSee('399999999900003');
        $responseInvoice->assertSee('+966 11 222 3333');
        $responseInvoice->assertSee('طريق الأمير محمد بن عبدالعزيز، الرياض');
        $responseInvoice->assertSee('فاتورة ضريبية رسمية');
        $responseInvoice->assertSee('1,200.00 ر.س');

        // 2. Official Receipt Voucher View
        $responseReceipt = $this->actingAs($admin)->get("/clinic/billing/{$invoice->id}?type=receipt");
        $responseReceipt->assertStatus(200);
        $responseReceipt->assertSee('مجمع النخبة التخصصي لطب الأسنان');
        $responseReceipt->assertSee('399999999900003');
        $responseReceipt->assertSee('+966 11 222 3333');
        $responseReceipt->assertSee('سند قـبـض مـالـي مـعـتـمـد');
        $responseReceipt->assertSee('REC-2026-9988');
        $responseReceipt->assertSee('سلطان بن عبدالعزيز');
        $responseReceipt->assertSee('1,200.00');
    }
}
