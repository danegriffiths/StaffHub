<?php

use App\Clocking;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClockingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = new Clocking();
        $c->clocking_time = Carbon::now()->format('Y-m-d H:i:s');
        $c->staff_number = "111111";
        $c->clocking_type = "IN";
        $c->approved = true;

        $user = App\User::all()->where('staff_number','111111')->first->id;
        $c->user()->associate($user);
        $c->save();

        $c = new Clocking();
        $c->clocking_time = Carbon::now()->format('Y-m-d H:i:s');
        $c->staff_number = "123123";
        $c->clocking_type = "IN";
        $c->approved = true;

        $user = App\User::all()->where('staff_number','121212')->first->id;
        $c->user()->associate($user);
        $c->save();
    }
}
