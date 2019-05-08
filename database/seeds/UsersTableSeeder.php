<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $u = new User();
        $u->staff_number = "123456";
        $u->forename = "Dane";
        $u->surname = "Griffiths";
        $u->department = "HR";
        $u->manager = true;
        $u->administrator = true;
        $u->email="danegriffiths@dvla.com";
        $u->password=bcrypt('admin');
        $u->save();


        $u = new User();
        $u->staff_number = "111111";
        $u->forename = "John";
        $u->surname = "Jones";
        $u->department = "HR";
        $u->daily_hours_permitted = "07:40:00";
        $u->weekly_hours_permitted = "37:00:00";
        $u->flexi_balance = "12:00:00";
        $u->manager = false;
        $u->administrator = false;
        $u->email="danegriffiths2@dvla.com";
        $u->password=bcrypt('admin');
        $u->save();

        $u = new User();
        $u->staff_number = "121212";
        $u->forename = "Manager";
        $u->surname = "Jones";
        $u->department = "HR";
        $u->daily_hours_permitted = "07:40:00";
        $u->weekly_hours_permitted = "37:00:00";
        $u->flexi_balance = "12:00:00";
        $u->manager = true;
        $u->administrator = false;
        $u->email = "manager@dvla.com";
        $u->password = bcrypt('admin');
        $u->save();

        $u = new User();
        $u->staff_number = "123123";
        $u->forename = "Test1";
        $u->surname = "Employee";
        $u->department = "HR";
        $u->daily_hours_permitted = "07:24:00";
        $u->weekly_hours_permitted = "37:00:00";
        $u->flexi_balance = "12:05:00";
        $u->manager = false;
        $u->administrator = false;
        $u->email="test1@dvla.com";
        $u->password = bcrypt('admin');
        $u->manager_id = "121212";
        $u->save();

        $u = new User();
        $u->staff_number = "321321";
        $u->forename = "Test2";
        $u->surname = "Employee";
        $u->department = "HR";
        $u->daily_hours_permitted = "07:24:00";
        $u->weekly_hours_permitted = "37:00:00";
        $u->flexi_balance = "12:05:00";
        $u->manager = false;
        $u->administrator = false;
        $u->email="test2@dvla.com";
        $u->password = bcrypt('admin');
        $u->manager_id = "121212";
        $u->save();
    }
}
