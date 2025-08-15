<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $table = 'login_logs'; 

    protected $fillable = [
        'user_id',
        'email',
        'logged_in_at',
        'logged_out_at',
    ];
}
