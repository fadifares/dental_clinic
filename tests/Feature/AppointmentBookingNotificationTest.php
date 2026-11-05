<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentBookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $receptionist;

    protected Doctor $doctor;

    protected Patient $patientWithEmail;

    protected Patient $patientWithoutEmail;

    protected function setUp(): void
    {
        parent::setUp();

        Clinic::firstOrCreate(['id' => 1], [
            'name' => 'مركز Dental Pro لطب الأسنان',
            'slug' => 'dental-pro',
            'status' => 'active',
            'max_users' => 50,
            'address' => 'شارع التخصصي، الرياض',
        ]);

        $this->receptionist = User::create([
            'name' => 'موظف الاستقبال',
            'email' => 'reception@example.com',
            'role' => UserRole::Receptionist,
            'password' => bcrypt('Password123#'),
            'is_active' => true,
        ]);

        $this->doctor = Doctor::create([
            'name' => 'أحمد النجار',
            'speciality' => 'استشاري جراحة وزراعة الأسنان',
            'phone' => '0551122334',
            'is_active' => true,
        ]);

        $this->patientWithEmail = Patient::create([
            'name' => 'فهد عبدالعزيز العتيبي',
            'phone' => '0501234567',
            'email' => 'fahad.patient@example.com',
            'file_number' => 'PT-5001',
            'gender' => 'male',
        ]);

        $this->patientWithoutEmail = Patient::create([
            'name' => 'سارة الشريف',
            'phone' => '01012345678', // Egypt number format
            'email' => null,
            'file_number' => 'PT-5002',
            'gender' => 'female',
        ]);
    }

    /**
     * Test booking appointment sends confirmation email with calendar invite (.ics) having 1-day reminder.
     */
    public function test_booking_appointment_sends_email_with_calendar_invite_and_one_day_reminder(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->receptionist)
            ->post(route('clinic.appointments.store'), [
                'patient_id' => $this->patientWithEmail->id,
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->addDays(3)->toDateString(),
                'appointment_time' => '14:30',
                'service_type' => 'جلسة علاج عصب ثانية',
                'notes' => 'المريض يعاني من ألم خفيف',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('whatsapp_url');
        $response->assertSessionHas('email_sent', true);
        $response->assertSessionHas('whatsapp_patient_phone', '0501234567');

        Mail::assertSent(AppointmentConfirmationMail::class, function ($mail) {
            $appointment = $mail->appointment;
            $this->assertEquals($this->patientWithEmail->id, $appointment->patient_id);

            // Assert mail has iCalendar .ics attachment
            $attachments = $mail->attachments();
            $this->assertNotEmpty($attachments);

            // Generate and verify .ics contents
            $ics = $appointment->generateIcsContent();
            $this->assertStringContainsString('BEGIN:VCALENDAR', $ics);
            $this->assertStringContainsString('METHOD:REQUEST', $ics);
            $this->assertStringContainsString('BEGIN:VALARM', $ics);
            $this->assertStringContainsString('TRIGGER:-P1D', $ics); // 1-day before reminder
            $this->assertStringContainsString('جلسة علاج عصب ثانية', $ics);

            return $mail->hasTo('fahad.patient@example.com');
        });
    }

    /**
     * Test booking appointment creates WhatsApp URL with normalized phone number and Arabic text.
     */
    public function test_booking_appointment_generates_correct_whatsapp_url(): void
    {
        Mail::fake();

        $appointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '11:00',
            'service_type' => 'تركيب فينير تجميلي',
            'status' => 'scheduled',
        ]);

        $url = $appointment->getWhatsAppUrl();

        // 0501234567 should be normalized to 966501234567
        $this->assertStringStartsWith('https://wa.me/966501234567?text=', $url);
        $this->assertStringContainsString(rawurlencode('تأكيد حجز موعد طبي'), $url);
        $this->assertStringContainsString(rawurlencode('فهد عبدالعزيز العتيبي'), $url);
    }

    /**
     * Test WhatsApp URL normalization for Egyptian numbers.
     */
    public function test_whatsapp_url_normalization_for_egypt_numbers(): void
    {
        $appointment = Appointment::create([
            'patient_id' => $this->patientWithoutEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(1)->toDateString(),
            'appointment_time' => '16:00',
            'service_type' => 'كشف دوري وتنظيف جير',
            'status' => 'scheduled',
        ]);

        $url = $appointment->getWhatsAppUrl();

        // 01012345678 should be normalized to 201012345678
        $this->assertStringStartsWith('https://wa.me/201012345678?text=', $url);
    }

    /**
     * Test booking appointment for patient without email does not fail and indicates email_sent=false.
     */
    public function test_booking_appointment_without_patient_email_succeeds_without_error(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->receptionist)
            ->post(route('clinic.appointments.store'), [
                'patient_id' => $this->patientWithoutEmail->id,
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->addDay()->toDateString(),
                'appointment_time' => '17:00',
                'service_type' => 'خلع ضرس عقل',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('email_sent', false);
        $response->assertSessionHas('whatsapp_url');

        Mail::assertNothingSent();
    }

    /**
     * Test JSON AJAX appointment booking returns whatsapp_url and email status.
     */
    public function test_ajax_appointment_booking_returns_json_with_whatsapp_url(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->receptionist)
            ->postJson(route('clinic.appointments.store'), [
                'patient_id' => $this->patientWithEmail->id,
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->addDays(4)->toDateString(),
                'appointment_time' => '15:00',
                'service_type' => 'تبييض أسنان بجهاز زووم',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'email_sent' => true,
        ]);
        $response->assertJsonStructure([
            'appointment',
            'whatsapp_url',
            'email_sent',
            'patient_name',
            'patient_phone',
        ]);
    }

    /**
     * Test AppointmentReminderMail also attaches the .ics calendar file.
     */
    public function test_appointment_reminder_mail_contains_ics_attachment(): void
    {
        $appointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '10:00',
            'service_type' => 'استشارة تقويم أسنان',
            'status' => 'scheduled',
        ]);

        $mailable = new AppointmentReminderMail($appointment);
        $attachments = $mailable->attachments();

        $this->assertNotEmpty($attachments);
    }

    /**
     * Test updating/rescheduling an existing future appointment.
     */
    public function test_updating_future_appointment_modifies_record_and_resends_calendar_invite(): void
    {
        Mail::fake();

        $appointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00',
            'service_type' => 'كشف أولي',
            'status' => 'scheduled',
        ]);

        $newDate = now()->addDays(5)->toDateString();

        $response = $this->actingAs($this->receptionist)
            ->put(route('clinic.appointments.update', $appointment), [
                'doctor_id' => $this->doctor->id,
                'appointment_date' => $newDate,
                'appointment_time' => '15:30',
                'service_type' => 'جلسة حشو تجميلي معدلة',
                'notes' => 'تم تغيير الموعد بناء على طلب المريض',
                'status' => 'scheduled',
                'notify_patient' => '1',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('email_sent', true);
        $response->assertSessionHas('whatsapp_url');

        $appointment->refresh();
        $this->assertEquals($newDate, $appointment->appointment_date->toDateString());
        $this->assertEquals('15:30', $appointment->appointment_time);
        $this->assertEquals('جلسة حشو تجميلي معدلة', $appointment->service_type);

        Mail::assertSent(AppointmentConfirmationMail::class, function ($mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id
                && $mail->hasTo('fahad.patient@example.com');
        });
    }

    /**
     * Test AJAX updating appointment returns JSON response.
     */
    public function test_ajax_updating_appointment_returns_json(): void
    {
        Mail::fake();

        $appointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '11:00',
            'service_type' => 'تنظيف جير',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->receptionist)
            ->putJson(route('clinic.appointments.update', $appointment), [
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->addDays(4)->toDateString(),
                'appointment_time' => '12:00',
                'service_type' => 'تنظيف جير وتلميع',
                'status' => 'scheduled',
                'notify_patient' => '0',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'email_sent' => false,
        ]);
    }

    /**
     * Test past appointment cannot be updated and returns error.
     */
    public function test_past_appointment_cannot_be_updated(): void
    {
        $pastAppointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->subDays(2)->toDateString(),
            'appointment_time' => '10:00',
            'service_type' => 'كشف قديم',
            'status' => 'scheduled',
        ]);

        $this->assertTrue($pastAppointment->isPast());
        $this->assertFalse($pastAppointment->canBeEdited());

        // Web request
        $response = $this->actingAs($this->receptionist)
            ->put(route('clinic.appointments.update', $pastAppointment), [
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->toDateString(),
                'appointment_time' => '12:00',
                'service_type' => 'محاولة تعديل موعد سابق',
            ]);

        $response->assertSessionHas('error');

        // JSON request
        $jsonResponse = $this->actingAs($this->receptionist)
            ->putJson(route('clinic.appointments.update', $pastAppointment), [
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->toDateString(),
                'appointment_time' => '12:00',
                'service_type' => 'محاولة تعديل موعد سابق',
            ]);

        $jsonResponse->assertStatus(422);
        $jsonResponse->assertJson(['success' => false]);
    }

    /**
     * Test past appointment cannot be deleted.
     */
    public function test_past_appointment_cannot_be_deleted(): void
    {
        $pastAppointment = Appointment::create([
            'patient_id' => $this->patientWithEmail->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->subDays(5)->toDateString(),
            'appointment_time' => '09:00',
            'service_type' => 'كشف سابق',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->receptionist)
            ->delete(route('clinic.appointments.destroy', $pastAppointment));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('appointments', ['id' => $pastAppointment->id]);
    }
}
