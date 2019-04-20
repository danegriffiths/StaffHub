@extends('layouts.app', ['title' => $user->displayName ])

@section('content')
  	<div class="row">
	    <div class="col-md">
			<p>User details:</p>
			<ul>
				<li>Name: {{ $user->forename}} {{ $user->surname}}</li>
                <li>Staff number: {{ $user->staff_number}}</li>
                <li>Department: {{ $user->department}}</li>
                <li>Flexi balance: {{ $user->flexi_balance}}</li>
                <li>Daily hours permitted: {{ $user->daily_hours_permitted}}</li>
                <li>Weekly hours permitted: {{ $user->weekly_hours_permitted}}</li>
                <li>Email: {{ $user->email}}</li>
                <li>Manager: {{ $user->manager ? 'Yes' : 'No'}}</li>
                <li>Administrator: {{ $user->administrator ? 'Yes' : 'No'}}</li>

                @if (!$user->manager_id === null)
                    <li>Line manager: {{ $user->manager_id}}</li>
                @endif
			</ul>


		</div>
	</div>

@endsection
