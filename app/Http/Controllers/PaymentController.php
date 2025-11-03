<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Bill;
use App\Models\Agreement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['agreement','bill','items'])->orderBy('payment_date','desc');

        // 🔹 Search by payer name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('payer_name', 'like', "%{$search}%");
        }

        // 🔹 Filter by agreement
        if ($request->filled('agreement_id')) {
            $query->where('agreement_id', $request->input('agreement_id'));
        }

        // 🔹 Filter by bill
        if ($request->filled('bill_id')) {
            $query->where('bill_id', $request->input('bill_id'));
        }

        // 🔹 Filter by payment status (unallocated = leftover)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'unallocated') {
                $query->where('unallocated_amount', '>', 0);
            } elseif ($status === 'allocated') {
                $query->where('unallocated_amount', '=', 0);
            }
        }

        $payments = $query->paginate(25)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $billId = $request->query('bill_id');

        // Load agreements that have unpaid bills, with their bills -> only positive balances
        $agreements = \App\Models\Agreement::with(['bills' => function($q){
                $q->where('balance','>',0)->orderBy('period_start');
            }])
            ->whereHas('bills', function($q){ $q->where('balance','>',0); })
            ->get();

        // If a bill_id is given, load that bill and ensure it is fresh
        $selectedBill = null;
        if ($billId) {
            $selectedBill = \App\Models\Bill::with('agreement')->find($billId);
            if ($selectedBill) {
                // ensure we include the selected bill's agreement in the agreements list
                $agreementId = $selectedBill->agreement_id;
                if (!$agreements->pluck('agreement_id')->contains($agreementId)) {
                    $agreements->push($selectedBill->agreement->load(['bills' => function($q){
                        $q->where('balance','>',0)->orderBy('period_start');
                    }]));
                }
            }
        }

        // pass selectedBill to the view so the blade can preselect agreement & bill and get the balance
        $bill_id = $selectedBill?->id;
        return view('payments.create', compact('agreements', 'bill_id', 'selectedBill'));
    }

    /**
     * Store payment and allocate to bills (carry over to next bills of same agreement).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'bill_id' => 'nullable|exists:bills,id',
            'agreement_id' => 'nullable|exists:agreements,agreement_id',
            'payer_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'exact_payment' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
        ]);

        $bill = null;
        if (!empty($data['bill_id'])) {
            $bill = Bill::find($data['bill_id']);
            if (!$bill) {
                return back()->withErrors(['bill_id' => 'Selected bill not found.']);
            }
        }

        $amountToPay = (!empty($data['exact_payment']) && $bill)
            ? (float)$bill->balance
            : (float)$data['amount'];

        if ($amountToPay <= 0) {
            return back()->withErrors(['amount' => 'Payment amount must be greater than zero.']);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'agreement_id' => $data['agreement_id'] ?? ($bill?->agreement_id ?? null),
                'bill_id' => $bill?->id,
                'payer_name' => $data['payer_name'] ?? null,
                'amount' => $amountToPay,
                'payment_date' => Carbon::now(),
                'created_by' => Auth::id(),
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'unallocated_amount' => 0,
            ]);

            $remaining = $amountToPay;

            // Apply to current bill first
            if ($bill) {
                $applied = $bill->applyPaymentAmount($remaining);
                if ($applied > 0) {
                    PaymentItem::create([
                        'payment_id' => $payment->billing_id, // correct FK
                        'bill_id' => $bill->id,
                        'amount' => $applied,
                    ]);
                    $remaining = round($remaining - $applied, 2);
                }
            }

            // Carry over to next unpaid bills (same agreement)
            if ($remaining > 0) {
                $agreementId = $payment->agreement_id ?? $bill?->agreement_id;

                if ($agreementId) {
                    $nextBills = Bill::where('agreement_id', $agreementId)
                        ->where('balance', '>', 0)
                        ->orderBy('period_start', 'asc')
                        ->get();

                    foreach ($nextBills as $nb) {
                        if ($remaining <= 0) break;
                        if ($bill && $nb->id === $bill->id) continue;

                        $applied = $nb->applyPaymentAmount($remaining);
                        if ($applied > 0) {
                            PaymentItem::create([
                                'payment_id' => $payment->billing_id,
                                'bill_id' => $nb->id,
                                'amount' => $applied,
                            ]);
                            $remaining = round($remaining - $applied, 2);
                        }
                    }
                }
            }

            // Save leftover
            if ($remaining > 0) {
                $payment->unallocated_amount = $remaining;
                $payment->save();
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', "Payment recorded and allocated. Unallocated: ₱" . number_format($payment->unallocated_amount, 2));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Payment store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to store payment: ' . $e->getMessage()]);
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('items.bill');
        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        // Add authorization if needed
        $payment->delete();
        return redirect()->route('payments.index')->with('success','Payment deleted.');
    }
}
