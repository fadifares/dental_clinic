<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Display a listing of invoices, payments, and financial overview.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $query = Invoice::with(['patient', 'doctor'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        $patients = Patient::select('id', 'name', 'phone', 'file_number')->latest()->get();
        $doctors = Doctor::where('is_active', true)->get();

        $stats = [
            'total_invoiced' => Invoice::sum('total'),
            'total_collected' => Invoice::sum('paid_amount'),
            'total_outstanding' => Invoice::sum('remaining_amount'),
            'count' => Invoice::count(),
        ];

        return view('clinic.billing.index', compact('invoices', 'patients', 'doctors', 'stats'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,installments',
        ]);

        $subtotal = (float) $validated['subtotal'];
        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $total = max(0, $subtotal - $discount + $tax);
        $paid = min($total, (float) $validated['paid_amount']);
        $remaining = max(0, $total - $paid);

        $status = 'paid';
        if ($paid == 0 && $total > 0) {
            $status = 'unpaid';
        } elseif ($remaining > 0) {
            $status = 'partially_paid';
        }

        $lastId = (Invoice::max('id') ?? 1000) + 1;
        $invoiceNumber = 'INV-'.date('Y').'-'.str_pad($lastId, 4, '0', STR_PAD_LEFT);

        Invoice::create([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'] ?? null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'payment_method' => $validated['payment_method'],
            'status' => $status,
        ]);

        return back()->with('success', "تم إصدار الفاتورة الإلكترونية بنجاح برقم #{$invoiceNumber}.");
    }

    /**
     * Display printable invoice receipt.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load(['patient', 'doctor']);

        return view('clinic.billing.show', compact('invoice'));
    }
}
