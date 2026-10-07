<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\LabOrder;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabOrderController extends Controller
{
    /**
     * Display a listing of dental lab orders.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $query = LabOrder::with(['patient', 'doctor'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $labOrders = $query->paginate(15)->withQueryString();
        $patients = Patient::select('id', 'name', 'phone', 'file_number')->latest()->get();
        $doctors = Doctor::activeDoctors()->get();

        $stats = [
            'total' => LabOrder::count(),
            'in_progress' => LabOrder::where('status', 'in_progress')->count(),
            'ready' => LabOrder::where('status', 'ready')->count(),
            'delivered' => LabOrder::where('status', 'delivered')->count(),
        ];

        return view('clinic.labs.index', compact('labOrders', 'patients', 'doctors', 'stats'));
    }

    /**
     * Store a newly created lab order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'lab_name' => 'required|string|max:255',
            'item_type' => 'required|string|max:255',
            'tooth_numbers' => 'nullable|string|max:50',
            'shade' => 'nullable|string|max:20',
            'cost' => 'required|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        LabOrder::create(array_merge($validated, [
            'order_date' => now()->toDateString(),
            'status' => 'sent',
        ]));

        return back()->with('success', 'تم تسجيل طلبية المعمل وإرسالها بنجاح.');
    }

    /**
     * Update lab order status.
     */
    public function updateStatus(Request $request, LabOrder $labOrder): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:sent,in_progress,ready,delivered,cancelled',
        ]);

        $labOrder->update(['status' => $validated['status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة طلبية المعمل.',
                'status' => $labOrder->status,
            ]);
        }

        return back()->with('success', 'تم تحديث حالة الطلبية بنجاح.');
    }
}
