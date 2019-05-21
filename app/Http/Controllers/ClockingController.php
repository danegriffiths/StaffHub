<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Clocking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $this->getDailyBalance();
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

        session()->flash('message', 'Clocking submitted successfully');
        return redirect()->route('clockings.create');
    }

    public function approve(Clocking $clocking)
    {
        $approval = Clocking::find($clocking->id);
        $approval->approved = true;
        $approval->save();

        session()->flash('message', 'Clocking approved');
        return redirect()->route('creations.index');
    }

    public function reject(Clocking $clocking)
    {
        $approval = Clocking::find($clocking->id);
        $approval->rejected = true;
        $approval->save();

        session()->flash('message', 'Clocking rejected');
        return redirect()->route('creations.index');
    }


    public function getDailyBalance()
    {
        $dailyAllowance = $this->time_to_decimal(Auth::user()->daily_hours_permitted);
        $userId = Auth::user()->staff_number;
        $clocks = Clocking::where('staff_number', $userId)->get();
        $clockIns = Clocking::where('staff_number', $userId)->where('clocking_type',"IN")->orderBy('clocking_time')->get();
        $clockOuts = Clocking::where('staff_number', $userId)->where('clocking_type',"OUT")->orderBy('clocking_time')->get();

        foreach ($clocks as $singleClock) {
            $data[] = substr($singleClock->clocking_time, 0, 10);
        }
        $uniqueDates = array_unique($data);

        foreach ($uniqueDates as $date) {

            $dailyTime = 0;
            for ($i = 0; $i < $clockOuts->count(); $i++) {

                if ($date == substr($clockOuts[$i]->clocking_time, 0, 10)) {

                    $in = Carbon::createFromFormat('Y-m-d H:i:s', $clockIns[$i]->clocking_time);
                    $out = Carbon::createFromFormat('Y-m-d H:i:s', $clockOuts[$i]->clocking_time);

                    $times[] = $in->diffInMinutes($out);
                    $dailyTime += $in->diffInMinutes($out);
                }
            }

            $time = gmdate("i:s", abs($dailyTime - $dailyAllowance));
            if ($dailyTime - $dailyAllowance < 0) {
                $time = '-' . $time;
            }

            $balanceList = Balance::where('staff_number', $userId)->where('date', $date)->get();

            if ($balanceList->count() == 0) {
                $balance = new Balance();
                $balance->staff_number = $userId;
                $balance->daily_balance = $time;
                $balance->date = $date;
                $balance->save();
            } else {
                $balance = Balance::where('staff_number', $userId)->where('date', $date)->first();
                $balance->daily_balance = $time;
                $balance->save();
            }

        }
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
