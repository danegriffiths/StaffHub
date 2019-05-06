@extends('layouts.app', ['title' => 'StaffHub: Admin Dashboard' ])

@section('content')
	<div id="adminButtons">
<!--            @auth-->
<!--            <a href="{{ route('dashboard') }}" class="btn btn-primary mb-3">Dashboard</a>-->
<!--            @endauth-->

        @if( auth()->user()->administrator )
            <div class="btn-group btn-group-lg" style="width:100%">
                <a href="{{ route('users.index') }}" class="btn btn-primary" style="width: 33.3%">All users</a>
                <a href="{{ route('managers.index') }}" class="btn btn-primary" style="width: 33.3%">Managers</a>
                <a href="{{ route('administrators.index') }}" class="btn btn-primary" style="width: 33.3%">Administrators</a>
            </div>
            <hr>
            <div class="btn-group btn-group-lg" style="width:100%">
                <button type="submit" class="btn btn-primary" style="width: 50%">Assign manager</button>
                <button type="submit" class="btn btn-primary" style="width: 50%">Remove manager</button>
            </div>
            <hr>
            <div class="btn-group btn-group-lg" style="width:100%">
                <a href="{{ route('users.create') }}" class="btn btn-primary" style="width: 50%">Create User</a>
                <button type="submit" class="btn btn-primary" style="width: 50%">Delete user</button>
            </div>
            <div>
                <a href="{{ route('users.loadData') }}">Upload</a>
            </div>

        @endif

	</div>
    <p>{{ auth()->user()->getCurrentTime()}}</p>
    <div>Registration closes in <span id="time">{{ auth()->user()->getCurrentTime()}}</span> minutes!</div>

    @if( !auth()->user()->administrator )
        <br>
        <div class="info" style="text-align: center">
            <h2>Total flexi balance: </h2>
            @if ( substr(auth()->user()->getFlexiBalance(),0,1) == "-" )
                <p style="color: red">{{ auth()->user()->getFlexiBalance() }}</p>
            @else
                <p style="color: green">{{ auth()->user()->getFlexiBalance() }}</p>
            @endif

            <h2>Clocking status</h2>
            @if ( !auth()->user()->isClockedIn() )
                <p style="color: green">Clocked In</p>
            @else
                <p style="color: red">Clocked Out</p>
            @endif
            <h2>Daily balance</h2>
            <p style="color: red">-4.35 hours</p>
        </div>

        <div class="btn-group btn-group-lg" style="width:100%">
            @if ( !auth()->user()->isClockedIn() )
            <a href="{{ route('clock-in.store') }}" class="btn btn-primary" style="width: 50%">Clock In</a>
            <a href="{{ route('clock-out.store') }}" class="btn btn-primary disabled" style="width: 50%">Clock Out</a>
            @else
            <a href="{{ route('clock-in.store') }}" class="btn btn-primary disabled" style="width: 50%">Clock In</a>
            <a href="{{ route('clock-out.store') }}" class="btn btn-primary" style="width: 50%">Clock Out</a>
            @endif
        </div>
        <hr>
        <div class="btn-group btn-group-lg" style="width:100%">
            <a href="{{ route('clockings.index') }}" class="btn btn-primary" style="width: 50%">View Records</a>
            <a href="{{ route('clockings.create') }}"class="btn btn-primary" style="width: 50%">Submit Request</a>
        </div>
    @endif
    @if( auth()->user()->manager )
        <hr>
        <div class="btn-group btn-group-lg" style="width:100%">
            <a href="{{ route('staff.index') }}" class="btn btn-primary" style="width: 50%">View Staff</a>
            <a href="{{ route('creations.index') }}" class="btn btn-primary" style="width: 50%">Manage Requests</a>
        </div>
    @endif

@endsection
