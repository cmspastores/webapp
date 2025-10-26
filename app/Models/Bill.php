<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'renter_id',
        'room_id',
        'period_start',
        'period_end',
        'due_date',
        'amount_due',
        'base_amount',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date'     => 'datetime',
        'amount_due' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    public function renter()
    {
        return $this->belongsTo(Renters::class, 'renter_id', 'renter_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function charges()
    {
        return $this->hasMany(\App\Models\BillCharge::class, 'bill_id', 'id');
    }
    
    /**
     * Sum of charges (decimal)
     */
    public function getTotalChargesAttribute()
    {
        return $this->charges()->sum('amount') ?: 0.00;
    }

    /**
     * Recompute amount_due and balance from base_amount + charges
     * - baseAmount should be the rent the bill was created for (existing 'amount_due' before charges)
     * - When adding charges, we'll recalc amount_due = base + sum(charges)
     *   and balance = amount_due - (payments already applied) . If you don't store payments yet,
     *   we will just increase balance by charge amount.
     */
    public function recomputeTotalsFromCharges($forceSave = true)
    {
        // Determine base rent stored in DB. Decide where base is stored:
        // If you want to keep a dedicated 'base_amount' column, we should add it.
        // For now assume the bill originally had amount_due equal to base; but we'll preserve base in 'base_amount' on first change.
        if (! isset($this->base_amount)) {
            // if migration doesn't have base_amount, we'll fallback to current amount_due only for recalculation
        }

        $base = $this->base_amount ?? $this->amount_due; // if you added base_amount column, it will be used.
        $totalCharges = $this->charges()->sum('amount');

        // new total due
        $newAmountDue = round((float)$base + (float)$totalCharges, 2);

        // If you track payments, balance = newAmountDue - payments_total
        // currently we only have 'balance' column; to be safe we'll adjust balance by diff:
        $diff = $newAmountDue - $this->amount_due;

        $this->amount_due = $newAmountDue;
        // increase balance by diff (if bill had been partially paid, this preserves that logic)
        $this->balance = round((float)$this->balance + $diff, 2);

        if ($forceSave) {
            $this->save();
        }

        return $this;
    }
}
