<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'administrator' => 'boolean',
        'manager' => 'boolean',
        'clocking_status' => 'boolean',
        'flexi_balance' => 'string'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_number', 'forename', 'surname', 'department', 'daily_hours_permitted', 'weekly_hours_permitted',
        'flexi_balance', 'manager', 'administrator', 'email', 'email_verified_at', 'password', 'manager_id'
    ];

    /**
     * Get the direct clockings a user has access to.
     */
    public function clockings()
    {
        return $this->hasMany('App\Clocking');
    }

    public function balances()
    {
        return $this->hasMany('App\Balance');
    }

    public function absences()
    {
        return $this->hasMany('App\Absence');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getFlexiBalance(){

        $balances = Balance::all()->where('staff_number', $this->staff_number);

        $accumulatedFlexi = 0;
        foreach($balances as $singleBalance) {
            $accumulatedFlexi += $this->time_to_decimal($singleBalance->daily_balance);
        }

        $absences = Absence::all()->where('staff_number', $this->staff_number);

        $accumulatedAbsences = 0;
        foreach($absences as $singleAbsence) {
            $accumulatedAbsences += $this->time_to_decimal($singleAbsence->flexi_balance_used);
        }

        $flexiBalance = $this->time_to_decimal($this->flexi_balance);
        $calculatedBalance = $flexiBalance + $accumulatedFlexi - $accumulatedAbsences;
        $time = gmdate("i:s", abs($calculatedBalance));

        if ($calculatedBalance < 0) {
            $time = '-' . $time;
        }
        return $time;
    }

    public function getDailyBalance() {

        $today = substr(Carbon::now(), 0, 10);
        $balance = Balance::all()->where('staff_number', $this->staff_number)->where('date', $today)->first();
        $dailyHoursPermitted = $this->time_to_decimal($this->daily_hours_permitted);

        if ($balance == null) {
            return "No clockings submitted today";
        } else {
            $dailyBalance = $this->time_to_decimal($balance->daily_balance);

            $total = $dailyHoursPermitted + $dailyBalance;

            $time = gmdate("i:s", abs($total));
            if ($total < 0) {
                $time = '-' . $time;
            }
            return $time;
        }
    }
    /**
     * Get the user's display name.
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->forename} {$this->surname}";
    }

    /**
     * Checks if the user has the administrator flag.
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->administrator) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the user has the manager flag.
     * @return bool
     */
    public function isManager()
    {

        if ($this->manager) {
            return true;
        } else {
            return false;
        }
    }

    public function isClockedIn() {
        return $this->clocking_status;
    }

    public function manager()
    {
        return $this->belongsTo('App\User');
    }

    public function managerName()
    {
        return User::where('staff_number', $this->manager_id)->first();
    }


    /**
     * Get minutes from a time value
     * @param $time
     * @return float|int
     */
    function time_to_decimal($time) {
        $timeArr = explode(':', $time);
        if (substr($timeArr[0], 0, 1) == '-') {
            $decTime = ($timeArr[0]*60) - ($timeArr[1]) - ($timeArr[2]/60);
        } else {
            $decTime = ($timeArr[0] * 60) + ($timeArr[1]) + ($timeArr[2] / 60);
        }
        return $decTime;
    }
}
