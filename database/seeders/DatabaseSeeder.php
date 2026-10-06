<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\DentalChart;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic dental clinic data.
     */
    public function run(): void
    {
        // 1. Multi-Role System Users
        User::updateOrCreate(
            ['email' => 'admin@dentalcare.com'],
            [
                'name' => 'د. أحمد السالم',
                'role' => UserRole::Admin,
                'roles' => ['admin'],
                'password' => Hash::make('Password123#'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'doctor@dentalcare.com'],
            [
                'name' => 'د. سارة المنصور',
                'role' => UserRole::Doctor,
                'roles' => ['doctor'],
                'password' => Hash::make('Password123#'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'reception@dentalcare.com'],
            [
                'name' => 'منى العتيبي (الاستقبال)',
                'role' => UserRole::Receptionist,
                'roles' => ['receptionist'],
                'password' => Hash::make('Password123#'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'accounting@dentalcare.com'],
            [
                'name' => 'طارق الحربي (المحاسب)',
                'role' => UserRole::Accountant,
                'roles' => ['accountant'],
                'password' => Hash::make('Password123#'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dual@dentalcare.com'],
            [
                'name' => 'هند الدوسري (استقبال ومحاسبة)',
                'role' => UserRole::Receptionist,
                'roles' => ['receptionist', 'accountant'],
                'password' => Hash::make('Password123#'),
                'is_active' => true,
            ]
        );

        // 2. Doctors
        $drAhmed = Doctor::create([
            'name' => 'د. أحمد السالم',
            'speciality' => 'استشاري جراحة وزراعة الأسنان',
            'phone' => '0501112233',
            'email' => 'dr.ahmed@dentalcare.com',
            'commission_rate' => 35.00,
            'is_active' => true,
        ]);

        $drSara = Doctor::create([
            'name' => 'د. سارة المنصور',
            'speciality' => 'أخصائية تجميل وابتسامة هوليود',
            'phone' => '0502223344',
            'email' => 'dr.sara@dentalcare.com',
            'commission_rate' => 30.00,
            'is_active' => true,
        ]);

        $drKhalid = Doctor::create([
            'name' => 'د. خالد القحطاني',
            'speciality' => 'استشاري تقويم وتعديل الفكين',
            'phone' => '0503334455',
            'email' => 'dr.khalid@dentalcare.com',
            'commission_rate' => 30.00,
            'is_active' => true,
        ]);

        $drReem = Doctor::create([
            'name' => 'د. ريم الشمري',
            'speciality' => 'أخصائية طب أسنان الأطفال',
            'phone' => '0504445566',
            'email' => 'dr.reem@dentalcare.com',
            'commission_rate' => 25.00,
            'is_active' => true,
        ]);

        // 3. Patients
        $patient1 = Patient::create([
            'file_number' => 'PT-1049',
            'name' => 'فهد عبدالعزيز العتيبي',
            'phone' => '0501234567',
            'national_id' => '1088776655',
            'gender' => 'male',
            'date_of_birth' => '1990-05-14',
            'medical_history' => 'لا يعاني من أمراض مزمنة',
            'allergies' => 'حساسية مفرطة من البنسلين ومشتقاته',
            'chronic_diseases' => null,
        ]);

        $patient2 = Patient::create([
            'file_number' => 'PT-1050',
            'name' => 'سارة محمد الشهري',
            'phone' => '0559876543',
            'national_id' => '1077665544',
            'gender' => 'female',
            'date_of_birth' => '1995-11-20',
            'medical_history' => 'سليمة ولله الحمد',
            'allergies' => null,
            'chronic_diseases' => null,
        ]);

        $patient3 = Patient::create([
            'file_number' => 'PT-1035',
            'name' => 'يوسف إبراهيم الدوسري',
            'phone' => '0563334444',
            'national_id' => '1066554433',
            'gender' => 'male',
            'date_of_birth' => '2001-03-08',
            'medical_history' => 'مريض تقويم مستمر منذ 5 أشهر',
            'allergies' => null,
            'chronic_diseases' => null,
        ]);

        $patient4 = Patient::create([
            'file_number' => 'PT-1052',
            'name' => 'ريان طارق الغامدي',
            'phone' => '0542221111',
            'national_id' => '1199887766',
            'gender' => 'male',
            'date_of_birth' => '2018-09-12',
            'medical_history' => 'طفل - فحص دوري وسد شقوق',
            'allergies' => null,
            'chronic_diseases' => null,
        ]);

        // 4. Appointments (Today)
        Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '09:30:00',
            'service_type' => 'تركيب تاج زيركون',
            'status' => 'in_consultation',
            'notes' => 'جلسة تركيب التاج بعد التئام غرسة السن 46',
        ]);

        Appointment::create([
            'patient_id' => $patient2->id,
            'doctor_id' => $drSara->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '10:15:00',
            'service_type' => 'جلسة تبييض ليزر',
            'status' => 'waiting',
            'notes' => 'تبييض الأسنان بالليزر البارد',
        ]);

        Appointment::create([
            'patient_id' => $patient3->id,
            'doctor_id' => $drKhalid->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '11:00:00',
            'service_type' => 'شد تقويم دوري',
            'status' => 'scheduled',
            'notes' => 'تبديل الأسلاك والمطاط للفكين',
        ]);

        Appointment::create([
            'patient_id' => $patient4->id,
            'doctor_id' => $drReem->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '11:45:00',
            'service_type' => 'حشوة ضرس لبني وفلورايد',
            'status' => 'scheduled',
            'notes' => 'تطبيق جل الفلورايد بنكهة الفراولة',
        ]);

        // 5. Dental Chart Records for Patient 1
        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 16,
            'condition' => 'caries',
            'surfaces' => 'MO',
            'notes' => 'تسوس عميق في السطح الإطباقي',
        ]);

        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 14,
            'condition' => 'filled',
            'surfaces' => 'O',
            'notes' => 'حشوة كمبوزيت ممتازة',
        ]);

        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 21,
            'condition' => 'crown',
            'notes' => 'تاج إيماكس تجميلي',
        ]);

        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 26,
            'condition' => 'rct',
            'notes' => 'علاج عصب مكتمل مع حشوة قنوات',
        ]);

        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 46,
            'condition' => 'implant',
            'notes' => 'زراعة تيتانيوم ألماني 4.2mm',
        ]);

        DentalChart::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'tooth_number' => 47,
            'condition' => 'missing',
            'notes' => 'مخلوع مسبقاً',
        ]);

        // 6. Lab Orders
        LabOrder::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'lab_name' => 'معمل النخبة لطب الأسنان',
            'item_type' => 'تاج زيركون كامل',
            'tooth_numbers' => '16',
            'shade' => 'A2',
            'cost' => 450.00,
            'order_date' => now()->subDays(3)->toDateString(),
            'expected_delivery_date' => now()->toDateString(),
            'status' => 'ready',
            'notes' => 'تاج عالي الشفافية ومطابق للمقاس الرقمي',
        ]);

        LabOrder::create([
            'patient_id' => $patient2->id,
            'doctor_id' => $drSara->id,
            'lab_name' => 'معمل الرواد للتركيبات',
            'item_type' => 'جسر إيماكس 3 وحدات',
            'tooth_numbers' => '11, 12, 13',
            'shade' => 'BL2',
            'cost' => 1200.00,
            'order_date' => now()->subDays(2)->toDateString(),
            'expected_delivery_date' => now()->addDays(2)->toDateString(),
            'status' => 'in_progress',
            'notes' => 'فينير وتاج أمامي فائق البياض',
        ]);

        // 7. Invoices
        Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'patient_id' => $patient1->id,
            'doctor_id' => $drAhmed->id,
            'subtotal' => 3500.00,
            'discount' => 200.00,
            'tax' => 0.00,
            'total' => 3300.00,
            'paid_amount' => 3300.00,
            'remaining_amount' => 0.00,
            'payment_method' => 'card',
            'status' => 'paid',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2026-0002',
            'patient_id' => $patient2->id,
            'doctor_id' => $drSara->id,
            'subtotal' => 1200.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 1200.00,
            'paid_amount' => 800.00,
            'remaining_amount' => 400.00,
            'payment_method' => 'cash',
            'status' => 'partially_paid',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2026-0003',
            'patient_id' => $patient3->id,
            'doctor_id' => $drKhalid->id,
            'subtotal' => 1500.00,
            'discount' => 100.00,
            'tax' => 0.00,
            'total' => 1400.00,
            'paid_amount' => 1400.00,
            'remaining_amount' => 0.00,
            'payment_method' => 'card',
            'status' => 'paid',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2026-0004',
            'patient_id' => $patient4->id,
            'doctor_id' => $drReem->id,
            'subtotal' => 450.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => 450.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 450.00,
            'payment_method' => 'cash',
            'status' => 'unpaid',
        ]);
    }
}
