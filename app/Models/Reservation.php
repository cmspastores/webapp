<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Agreement;
use App\Models\Renters;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'agreement_id',
        'room_id',
        'first_name',
        'last_name',
        'reservation_type',
        'check_in_date',
        'check_out_date',
        'status',
        'pending_payload',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'pending_payload' => 'array',
    ];

    protected $with = ['agreement', 'agreement.renter'];

    /**
     * Relationship: Reservation belongs to an Agreement
     */
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    /**
     * Accessor: allow $reservation->renter to return the renter from the agreement
     *
     * Note: this is an accessor (not a DB relation) because Agreement does not
     * hold a foreign key to Reservation. Use $reservation->agreement->renter
     * when you need Eloquent relation chaining, or $reservation->renter for convenience.
     */
    public function getRenterAttribute()
    {
        return $this->agreement ? $this->agreement->renter : null;
    }
}
