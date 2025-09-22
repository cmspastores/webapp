<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'room_price',
        'number_of_occupants',
        'occupant_name',
        'start_date',
        'image',
    ];

    /**
     * Relationship: A room belongs to a room type
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
