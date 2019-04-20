<?php

namespace App\Http\Controllers;

use App\Clocking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClockingController extends Controller
{
    /**
     * Store a clock in
     * @return \Illuminate\Http\Response
     */
    public function clockIn()
    {
        $user = User::find(Auth::user()->id);
        if ($user->clocking_status == null || $user->isClockedIn() == false) {
            $clocking = new Clocking();
            $clocking->clocking_time = Carbon::now()->format('Y-m-d H:i:s');
            $clocking->staff_number = Auth::user()->staff_number;
            $clocking->clocking_type = 'IN';
            $clocking->approved = true;
            $clocking->user_id = $user->id;
            $clocking->save();

            $user->clocking_status = true;
            $user->save();

            session()->flash('message', 'Clock in submitted');
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', 'Already clocked in');
            return redirect()->route('dashboard');
        }
    }

    /**
     * Store a clock out
     * @return \Illuminate\Http\Response
     */
    public function clockOut()
    {
        $user = User::find(Auth::user()->id);
        if ($user->isClockedIn() == true) {
            $clocking = new Clocking();
            $clocking->clocking_time = Carbon::now()->format('Y-m-d H:i:s');
            $clocking->staff_number = Auth::user()->staff_number;
            $clocking->clocking_type = 'OUT';
            $clocking->approved = true;
            $clocking->user_id = $user->id;
            $clocking->save();

            $user->clocking_status = false;
            $user->save();

            session()->flash('message', 'Clock out submitted');
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', 'Please clock in first');
            return redirect()->route('dashboard');
        }
    }

    public function getClockings() {

        $userId = Auth::user()->staff_number;
        $clocks = Clocking::where('staff_number',$userId)->orderBy('clocking_time')->get();
        return view('clockings.index', ['clocks' => $clocks]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clockings.create');
    }

    /**
     * Store a newly created user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'time' => 'required',
            'type' => 'required',
        ]);

        $date = $validatedData['date'];
        $time = $validatedData['time'];
        $dateTime = $date." ".$time.":00";

        if ($validatedData['type'] == "IN") {
            $type = "IN";
        } else {
            $type = "OUT";
        }
        $user = User::find(Auth::user()->id);
        $clocking = new Clocking;
        $clocking->clocking_time = $dateTime;
        $clocking->staff_number = Auth::user()->staff_number;
        $clocking->clocking_type = $type;
        $clocking->approved = false;
        $clocking->user_id = $user->id;
        $clocking->save();
//        $user->staff_number = $validatedData['staff_number'];
//        $user->forename = $validatedData['forename'];
//        $user->surname = $validatedData['surname'];
//        $user->department = $validatedData['department'];
//        $user->daily_hours_permitted = $validatedData['daily_hours_permitted'];
//        $user->weekly_hours_permitted = $validatedData['weekly_hours_permitted'];
//        $user->flexi_balance = $validatedData['flexi_balance'];
//        $user->manager = array_has($validatedData, 'manager');
//        $user->administrator = array_has($validatedData, 'administrator');
//        $user->email = $validatedData['email'];
//        $user->password = Hash::make($validatedData['password']);
//        $user->manager_id = $validatedData['manager_id'];
//        $user->save();

        session()->flash('message', 'Clocking submitted successfully');
        return redirect()->route('clockings.create');
    }

}
