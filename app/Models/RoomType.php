<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relationship: A room type has many rooms
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}