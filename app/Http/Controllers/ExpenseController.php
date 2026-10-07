<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses, filters, and financial analytics.
     */
    public function index(Request $request): View
    {
        $query = Expense::with('user')->latest('expense_date')->latest('id');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by date range or specific date
        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->input('to_date'));
        }

        // Search in title, notes, or invoice reference
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('invoice_reference', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $today = Carbon::today()->toDateString();

        $monthExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $totalExpenses = Expense::sum('amount');

        // Revenue collected this month to compute Net Cash Flow
        $monthCollections = Invoice::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('paid_amount');
        $netIncomeMonth = $monthCollections - $monthExpenses;

        $stats = [
            'month_expenses' => $monthExpenses,
            'today_expenses' => $todayExpenses,
            'total_expenses' => $totalExpenses,
            'month_collections' => $monthCollections,
            'net_income_month' => $netIncomeMonth,
            'count' => Expense::count(),
        ];

        $categories = [
            'materials' => 'مواد ومستهلكات طبية',
            'rent' => 'إيجار العيادة',
            'utilities' => 'فواتير وكهرباء ومياه',
            'maintenance' => 'صيانة أجهزة ومعدات',
            'salaries' => 'رواتب ومستحقات',
            'marketing' => 'تسويق وإعلانات',
            'other' => 'نثريات ومصاريف أخرى',
        ];

        return view('clinic.expenses.index', compact('expenses', 'stats', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:materials,rent,utilities,maintenance,salaries,marketing,other',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'invoice_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'يرجى كتابة عنوان أو بيان المصروف.',
            'category.required' => 'يرجى اختيار تصنيف المصروف.',
            'amount.required' => 'يرجى إدخال مبلغ المصروف.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً صحيحاً.',
            'amount.min' => 'أقل مبلغ للمصروف هو 0.01 ج.م.',
            'expense_date.required' => 'يرجى تحديد تاريخ الصرف.',
            'payment_method.required' => 'يرجى تحديد طريقة السداد.',
        ]);

        Expense::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'payment_method' => $validated['payment_method'],
            'invoice_reference' => $validated['invoice_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'تم تسجيل وحفظ سند المصروف بمبلغ '.number_format($validated['amount'], 2).' ج.م بنجاح.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $amount = $expense->amount;
        $title = $expense->title;
        $expense->delete();

        return back()->with('success', "تم حذف سند المصروف ({$title}) بمبلغ ".number_format($amount, 2).' ج.م بنجاح.');
    }
}
