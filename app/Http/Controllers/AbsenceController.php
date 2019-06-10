<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Clocking;
use Illuminate\Support\Facades\Auth;

class AbsenceController
{

    public function index() {

        $userId = Auth::user()->staff_number;
        $absences = Absence::where('staff_number',$userId)->orderBy('date')->paginate(25);
        return view('absences.index', ['absences' => $absences]);
    }
}
