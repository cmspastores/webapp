<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillCharge;
use Illuminate\Http\Request;

class BillChargeController extends Controller
{
    public function store(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // Create the charge
        $charge = BillCharge::create(array_merge($data, ['bill_id' => $bill->id]));

        // Recompute bill totals (this uses Bill::recomputeTotalsFromCharges)
        $bill->recomputeTotalsFromCharges(true);

        return redirect()->back()->with('success', 'Charge added to bill.');
    }

    public function destroy(Bill $bill, BillCharge $charge)
    {
        // ensure the charge belongs to the bill
        if ($charge->bill_id !== $bill->id) {
            return redirect()->back()->with('error', 'Charge does not belong to this bill.');
        }

        $amount = $charge->amount;
        $charge->delete();

        // Recompute bill totals
        $bill->recomputeTotalsFromCharges(true);

        return redirect()->back()->with('success', 'Charge removed and bill updated.');
    }
}