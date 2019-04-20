<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Clocking extends Model
{
    use Notifiable;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'approved' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'clocking_time', 'staff_number', 'clocking_type'
    ];

    /**
     * Get the user that this clocking belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getDailyBalance() {

    }

    public function storeDailyBalance() {
        $expiresAt = Carbon::now()->endOfDay();

    }

}
