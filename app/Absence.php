<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
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
        'staff_number', 'flexi_type', 'date', 'flexi_balance_used'
    ];

    /**
     * Get the user that this clocking belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
