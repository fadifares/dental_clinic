<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        if ($request->wantsJson()) {
            $appointment->load(['patient', 'doctor']);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الموعد بنجاح.',
                'appointment' => $appointment,
            ]);
        }

        return back()->with('success', 'تم حجز الموعد بنجاح.');
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
        $appointment->delete();

        return back()->with('success', 'تم إلغاء الموعد بنجاح.');
    }
}
