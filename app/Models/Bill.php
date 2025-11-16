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
     * Payments applied to this bill (through PaymentItem pivot)
     */
    public function payments()
    {
        return $this->belongsToMany(\App\Models\Payment::class, \App\Models\PaymentItem::class, 'bill_id', 'payment_id')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
    
    /**
     * Sum of charges (decimal)
     */
    public function getTotalChargesAttribute()
    {
        return $this->charges()->sum('amount') ?: 0.00;
    }

    /**
     * Recompute amount_due and balance from base_amount + charges.
     * If a dedicated base_amount column exists, use it. Otherwise we treat
     * the original amount_due that existed before any charges as the base.
     *
     * This method ensures that status is updated accordingly:
     * - balance == 0 => status = 'paid'
     * - 0 < balance < amount_due => status = 'partially_paid'
     * - balance == amount_due => status = 'unpaid'
     */
    public function recomputeTotalsFromCharges(bool $save = true)
    {
        // total charges
        $totalCharges = $this->charges()->sum('amount') ?: 0.00;

        // Determine base: prefer base_amount column if exists (check attribute), otherwise try using stored base meta
        if (array_key_exists('base_amount', $this->attributes) && !is_null($this->base_amount)) {
            $base = (float)$this->base_amount;
        } else {
            // If base_amount column doesn't exist, we will attempt to find a base stored in meta
            // If none, assume the current amount_due minus current charges is the base (best effort)
            $base = max(0.00, (float)$this->amount_due - $this->charges()->sum('amount'));
            // persist base into attribute if DB has column (best effort)
            if (array_key_exists('base_amount', $this->attributes) && is_null($this->base_amount)) {
                $this->base_amount = $base;
            }
        }

        $newAmountDue = round($base + (float)$totalCharges, 2);

        // Determine how to compute new balance:
        // If you track payments separately, you'd compute payments_total and subtract.
        // We don't have payments_total; we keep current paid amount as (old amount_due - balance).
        $previousAmountDue = (float) ($this->getOriginal('amount_due') ?? $this->amount_due);
        $previousBalance = (float)$this->balance;

        // amount already paid (if any)
        $alreadyPaid = max(0.00, $previousAmountDue - $previousBalance);

        // new balance is newAmountDue - alreadyPaid (can't go below 0)
        $newBalance = round(max(0.00, $newAmountDue - $alreadyPaid), 2);

        // assign
        $this->amount_due = $newAmountDue;
        $this->balance = $newBalance;

        // update status
        if ($newBalance <= 0) {
            $this->status = 'paid';
            $this->balance = 0.00;
        } elseif ($newBalance < $newAmountDue) {
            $this->status = 'partially_paid';
        } else {
            $this->status = 'unpaid';
        }

        if ($save) $this->save();

        return $this;
    }

    // applies amount to this bill, returns amount actually applied (<= $amount)
    public function applyPaymentAmount(float $amount): float
    {
        $amount = round($amount, 2);
        if ($amount <= 0) return 0.0;

        $apply = min($amount, $this->balance);
        $this->balance = round($this->balance - $apply, 2);

        // If the bill has charges, check status more carefully
        if ($this->balance <= 0) {
            if ($this->charges()->count() > 0) {
                $this->status = 'paid';
            } else {
                // If no charges and rent is fully paid, mark as paid
                $this->status = 'paid';
            }
            $this->balance = 0.00;
        } elseif ($this->balance < $this->amount_due) {
            $this->status = 'partially_paid';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
        return $apply;
    }
}
