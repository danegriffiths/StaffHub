<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index()
    {
        app(ClockingController::class)->getDailyBalance();
        return view('dashboard');
    }
}
