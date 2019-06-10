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

    /**
     * Remove the specified resource from storage.
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();
        session()->flash('message', 'Absence deleted');
        return redirect()->route('absences.index');
    }
}
