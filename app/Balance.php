<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'staff_number', 'daily_balance', 'date'
    ];

    /**
     * Get the user that this clocking belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
