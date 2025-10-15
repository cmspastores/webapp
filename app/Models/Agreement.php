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

    // If your primary key is an integer and auto-incrementing (default)
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'renter_id', 'room_id', 'agreement_date', 'start_date', 'end_date',
        'monthly_rent', // if you keep legacy
        'rate','rate_unit','is_active',
    ];

    // If you want the model to treat dates properly
    protected $casts = [
        'agreement_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $dates = ['agreement_date', 'start_date', 'end_date'];

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

    // convenient access to room type
    public function roomType()
    {
        return $this->hasOneThrough(RoomType::class, Room::class, 'id', 'id', 'room_id', 'room_type_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'agreement_id', 'agreement_id');
    }

    public function isTransient()
    {
        return ($this->rate_unit ?? 'monthly') === 'daily';
    }

    public function displayRate()
    {
        return ($this->rate_unit === 'daily' ? '₱' . number_format($this->rate,2) . ' / day' : '₱' . number_format($this->rate,2) . ' / month');
    }

    // 🔹 Automatically detect "expired" without storing it in the DB
    public function getStatusAttribute()
    {
        if (!$this->is_active && $this->end_date && $this->end_date->isPast()) {
            return 'Expired';
        }

        return $this->is_active ? 'Active' : 'Terminated';
    }

    // Auto set 1-year end date if not provided
    protected static function booted()
    {
        // Auto-fill end_date when creating
        static::creating(function ($agreement) {
            if (empty($agreement->end_date) && !empty($agreement->start_date)) {
                $agreement->end_date = Carbon::parse($agreement->start_date)->addYear();
            }
        });

        // Auto-mark expired as inactive when retrieved
        static::retrieved(function ($agreement) {
            if (
                $agreement->is_active &&
                $agreement->end_date &&
                Carbon::parse($agreement->end_date)->isPast()
            ) {
                $agreement->is_active = false;
                $agreement->saveQuietly();
            }
        });
    }
}