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
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date'     => 'datetime',
        'amount_due' => 'decimal:2',
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
}
