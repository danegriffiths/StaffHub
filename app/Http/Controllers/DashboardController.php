<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    
    public function index()
    {
        if (Auth::user()->administrator) {
            $title = "Admin Dashboard";
        } elseif (Auth::user()->manager) {
            $title = "Manager Dashboard";
        } else {
            $title = "User Dashboard";
        }
        app(ClockingController::class)->getDailyBalance();
        return view('dashboard', ['title' => $title]);
    }
}
