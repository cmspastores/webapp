<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'name',
        'description',
        'amount',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }
}