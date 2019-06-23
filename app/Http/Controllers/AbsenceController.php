<?php

namespace App\Http\Controllers;

use App\Absence;
use App\User;
use Illuminate\Support\Facades\Auth;

class AbsenceController
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::user()->staff_number;
        $absences = Absence::where('staff_number',$userId)->orderBy('date')->paginate(25);
        return view('absences.index', ['absences' => $absences]);
    }

    public function managerIndex()
    {
        $managerId = Auth::user()->staff_number;
        $users = User::where('manager_id',$managerId)->get();
        $absences = Absence::where([ ['approved', false], ['rejected', null] ])->orderBy('date')->get();

        foreach ($users as $u) {

            $data = null;
            foreach ($absences as $a) {

                if ($a->staff_number == $u->staff_number) {
                    $data[] = $a;
                }
            }
            if ($data != null) {
                $u->absences = $data;
            }
        }
        return view('absences.managerIndex', ['users' => $users]);
    }

    public function approve(Absence $absence)
    {
        $approval = Absence::find($absence->id);
        $approval->approved = true;
        $approval->save();

        session()->flash('message', 'Absence approved');
        return redirect()->route('absences.managerIndex');
    }

    public function reject(Absence $absence)
    {
        $approval = Absence::find($absence->id);
        $approval->rejected = true;
        $approval->save();

        //TODO EMAIL USER
        session()->flash('message', 'Absence rejected');
        return redirect()->route('absences.managerIndex');
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
