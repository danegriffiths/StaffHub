@extends('layouts.app', ['title' => $title ])

@section('content')
	<div id="adminButtons">
        @if( auth()->user()->administrator )
            <div class="btn-group btn-group-lg" style="width:100%">
                <a href="{{ route('users.index') }}" class="btn btn-primary" style="width: 33.3%">All users</a>
                <a href="{{ route('managers.index') }}" class="btn btn-primary" style="width: 33.3%">Managers</a>
                <a href="{{ route('administrators.index') }}" class="btn btn-primary" style="width: 33.3%">Administrators</a>
            </div>
            <hr>
            <div class="btn-group btn-group-lg" style="width:100%">
                <a href="{{ route('users.create') }}" class="btn btn-primary" style="width: 33.3%">Create User</a>
                <a href="{{ route('clockings.request') }}" class="btn btn-primary" style="width: 33.3%">Download clockings</a>
                <a href="{{ route('users.loadData') }}" class="btn btn-danger" style="width: 33.3%" onclick="return confirm('WARNING \n Are you sure you want to migrate data?')">Migrate Data</a>

            </div>
        @endif
	</div>

    @if( !auth()->user()->administrator )
        <div class="info" style="text-align: center">
            <h2>Total flexi balance: </h2>
            @if ( substr(auth()->user()->getFlexiBalance(),0,1) == "-" )
                <h4 style="color: red">{{ auth()->user()->getFlexiBalance() }}</h4>
            @else
                <h4 style="color: green">{{ auth()->user()->getFlexiBalance() }}</h4>
            @endif

            <h2>Clocking status</h2>
            @if ( auth()->user()->isClockedIn() )
                <h4 style="color: green">Clocked In</h4>
            @else
                <h4 style="color: red">Clocked Out</h4>
            @endif
            <h2>Daily balance</h2>
            @if ( auth()->user()->getDailyBalance() === 'No clockings submitted today' )
                <h4 style="color: red">{{ auth()->user()->getDailyBalance() }}</h4>
            @else
                <h4 style="color: red">{{ auth()->user()->getDailyBalance() }} of {{ substr(auth()->user()->daily_hours_permitted, 0, 5) }}</h4>
            @endif
        </div>
        <br>
        <div class="btn-group btn-group-lg" style="width:100%">
            @php
            $start = '06:00:00';
            $end   = '20:00:00';
            $now   = Carbon::now('UTC');
            $time  = $now->format('H:i:s');
            @endphp
            @if (Carbon::now()->isWeekend())
                <h2 style="text-align: center; color: red; margin: auto">Not permitted to clock in on weekends.</h2>
            @elseif ($time < $start || $time > $end)
                <h2 style="text-align: center; color: red; margin: auto">Clocking only available between 6am and 8pm.</h2>
            @elseif ( !auth()->user()->isClockedIn() )
                <a href="{{ route('clock-in.store') }}" class="btn btn-primary" style="width: 100%">Clock In</a>
            @else
                <a href="{{ route('clock-out.store') }}" class="btn btn-primary" style="width: 100%">Clock Out</a>
            @endif
        </div>
        <hr>
        <div class="btn-group btn-group-lg" style="width:100%">
            <a href="{{ route('clockings.index') }}" class="btn btn-primary" style="width: 50%">View Clocking Records</a>
            <a href="{{ route('clockings.create') }}"class="btn btn-primary" style="width: 50%">Submit Clocking Request</a>
        </div>
        <hr>
        <div class="btn-group btn-group-lg" style="width:100%">
            <a href="{{ route('absences.index') }}" class="btn btn-primary" style="width: 50%">View Flexi Leave Submissions</a>
            <a href="{{ route('absences.create') }}" class="btn btn-primary" style="width: 50%">Submit Flexi Leave Request</a>
        </div>
    @endif
    @if( auth()->user()->manager )
        <hr>
        <h2 style="text-align: center">Staff</h2>
        <div class="btn-group btn-group-lg" style="width:100%">
            <a href="{{ route('staff.index') }}" class="btn btn-primary" style="width: 50%">View Staff</a>
            <a href="{{ route('creations.index') }}" class="btn btn-primary" style="width: 50%">Manage Clocking Requests</a>
            <a href="{{ route('absences.managerIndex') }}" class="btn btn-primary" style="width: 50%">Manage Absence Requests</a>

        </div>
    @endif

@endsection
