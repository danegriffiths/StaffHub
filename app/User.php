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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getFlexiBalance(){

        if (substr($this->flexi_balance,0,1) == "-") {
            return substr($this->flexi_balance, 0, 6);
        } else {
            return substr($this->flexi_balance, 0, 5);
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






    public function TODO() {
        "AT END OF THE DAY, Need to get previous balance, add/subtract from clockings, then update DB";
    }
    public function getCurrentTime()
    {
        return Carbon::now();
    }
}
