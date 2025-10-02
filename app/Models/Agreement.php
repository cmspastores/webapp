<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Agreement extends Model
{
    use HasFactory;

    // If your migration used bigIncrements('agreement_id')
    protected $primaryKey = 'agreement_id';

    // If you want the model to treat dates properly
    protected $casts = [
        'agreement_date' => 'date',
        'start_date'     => 'date',
        'end_date'       => 'date',
        'monthly_rent'   => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    // If your primary key is an integer and auto-incrementing (default)
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'renter_id',
        'room_id',
        'agreement_date',
        'start_date',
        'end_date',
        'monthly_rent',
        'is_active',
    ];

    // Relationships
    public function renter()
    {
        // Renters model uses 'renter_id' as primary key
        return $this->belongsTo(Renters::class, 'renter_id', 'renter_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    // Auto set 1-year end date if not provided
    protected static function booted()
    {
        static::creating(function ($agreement) {
            if (empty($agreement->end_date) && !empty($agreement->start_date)) {
                $agreement->end_date = Carbon::parse($agreement->start_date)->addYear();
            }
        });
    }
}