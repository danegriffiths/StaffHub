<?php

namespace App\Http\Controllers;

use App\Absence;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    /**
     * Return the view to store a flexi leave request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('absences.flexiLeave');
    }

    /**
     * Receive a flexi leave request, and process the request.
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'flexi-type' => 'required',
        ]);
        $date = Carbon::parse($validatedData['date']);
        $absences = Absence::where('date', $date)->count();
        if ($date->isWeekend()) {
            return redirect()->back()->withErrors("You have selected a weekend. Please select a weekday to use as flexi leave.");
        } elseif ($absences > 0) {
            return redirect()->back()->withErrors("You have already submitted leave for " . $date->format('d/m/Y'));
        }
        else {
            $user = Auth::user();
            app(ClockingController::class)->getDailyBalance();
            $flexiBalance = $user->getFlexiBalance();
            $flexiBalance = $flexiBalance . ":00";

            $fullDay = $this->time_to_decimal($user->daily_hours_permitted);
            $halfday = $this->time_to_decimal($user->daily_hours_permitted) / 2;
            $flexiBalanceDecimal = $this->time_to_decimal($flexiBalance);
            if ($validatedData['flexi-type'] === 'full') {
                //do calculation on full day's leave
                if ($flexiBalance < 0) {
                    return redirect()->back()->withErrors("Full day of leave will take you beyond -" . $user->daily_hours_permitted);
                } else {
                    $absence = new Absence;
                    $absence->staff_number = $user->staff_number;
                    $absence->flexi_type = 'FULL-DAY';
                    $absence->date = $date;
                    $absence->approved = false;
                    $absence->flexi_balance_used = gmdate("i:s", abs($fullDay));
                    $absence->save();
                }
            } else {
                //do calculation on full day's leave
                if (($flexiBalanceDecimal - $halfday) < (0 - $fullDay)) {
                    return redirect()->back()->withErrors("Half day of leave will take you beyond -" . $user->daily_hours_permitted);
                }
                $absence = new Absence;
                $absence->staff_number = $user->staff_number;
                $absence->flexi_type = 'HALF-DAY';
                $absence->date = $date;
                $absence->approved = false;
                $absence->flexi_balance_used = gmdate("i:s", abs($halfday));
                $absence->save();
            }
        }
        //TODO EMAIL MANAGER
        if (Auth::user()->manager) {
            $title = "Manager Dashboard";
        } else {
            $title = "User Dashboard";
        }
        session()->flash('message', 'Flexi leave submitted successfully');
        return view('dashboard', ['title' => $title]);
    }

    /**
     * Get minutes from a time value
     * @param $time
     * @return float|int
     */
    function time_to_decimal($time) {
        $timeArr = explode(':', $time);
        $decTime = ($timeArr[0]*60) + ($timeArr[1]) + ($timeArr[2]/60);

        return $decTime;
    }
}
