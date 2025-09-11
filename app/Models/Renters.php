<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renters extends Model
{
    use HasFactory;

    protected $table = 'renters'; // explicitly define table
    protected $primaryKey = 'renter_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'email',
        'phone',
        'address',
        'emergency_contact',
        'created_at',
        'updated_at',
    ];
}

