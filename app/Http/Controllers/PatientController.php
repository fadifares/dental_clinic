<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index(Request $request): View
    {
        $query = Patient::withCount(['appointments', 'invoices', 'dentalCharts'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('file_number', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        $patients = $query->paginate(15)->withQueryString();
        $totalPatientsCount = Patient::count();
        $allergiesCount = Patient::whereNotNull('allergies')->where('allergies', '!=', '')->count();

        return view('clinic.patients.index', compact('patients', 'totalPatientsCount', 'allergiesCount'));
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:30',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
        ]);

        // Generate unique file number
        $lastId = (Patient::max('id') ?? 1000) + 1;
        $validated['file_number'] = 'PT-'.$lastId;

        $patient = Patient::create($validated);

        return redirect()->route('clinic.patients.show', $patient)
            ->with('success', 'تم تسجيل ملف المريض بنجاح.');
    }

    /**
     * Display the specified patient's full EMR profile.
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'dentalCharts',
            'appointments.doctor',
            'labOrders.doctor',
            'invoices.doctor',
        ]);

        $doctors = Doctor::where('is_active', true)->get();

        // Map existing dental conditions by tooth number (FDI 11..48)
        $patientToothConditions = [];
        foreach ($patient->dentalCharts as $chart) {
            $patientToothConditions[$chart->tooth_number] = $chart->condition;
        }

        $totalInvoiced = $patient->invoices->sum('total');
        $totalPaid = $patient->invoices->sum('paid_amount');
        $totalRemaining = $patient->invoices->sum('remaining_amount');

        return view('clinic.patients.show', compact(
            'patient',
            'doctors',
            'patientToothConditions',
            'totalInvoiced',
            'totalPaid',
            'totalRemaining'
        ));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:30',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
        ]);

        $patient->update($validated);

        return back()->with('success', 'تم تحديث بيانات ملف المريض بنجاح.');
    }

    /**
     * Live search for Topbar auto-suggest.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        if (! $query || strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('file_number', 'like', "%{$query}%")
            ->take(8)
            ->get(['id', 'file_number', 'name', 'phone']);

        return response()->json($patients);
    }
}
