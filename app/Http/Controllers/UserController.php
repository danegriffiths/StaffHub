<?php

namespace App\Http\Controllers;

use App\Clocking;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use League\Csv\Reader;

ini_set('max_execution_time', 320);

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        $users = User::orderBy('surname')->orderBy('forename')->paginate(25);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function managerIndex()
    {
        $users = User::where('manager',1)->orderBy('surname')->orderBy('forename')->paginate(25);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function administratorIndex()
    {
        $users = User::where('administrator',1)->orderBy('surname')->orderBy('forename')->paginate(25);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function staffIndex()
    {
        $managerId = Auth::user()->staff_number;
        $users = User::where('manager_id',$managerId)->orderBy('surname')->orderBy('forename')->paginate(25);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function clockingCreationsIndex()
    {
        $managerId = Auth::user()->staff_number;
        $users = User::where('manager_id',$managerId)->get();
        $clockings = Clocking::where([ ['approved', false], ['rejected', null] ])->orderBy('clocking_time')->get();

        foreach ($users as $u) {

            $data = null;
            foreach ($clockings as $c) {

                if ($c->staff_number == $u->staff_number) {
                    $data[] = $c;
                }
            }
            if ($data != null) {
                $u->clocking_corrections = $data;
            }
        }

        return view('approvals.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'staff_number' => 'required|string|max:12|unique:users',
            'forename' => 'required|string|max:50',
            'surname' => 'required|string|max:50',
            'department' => 'required|string|max:100',
            'daily_hours_permitted' => '',
            'weekly_hours_permitted' => '',
            'flexi_balance' => '',
            'manager' => '',
            // If present it means true, if not present it means false
            'administrator' => '',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'manager_id' => '',
        ]);

        $user = new User;
        $user->staff_number = $validatedData['staff_number'];
        $user->forename = $validatedData['forename'];
        $user->surname = $validatedData['surname'];
        $user->department = $validatedData['department'];
        $user->daily_hours_permitted = $validatedData['daily_hours_permitted'];
        $user->weekly_hours_permitted = $validatedData['weekly_hours_permitted'];
        $user->flexi_balance = $validatedData['flexi_balance'];
        $user->manager = array_has($validatedData, 'manager');
        $user->administrator = array_has($validatedData, 'administrator');
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->manager_id = $validatedData['manager_id'];
        $user->save();

        session()->flash('message', 'User created successfully');
        return redirect()->route('users.show');
    }

    /**
     * Display the specified resource.
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }

    public function importCsv() {

        $reader = Reader::createFromPath('/home/vagrant/Laravel/staffhub/app/Http/Controllers/test.csv', 'r');
        $results = $reader->fetch();
        foreach ($results as $row) {
            $user = new User;
            $user->staff_number = $row[0];
            $user->forename = $row[1];
            $user->surname = $row[2];
            $user->department = $row[3];
            $user->daily_hours_permitted = $row[4];
            $user->weekly_hours_permitted = $row[5];
            $user->flexi_balance = $row[6];
            if($row[7] == "true") {
                $user->manager = 1;
            } else {
                $user->manager = 0;
            }
            if($row[8] == "true") {
                $user->administrator = 1;
            } else {
                $user->administrator = 0;
            }
            $user->email =$row[9];
            $user->password = Hash::make($row[10]);
            $user->manager_id = $row[11];
            $user->save();
        }

        return view('dashboard');
    }
}
