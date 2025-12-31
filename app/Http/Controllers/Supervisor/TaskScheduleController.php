<?php

namespace App\Http\Controllers\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskScheduleController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $sites = $currentUser->sites_leader;
        
        return view('supervisor.task-schedule.index', compact('sites'));
    }
}
