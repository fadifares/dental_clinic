<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DentalChart;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicDashboardController extends Controller
{
    /**
     * Display the clinic operations ERP dashboard.
     */
    public function index(): View
    {
        $today = now()->toDateString();

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        $totalAppointmentsCount = $todayAppointments->count();
        $waitingCount = $todayAppointments->where('status', 'waiting')->count();
        $todayCollection = Invoice::whereDate('created_at', $today)->sum('paid_amount');
        $activeLabOrdersCount = LabOrder::whereIn('status', ['sent', 'in_progress', 'ready'])->count();

        $labOrders = LabOrder::with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        $activePatient = Patient::with('dentalCharts')->first();
        $doctors = Doctor::where('is_active', true)->get();

        // Key-value array of tooth conditions for active patient e.g. [16 => 'caries', 14 => 'filled']
        $patientToothConditions = [];
        if ($activePatient) {
            foreach ($activePatient->dentalCharts as $chart) {
                $patientToothConditions[$chart->tooth_number] = $chart->condition;
            }
        }

        return view('clinic.dashboard', compact(
            'todayAppointments',
            'totalAppointmentsCount',
            'waitingCount',
            'todayCollection',
            'activeLabOrdersCount',
            'labOrders',
            'activePatient',
            'doctors',
            'patientToothConditions'
        ));
    }

    /**
     * Update tooth condition in dental chart via AJAX (jQuery).
     */
    public function updateTooth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tooth_number' => 'required|integer|between:11,48',
            'condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $chart = DentalChart::updateOrCreate(
            [
                'patient_id' => $validated['patient_id'],
                'tooth_number' => $validated['tooth_number'],
            ],
            [
                'condition' => $validated['condition'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ حالة السن بنجاح في السجل الطبي',
            'chart' => $chart,
        ]);
    }

    /**
     * Change appointment status via AJAX (jQuery).
     */
    public function updateStatus(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,waiting,in_consultation,completed,cancelled',
        ]);

        $appointment->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الموعد',
            'status' => $appointment->status,
        ]);
    }

    /**
     * Store new clinic appointment via AJAX (jQuery).
     */
    public function storeAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:30',
            'doctor_id' => 'required|exists:doctors,id',
            'service_type' => 'required|string',
            'appointment_time' => 'required',
        ]);

        // Find or create patient
        $patient = Patient::firstOrCreate(
            ['phone' => $validated['patient_phone']],
            [
                'name' => $validated['patient_name'],
                'file_number' => 'PT-'.rand(1100, 9999),
            ]
        );

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => now()->toDateString(),
            'appointment_time' => $validated['appointment_time'],
            'service_type' => $validated['service_type'],
            'status' => 'scheduled',
        ]);

        $appointment->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الموعد بنجاح في قاعدة البيانات',
            'appointment' => $appointment,
        ]);
    }
}
