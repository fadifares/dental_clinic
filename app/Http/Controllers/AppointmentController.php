<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments with filters.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $doctorId = $request->input('doctor_id');
        $status = $request->input('status');

        $query = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $selectedDate)
            ->orderBy('appointment_time');

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $appointments = $query->get();
        $doctors = Doctor::where('is_active', true)->get();
        $patients = Patient::select('id', 'name', 'phone', 'file_number')->latest()->get();

        $stats = [
            'total' => $appointments->count(),
            'waiting' => $appointments->where('status', 'waiting')->count(),
            'in_consultation' => $appointments->where('status', 'in_consultation')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
        ];

        return view('clinic.appointments.index', compact(
            'appointments',
            'doctors',
            'patients',
            'selectedDate',
            'stats'
        ));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        // Gracefully support finding/creating patient if quick-booked from dashboard with patient_name & phone
        if (! $request->filled('patient_id') && $request->filled('patient_name') && $request->filled('patient_phone')) {
            $patient = Patient::firstOrCreate(
                ['phone' => $request->input('patient_phone')],
                [
                    'name' => $request->input('patient_name'),
                    'file_number' => 'PT-'.((Patient::max('id') ?? 1000) + 1),
                    'gender' => 'male',
                ]
            );
            $request->merge(['patient_id' => $patient->id]);
        }

        if (! $request->filled('appointment_date')) {
            $request->merge(['appointment_date' => now()->toDateString()]);
        }

        if (! $request->filled('service_type')) {
            $request->merge(['service_type' => 'كشف واستشارة طبية']);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create(array_merge($validated, [
            'status' => 'scheduled',
        ]));

        $appointment->load(['patient', 'doctor', 'clinic']);

        $patient = $appointment->patient;
        $emailSent = false;

        // 1. Immediately send Calendar Invite (.ics with 1-day advance reminder) if patient has email
        if ($patient && ! empty($patient->email)) {
            try {
                Mail::to($patient->email)->send(new AppointmentConfirmationMail($appointment));
                $emailSent = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // 2. Build WhatsApp direct URL for patient's registered phone
        $whatsappUrl = $appointment->getWhatsAppUrl();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الموعد بنجاح'.($emailSent ? ' وإرسال دعوة التقويم لبريد المريض.' : '.'),
                'appointment' => $appointment,
                'whatsapp_url' => $whatsappUrl,
                'email_sent' => $emailSent,
                'patient_name' => $patient?->name,
                'patient_phone' => $patient?->phone,
            ]);
        }

        return back()
            ->with('success', 'تم حجز الموعد بنجاح'.($emailSent ? ' وتم إرسال بطاقة التقويم التفاعلية (.ics) إلى بريد المريض.' : '.'))
            ->with('whatsapp_url', $whatsappUrl)
            ->with('whatsapp_patient_name', $patient?->name)
            ->with('whatsapp_patient_phone', $patient?->phone)
            ->with('email_sent', $emailSent)
            ->with('patient_email', $patient?->email)
            ->with('appointment_date', $appointment->appointment_date?->format('Y-m-d'))
            ->with('appointment_time', $appointment->appointment_time)
            ->with('doctor_name', $appointment->doctor?->name)
            ->with('service_type', $appointment->service_type);
    }

    /**
     * Update an existing appointment (reschedule, change doctor, service, or notes).
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse|JsonResponse
    {
        if ($appointment->isPast()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تعديل المواعيد السابقة، المواعيد السابقة مخصصة للأرشفة والاطلاع فقط.',
                ], 422);
            }

            return back()->with('error', 'لا يمكن تعديل المواعيد السابقة، المواعيد السابقة مخصصة للأرشفة والاطلاع فقط.');
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,waiting,in_consultation,completed,cancelled,no_show',
        ]);

        $appointment->update($validated);
        $appointment->load(['patient', 'doctor', 'clinic']);

        $patient = $appointment->patient;
        $emailSent = false;

        // If patient has an email and user opted to notify
        if ($request->boolean('notify_patient', true) && $patient && ! empty($patient->email)) {
            try {
                Mail::to($patient->email)->send(new AppointmentConfirmationMail($appointment));
                $emailSent = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $whatsappUrl = $appointment->getWhatsAppUrl();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل الموعد بنجاح'.($emailSent ? ' وإرسال التحديث لبريد المريض.' : '.'),
                'appointment' => $appointment,
                'whatsapp_url' => $whatsappUrl,
                'email_sent' => $emailSent,
                'patient_name' => $patient?->name,
                'patient_phone' => $patient?->phone,
            ]);
        }

        return back()
            ->with('success', 'تم تعديل وتحديث بيانات الموعد بنجاح'.($emailSent ? ' وإرسال بطاقة التقويم المحدثة لبريد المريض.' : '.'))
            ->with('whatsapp_url', $whatsappUrl)
            ->with('whatsapp_patient_name', $patient?->name)
            ->with('whatsapp_patient_phone', $patient?->phone)
            ->with('email_sent', $emailSent)
            ->with('patient_email', $patient?->email)
            ->with('appointment_date', $appointment->appointment_date?->format('Y-m-d'))
            ->with('appointment_time', $appointment->appointment_time)
            ->with('doctor_name', $appointment->doctor?->name)
            ->with('service_type', $appointment->service_type);
    }

    /**
     * Update appointment status via AJAX.
     */
    public function updateStatus(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,waiting,in_consultation,completed,cancelled,no_show',
        ]);

        $appointment->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الموعد بنجاح.',
            'status' => $appointment->status,
        ]);
    }

    /**
     * Delete/cancel an appointment.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        if ($appointment->isPast()) {
            return back()->with('error', 'لا يمكن إلغاء أو حذف المواعيد السابقة المحفوظة في الأرشيف.');
        }

        $appointment->delete();

        return back()->with('success', 'تم إلغاء الموعد بنجاح.');
    }

    /**
     * Send email reminder for an individual appointment on-demand.
     */
    public function sendReminder(Appointment $appointment): RedirectResponse|JsonResponse
    {
        $appointment->load(['patient', 'doctor']);
        $patient = $appointment->patient;

        if (! $patient || empty($patient->email)) {
            $msg = 'المريض لا يملك بريداً إلكترونياً مسجلاً في ملفه الطبي لإرسال التذكير.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        try {
            Mail::to($patient->email)->send(new AppointmentReminderMail($appointment));
            $appointment->update(['reminder_sent_at' => now()]);

            $msg = 'تم إرسال رسالة التذكير بالموعد بنجاح إلى البريد الإلكتروني: '.$patient->email;
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'reminder_sent_at' => now()->format('Y-m-d H:i'),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = 'تعذر إرسال البريد الإلكتروني: '.$e->getMessage();
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }

            return back()->with('error', $msg);
        }
    }
}
