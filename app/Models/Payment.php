<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'billing_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'agreement_id',
        'bill_id',
        'payer_name',
        'amount',
        'payment_date',
        'created_by',
        'reference',
        'notes',
        'unallocated_amount',
        'payment_type', 'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'unallocated_amount' => 'decimal:2',
    ];

    public function items()
    {
        // payment_id -> payments.billing_id
        return $this->hasMany(PaymentItem::class, 'payment_id', 'billing_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }
}