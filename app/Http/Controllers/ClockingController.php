<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Clocking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;

class ClockingController extends Controller
{

    public function index() {

        $userId = Auth::user()->staff_number;
        $clockings = Clocking::where('staff_number',$userId)->orderBy('clocking_time')->paginate(50);
        return view('clockings.index', ['clockings' => $clockings]);
    }

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
            $clocking->manual = false;
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
            $clocking->manual = false;
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

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clockings.create');
    }

    /**
     * Remove the specified resource from storage.
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Clocking $clocking)
    {
        $user = Auth::user();
        $associatedClocking = Clocking::where('id', $clocking->associated_with)->first();

        if ($clocking->clocking_time < $associatedClocking->clocking_time) {

            $clocks = Clocking::where('staff_number', $user->staff_number)->
                where(DB::raw('substr(clocking_time, 1, 19)'), '>', $clocking->clocking_time)->
                where(DB::raw('substr(clocking_time, 1, 19)'), '<', $associatedClocking->clocking_time)->
                where('manual', true)->count();

            if ($clocks > 0) {
                return redirect()->route('clockings.index')->withErrors("Error: Please delete manual entries between this clock-in and clock-out first");
            }

        } else {
            $clocks = Clocking::where('staff_number', $user->staff_number)->
            where(DB::raw('substr(clocking_time, 1, 19)'), '>', $associatedClocking->clocking_time)->
            where(DB::raw('substr(clocking_time, 1, 19)'), '<', $clocking->clocking_time)->
            where('manual', true)->count();

            if ($clocks > 0) {
                return redirect()->route('clockings.index')->withErrors("Error: Please delete manual entries between this clock-in and clock-out first");
            }
        }

        $clocking->delete();
        $associatedClocking->delete();
        session()->flash('message', 'Clocking was deleted');
        return redirect()->route('clockings.index');
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
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Cannot submit date/time in future');
        }

        $uniqueClockInCheck = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeIn)->get();
        $uniqueClockOutCheck = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeOut)->get();

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Duplicate of existing clock in time');
        }

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockOutCheck->count() > 0) {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Duplicate of existing clock out time');
        }

        $comparingPriorClockType = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'), '<', $dateTimeIn)
            ->orderBy('clocking_time', 'desc')->first();

        $comparingNextClockType = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'), '>', $dateTimeIn)
            ->orderBy('clocking_time', 'asc')->first();


        //Reject overlapping clock times i.e. an out must follow an in, with no existing times inbetween.
        if ($comparingNextClockType != null) {
            if (substr($comparingNextClockType->clocking_time, 0, 16) < $dateTimeOut) {
                return redirect()->route('clockings.createinout')->withErrors('ERROR: Clock out must occur before clock in, and before next clocking entry');
            }
        }
        //Reject if the last clocking was of type "IN"
        if ($comparingPriorClockType != null && $comparingPriorClockType->clocking_type == "IN") {
            return redirect()->route('clockings.createinout')->withErrors('ERROR: Clock in can only be submitted if clock out occurred previously');
        } else {
            $clockIn = new Clocking;
            $clockIn->clocking_time = $dateTimeIn;
            $clockIn->staff_number = $user->staff_number;
            $clockIn->clocking_type = "IN";
            $clockIn->manual = true;
            $clockIn->approved = false;
            $clockIn->user_id = $user->id;
            $clockIn->save();

            $clockOut = new Clocking;
            $clockOut->clocking_time = $dateTimeOut;
            $clockOut->staff_number = $user->staff_number;
            $clockOut->clocking_type = "OUT";
            $clockOut->manual = true;
            $clockOut->approved = false;
            $clockOut->associated_with = $clockIn->id;
            $clockOut->user_id = $user->id;
            $clockOut->save();

            $update = Clocking::where('id', $clockIn->id)->first();
            $update->clocking_time = DB::raw('clocking_time');
            $update->associated_with = $clockOut->id;
            $update->save();

            //TODO EMAIL MANAGER
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

        $uniqueClockOutCheck = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeOut)->get();
        $uniqueClockInCheck = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'),'=', $dateTimeIn)->get();

        //Reject any duplicate clock out date/time entries
        if ($uniqueClockOutCheck->count() > 0 || $uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Duplicate of existing clock out time');
        }

        //Reject any duplicate clock in date/time entries
        if ($uniqueClockInCheck->count() > 0) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Duplicate of existing clock in time');
        }

        $comparingPriorClockType = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'), '<', $dateTimeOut)
            ->orderBy('clocking_time', 'desc')->first();

        $comparingNextClockType = Clocking::where('staff_number', $user->staff_number)->where(DB::raw('substr(clocking_time, 1, 16)'), '>', $dateTimeOut)
            ->orderBy('clocking_time', 'asc')->first();

        //Reject overlapping clock times i.e. an out must follow an in, with no existing times in between.
        if ($comparingNextClockType == null) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: No clockings exist after the IN date/time you have entered');
        } elseif (substr($comparingNextClockType->clocking_time,0,16) < $dateTimeIn) {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock in must occur after clock out, and before next clocking entry which has been saved');
        }

        //Reject if the user has not made any clockings, or the last clocking was of type "OUT"
        if ($comparingPriorClockType == null || $comparingPriorClockType->clocking_type == "OUT") {
            return redirect()->route('clockings.createoutin')->withErrors('ERROR: Clock out can only be submitted if clock in occurred previously');

        } else {

            $clockOut = new Clocking;
            $clockOut->clocking_time = $dateTimeOut;
            $clockOut->staff_number = $user->staff_number;
            $clockOut->clocking_type = "OUT";
            $clockOut->manual = true;
            $clockOut->approved = false;
            $clockOut->user_id = $user->id;
            $clockOut->save();

            $clockIn = new Clocking;
            $clockIn->clocking_time = $dateTimeIn;
            $clockIn->staff_number = $user->staff_number;
            $clockIn->clocking_type = "IN";
            $clockIn->manual = true;
            $clockIn->approved = false;
            $clockIn->associated_with = $clockOut->id;
            $clockIn->user_id = $user->id;
            $clockIn->save();

            $update = Clocking::where('id', $clockOut->id)->first();
            $update->clocking_time = DB::raw('clocking_time');
            $update->associated_with = $clockIn->id;
            $update->save();

            //TODO EMAIL MANAGER
            session()->flash('message', 'Clocking submitted successfully');
            return redirect()->route('clockings.create');
        }
    }

    public function approve(Clocking $clocking)
    {
        $approval = Clocking::find($clocking->id);
        $approval->approved = true;
        $approval->clocking_time = DB::raw('clocking_time');
        $approval->save();

        $approval = Clocking::find($clocking->id+1);
        $approval->approved = true;
        $approval->clocking_time = DB::raw('clocking_time');
        $approval->save();

        session()->flash('message', 'Clocking approved');
        return redirect()->route('creations.index');
    }

    public function reject(Clocking $clocking)
    {
        $approval = Clocking::find($clocking->id);
        $approval->rejected = true;
        $approval->clocking_time = DB::raw('clocking_time');
        $approval->save();

        $approval = Clocking::find($clocking->id+1);
        $approval->rejected = true;
        $approval->clocking_time = DB::raw('clocking_time');
        $approval->save();

        //TODO EMAIL USER
        session()->flash('message', 'Clocking rejected');
        return redirect()->route('creations.index');
    }

    public function getDailyBalance()
    {
        $user = Auth::user();
        if ($user->daily_hours_permitted == null) {
            return view('/dashboard')->withErrors("You are not setup to submit clockings on the system. Please contact an administrator.");
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

                            $dailyTime += $in->diffInMinutes($out);
                        }
                    }

                    //If at end of day IN is last clocking, add OUT at 2hrs more than daily allowance then notify user and manager
                    if ($date != Carbon::now()->toDateString()) {
                        $inCount = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "IN")->where(DB::raw('substr(clocking_time, 1, 10)'),'=', $date)->get();
                        $outCount = Clocking::where('staff_number', $staffNumber)->where('clocking_type', "OUT")->where(DB::raw('substr(clocking_time, 1, 10)'),'=', $date)->get();

                        if ($inCount->count() > $outCount->count()) {

                            $lastIn = $inCount[$inCount->count()-1]->clocking_time;

                            $updateTime = Carbon::createFromTimeString($lastIn)->addMinute();
                            $clocking = new Clocking;
                            $clocking->clocking_time = $updateTime;
                            $clocking->staff_number = $staffNumber;
                            $clocking->clocking_type = "OUT";
                            $clocking->manual = true;
                            $clocking->approved = false;
                            $clocking->user_id = $user->id;
                            $clocking->save();
                            //TODO-NEED TO ADD NOTIFICATION TO USER AND MANAGER NOW
                        }
                    }

                    //If dailytime > dailyallowance + 2hrs, cap at dailyallowance + 2, change clock out time, notify user and manager.
                    if ($dailyTime > $dailyAllowance + 120) {
                        $time = gmdate("i:s", abs($dailyAllowance + 120));
                        //TODO-NEED TO ADD NOTIFICATION TO USER AND MANAGER NOW
                    } else {
                        $time = gmdate("i:s", abs($dailyTime - $dailyAllowance));
                    }

                    if ($dailyTime - $dailyAllowance < 0) {
                        $time = '-' . $time;
                    }
                    $balanceList = Balance::where('staff_number', $staffNumber)->where('date', $date)->get();

                    $decimalTime = $this->time_to_decimal($time . ':00');

                    if ($balanceList->count() == 0) {
                        $balance = new Balance();
                        $balance->staff_number = $staffNumber;
                        $balance->daily_balance = gmdate("i:s", abs($decimalTime - $dailyAllowance));
                        $balance->date = $date;
                        $balance->user_id = $user->id;
                        $balance->save();
                    } else {
                        $balance = Balance::where('staff_number', $staffNumber)->where('date', $date)->first();
                        $balance->daily_balance = gmdate("i:s", abs($decimalTime - $dailyAllowance));;
                        $balance->save();
                    }

                }
            }
            $balances = Balance::where('staff_number', $staffNumber)->get();
            $totalHours = 0;
            foreach ($balances as $singleBalance) {
                $totalHours += $this->time_to_decimal($singleBalance->daily_balance);
            }
            $initialFlexiBalance = $this->time_to_decimal($user->flexi_balance);
            $latestFlexiBalance = gmdate("i:s", abs($initialFlexiBalance + $totalHours));
            $user->latest_flexi_balance = $latestFlexiBalance;
            $user->save();

        }
    }

    /**
     * Show the form for requesting data from the database.
     * @return \Illuminate\Http\Response
     */
    public function request()
    {
        return view('clockings.request');
    }

    /**
     * Download data from database.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $validatedData = $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $dateFrom = $validatedData['date_from'];
        $dateTo = $validatedData['date_to'];

        $clocks = Clocking::select('id','clocking_time', 'staff_number', 'clocking_type', 'manual', 'approved', 'rejected')->where(DB::raw('substr(clocking_time, 1, 10)'), '>=', $dateFrom)->
        where(DB::raw('substr(clocking_time, 1, 19)'), '<=', $dateTo)->get()->toArray();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['ID', 'Clocking Time', 'Staff Number', 'Clocking Type', 'Manual Entry', 'Approved', 'Rejected']);
        $csv->insertAll($clocks);

        $formattedDateFrom = date("d-m-y", strtotime($dateFrom));
        $formattedDateTo = date("d-m-y", strtotime($dateTo));

        $csv->output('Clockings - ' . $formattedDateFrom . ' - ' . $formattedDateTo . '.csv');
        die;
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
