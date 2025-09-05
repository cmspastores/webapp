<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   #####  ??
use App\Models\LoginLog;

class LoginLogController extends Controller
{
    public function index()
    {
        $logs = LoginLog::latest()->paginate(10); // paginated list
        return view('index', compact('logs'));
    }
}
