<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DentalChart;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicDashboardController extends Controller
{
    /**
     * Display the clinic operations ERP dashboard tailored to user role.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $today = now()->toDateString();

        // Effective role: selected by user from their assigned roles, or preview for Admin
        $userRoles = $user->getRolesArray();
        $effectiveRole = $userRoles[0] ?? 'receptionist';

        if ($request->has('role_view') && in_array($request->query('role_view'), $userRoles, true)) {
            $effectiveRole = $request->query('role_view');
        } elseif ($user->isAdmin() && $request->has('role_preview')) {
            $preview = $request->query('role_preview');
            if (in_array($preview, ['admin', 'doctor', 'receptionist', 'accountant'], true)) {
                $effectiveRole = $preview;
            }
        }

        // Shared baseline data
        $allDoctors = Doctor::activeDoctors()->get();
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        // 1. Admin Dashboard Data
        $adminData = [];
        if ($effectiveRole === 'admin' || $user->isAdmin()) {
            $adminData = [
                'totalPatientsCount' => Patient::count(),
                'totalAppointmentsCount' => $todayAppointments->count(),
                'waitingCount' => $todayAppointments->where('status', 'waiting')->count(),
                'inConsultationCount' => $todayAppointments->where('status', 'in_consultation')->count(),
                'completedCount' => $todayAppointments->where('status', 'completed')->count(),
                'todayCollection' => Invoice::whereDate('created_at', $today)->sum('paid_amount'),
                'monthCollection' => Invoice::whereMonth('created_at', now()->month)->sum('paid_amount'),
                'totalDoctorsCount' => $allDoctors->count(),
                'totalStaffCount' => User::where('is_active', true)->count(),
                'activeLabOrdersCount' => LabOrder::whereIn('status', ['sent', 'in_progress', 'ready'])->count(),
                'recentInvoices' => Invoice::with(['patient', 'doctor'])->latest()->take(5)->get(),
                'labOrders' => LabOrder::with(['patient', 'doctor'])->latest()->take(5)->get(),
                'doctorsPerformance' => $allDoctors->map(function ($doc) use ($today) {
                    return [
                        'doctor' => $doc,
                        'today_appointments' => Appointment::where('doctor_id', $doc->id)->whereDate('appointment_date', $today)->count(),
                        'completed_today' => Appointment::where('doctor_id', $doc->id)->whereDate('appointment_date', $today)->where('status', 'completed')->count(),
                        'monthly_revenue' => Invoice::where('doctor_id', $doc->id)->whereMonth('created_at', now()->month)->sum('paid_amount'),
                    ];
                }),
            ];
        }

        // 2. Doctor Dashboard Data
        $doctorData = [];
        if ($effectiveRole === 'doctor' || $user->isAdmin()) {
            $currentDoctor = Doctor::where('name', $user->name)
                ->orWhere('email', $user->email)
                ->first() ?? Doctor::first();

            $docTodayApts = Appointment::with(['patient', 'doctor'])
                ->where('doctor_id', $currentDoctor->id)
                ->whereDate('appointment_date', $today)
                ->orderBy('appointment_time')
                ->get();

            // Active chair patient (in consultation or waiting or first)
            $activePatientId = $request->query('patient_id');
            if ($activePatientId) {
                $activePatient = Patient::with('dentalCharts')->find($activePatientId);
            } else {
                $chairApt = $docTodayApts->where('status', 'in_consultation')->first()
                    ?? $docTodayApts->where('status', 'waiting')->first()
                    ?? $docTodayApts->first();
                $activePatient = $chairApt?->patient?->load('dentalCharts') ?? Patient::with('dentalCharts')->first();
            }

            $patientToothConditions = [];
            if ($activePatient) {
                foreach ($activePatient->dentalCharts as $chart) {
                    $patientToothConditions[$chart->tooth_number] = $chart->condition;
                }
            }

            $doctorRevenue = Invoice::where('doctor_id', $currentDoctor->id)->whereMonth('created_at', now()->month)->sum('paid_amount');
            $doctorCommission = $doctorRevenue * ($currentDoctor->commission_rate / 100);

            $doctorData = [
                'currentDoctor' => $currentDoctor,
                'doctorAppointments' => $docTodayApts,
                'doctorWaitingQueue' => $docTodayApts->where('status', 'waiting'),
                'doctorInChair' => $docTodayApts->where('status', 'in_consultation')->first(),
                'activePatient' => $activePatient,
                'patientToothConditions' => $patientToothConditions,
                'doctorLabOrders' => LabOrder::with('patient')->where('doctor_id', $currentDoctor->id)->latest()->take(6)->get(),
                'completedCasesCount' => Appointment::where('doctor_id', $currentDoctor->id)->where('status', 'completed')->whereMonth('appointment_date', now()->month)->count(),
                'monthlyRevenue' => $doctorRevenue,
                'estimatedCommission' => $doctorCommission,
            ];
        }

        // 3. Receptionist Dashboard Data
        $receptionData = [];
        if ($effectiveRole === 'receptionist' || $user->isAdmin()) {
            $receptionData = [
                'waitingQueue' => $todayAppointments->where('status', 'waiting'),
                'inConsultationList' => $todayAppointments->where('status', 'in_consultation'),
                'scheduledList' => $todayAppointments->where('status', 'scheduled'),
                'completedCount' => $todayAppointments->where('status', 'completed')->count(),
                'newPatientsToday' => Patient::whereDate('created_at', $today)->count(),
                'totalToday' => $todayAppointments->count(),
                'doctorsLoad' => $allDoctors->map(function ($doc) use ($today) {
                    $apts = Appointment::with('patient')->where('doctor_id', $doc->id)->whereDate('appointment_date', $today)->get();

                    return [
                        'doctor' => $doc,
                        'current_patient' => $apts->where('status', 'in_consultation')->first()?->patient,
                        'waiting_count' => $apts->where('status', 'waiting')->count(),
                        'scheduled_count' => $apts->where('status', 'scheduled')->count(),
                        'completed_count' => $apts->where('status', 'completed')->count(),
                    ];
                }),
            ];
        }

        // 4. Accountant Dashboard Data
        $accountantData = [];
        if ($effectiveRole === 'accountant' || $user->isAdmin()) {
            $accountantData = [
                'todayCollection' => Invoice::whereDate('created_at', $today)->sum('paid_amount'),
                'monthCollection' => Invoice::whereMonth('created_at', now()->month)->sum('paid_amount'),
                'outstandingDebt' => Invoice::sum('remaining_amount'),
                'totalInvoicesMonth' => Invoice::whereMonth('created_at', now()->month)->count(),
                'recentInvoices' => Invoice::with(['patient', 'doctor'])->latest()->take(10)->get(),
                'unpaidInvoices' => Invoice::with(['patient', 'doctor'])->where('remaining_amount', '>', 0)->latest()->take(6)->get(),
                'cardTotal' => Invoice::where('payment_method', 'card')->whereMonth('created_at', now()->month)->sum('paid_amount'),
                'cashTotal' => Invoice::where('payment_method', 'cash')->whereMonth('created_at', now()->month)->sum('paid_amount'),
                'todayAppointmentsForBilling' => $todayAppointments,
            ];
        }

        // For views that need fallback active patient (odontogram modal)
        $activePatient = $doctorData['activePatient'] ?? Patient::with('dentalCharts')->first();
        $patientToothConditions = $doctorData['patientToothConditions'] ?? [];
        if (empty($patientToothConditions) && $activePatient) {
            foreach ($activePatient->dentalCharts as $chart) {
                $patientToothConditions[$chart->tooth_number] = $chart->condition;
            }
        }

        return view('clinic.dashboard', compact(
            'effectiveRole',
            'today',
            'allDoctors',
            'todayAppointments',
            'adminData',
            'doctorData',
            'receptionData',
            'accountantData',
            'activePatient',
            'patientToothConditions',
            'userRoles'
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
            'message' => 'تم تحديث حالة الموعد بنجاح',
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
