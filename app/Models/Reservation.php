<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $primaryKey = 'reservation_id';

    // fillable must include foreign keys and names used by the form
    protected $fillable = [
        'agreement_id',
        'room_id',
        'first_name',
        'last_name',
        'reservation_type',
        'check_in_date',
        'check_out_date',
        'status',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];
}
