<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Clocking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $this->getDailyBalance();
            session()->flash('message', 'Clock in submitted');
            return redirect()->route('dashboard');
        } else {
            $this->getDailyBalance();
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

            $this->getDailyBalance();
            session()->flash('message', 'Clock out submitted');
            return redirect()->route('dashboard');
        } else {
            $this->getDailyBalance();
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
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function createInOut()
    {
        return view('clockings.createinout');
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function createOutIn()
    {
        return view('clockings.createoutin');
    }

    /**
     * Store a newly created user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInOut(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        $date = $validatedData['date'];
        $timeIn = $validatedData['time_in'];
        $timeOut = $validatedData['time_out'];

        //Clock Out must occur after Clock In
        if ($timeOut < $timeIn) {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Clock out must be after clock in');
        }

        $user = Auth::user();

        $dateTimeIn = $date . " " . $timeIn;
        $dateTimeOut = $date . " " . $timeOut;

        //Reject any dates that occur in the future
        if ($dateTimeOut > Carbon::now() || $dateTimeIn > Carbon::now())  {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Cannot submit date/time in future');
        }

        $uniqueClockInCheck = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeIn)->get();
        $uniqueClockOutCheck = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeOut)->get();

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Duplicate of existing clock in time');
        }

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockOutCheck->count() > 0) {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Duplicate of existing clock out time');
        }

        $comparingPriorClockType = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'), '<', $dateTimeIn)
            ->orderBy('clocking_time', 'desc')->first();

        $comparingNextClockType = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'), '>', $dateTimeIn)
            ->orderBy('clocking_time', 'asc')->first();

//        dd($comparingNextClockType);
        //Reject overlapping clock times i.e. an out must follow an in, with no existing times inbetween.
        if ($comparingNextClockType != null) {
            if (substr($comparingNextClockType->clocking_time, 0, 16) < $dateTimeOut) {
                return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock out must occur before clock in, and before next clocking entry');
            }
        }
        //Reject if the last clocking was of type "IN"
        if ($comparingPriorClockType->clocking_type == "IN") {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock in can only be submitted if clock out occurred previously');

        } else {
            $clocking = new Clocking;
            $clocking->clocking_time = $dateTimeIn;
            $clocking->staff_number = $user->staff_number;
            $clocking->clocking_type = "IN";
            $clocking->approved = false;
            $clocking->user_id = $user->id;
            $clocking->save();

            $clocking = new Clocking;
            $clocking->clocking_time = $dateTimeOut;
            $clocking->staff_number = $user->staff_number;
            $clocking->clocking_type = "OUT";
            $clocking->approved = false;
            $clocking->user_id = $user->id;
            $clocking->save();


            session()->flash('message', 'Clocking submitted successfully');
            return redirect()->route('clockings.create');
        }
    }

    /**
     * Store a newly created user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOutIn(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        $date = $validatedData['date'];
        $timeOut = $validatedData['time_out'];
        $timeIn = $validatedData['time_in'];

        //Clock Out must occur before Clock In
        if ($timeOut > $timeIn) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock in must be after clock out');
        }

        $user = Auth::user();

        $dateTimeOut = $date . " " . $timeOut;
        $dateTimeIn = $date . " " . $timeIn;

        //Reject any dates that occur in the future
        if ($dateTimeOut > Carbon::now() || $dateTimeIn > Carbon::now())  {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Cannot submit date/time in future');
        }

        $uniqueClockOutCheck = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeOut)->get();
        $uniqueClockInCheck = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeIn)->get();

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockOutCheck->count() > 0 || $uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Duplicate of existing clock out time');
        }

        //Reject any duplicate clock in date/time entries
        if ($uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Duplicate of existing clock in time');
        }

        $comparingPriorClockType = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'), '<', $dateTimeOut)
            ->orderBy('clocking_time', 'desc')->first();

        $comparingNextClockType = Clocking::where('staff_number', $user->staff_number)->where(\DB::raw('substr(clocking_time, 1, 16)'), '>', $dateTimeOut)
            ->orderBy('clocking_time', 'asc')->first();

        //Reject overlapping clock times i.e. an out must follow an in, with no existing times inbetween.
        if (substr($comparingNextClockType->clocking_time,0,16) < $dateTimeIn) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock in must occur after clock out, and before next clocking entry');
        }

        //Reject if the user has not made any clockings, or the last clocking was of type "OUT"
        if ($comparingPriorClockType == null || $comparingPriorClockType->clocking_type == "OUT") {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock out can only be submitted if clock in occurred previously');

        } else {
            $clocking = new Clocking;
            $clocking->clocking_time = $dateTimeOut;
            $clocking->staff_number = $user->staff_number;
            $clocking->clocking_type = "OUT";
            $clocking->approved = false;
            $clocking->user_id = $user->id;
            $clocking->save();

            $clocking = new Clocking;
            $clocking->clocking_time = $dateTimeIn;
            $clocking->staff_number = $user->staff_number;
            $clocking->clocking_type = "IN";
            $clocking->approved = false;
            $clocking->user_id = $user->id;
            $clocking->save();

            session()->flash('message', 'Clocking submitted successfully');
            return redirect()->route('clockings.create');
        }
    }

    public function approve(Clocking $clocking)
    {
        $approval = Clocking::find($clocking->id);
        $approval->approved = true;
        $approval->save();

        $approval = Clocking::find($clocking->id+1);
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

        $approval = Clocking::find($clocking->id+1);
        $approval->rejected = true;
        $approval->save();

        session()->flash('message', 'Clocking rejected');
        return redirect()->route('creations.index');
    }


    public function getDailyBalance()
    {
        $user = Auth::user();
        if ($user->daily_hours_permitted == null) {
            return view('/dashboard');
        } else {
            $dailyAllowance = $this->time_to_decimal(Auth::user()->daily_hours_permitted);
            $staffNumber = $user->staff_number;
            $clocks = Clocking::where('staff_number', $staffNumber)->get();
            if ($clocks->count() == 0) {
                return view('/dashboard');
            } else {
                $clockIns = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "IN")->orderBy('clocking_time')->get();
                $clockOuts = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "OUT")->orderBy('clocking_time')->get();

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

                    //If at end of day IN is last clocking, add OUT at 2hrs more than daily allowance then notify user and manager
                    if ($date != Carbon::now()->toDateString()) {
                        $inCount = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "IN")->where(\DB::raw('substr(clocking_time, 1, 10)'),'=', $date)->get();
                        $outCount = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "OUT")->where(\DB::raw('substr(clocking_time, 1, 10)'),'=', $date)->get();

                        if ($inCount->count() > $outCount->count()) {

                            $lastIn = $inCount[$inCount->count()-1]->clocking_time;

                            $updateTime = Carbon::createFromTimeString($lastIn)->addMinute();
                            $clocking = new Clocking;
                            $clocking->clocking_time = $updateTime;
                            $clocking->staff_number = $staffNumber;
                            $clocking->clocking_type = "OUT";
                            $clocking->approved = false;
                            $clocking->user_id = $user->id;
                            $clocking->save();
                            /**
                             * NEED TO ADD NOTIFICATION TO USER AND MANAGER NOW
                             */
                        }
                    }

                    //If dailytime > dailyallowance + 2hrs, cap at dailyallowance + 2, change clock out time, notify user and manager.
                    /**
                     * NEED TO ADD NOTIFICATION TO USER AND MANAGER NOW
                     */
                    if ($dailyTime > $dailyAllowance + 120) {
                        $time = gmdate("i:s", abs($dailyAllowance + 120));
                    } else {
                        $time = gmdate("i:s", abs($dailyTime - $dailyAllowance));
                    }

                    if ($dailyTime - $dailyAllowance < 0) {
                        $time = '-' . $time;
                    }

                    $balanceList = Balance::where('staff_number', $staffNumber)->where('date', $date)->get();

                    if ($balanceList->count() == 0) {
                        $balance = new Balance();
                        $balance->staff_number = $staffNumber;
                        $balance->daily_balance = $time;
                        $balance->date = $date;
                        $balance->user_id = $user->id;
                        $balance->save();
                    } else {
                        $balance = Balance::where('staff_number', $staffNumber)->where('date', $date)->first();
                        $balance->daily_balance = $time;
                        $balance->save();
                    }

                }
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
