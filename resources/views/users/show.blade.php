@extends('layouts.app', ['title' => $user->displayName ])

@section('content')
<a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>
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
                @if ($user->manager_id != null)
                <li>Line manager: {{ $manager->forename }} {{ $manager->surname }}</li>
                @endif
                <br>
                <li>Is a manager: {{ $user->manager ? 'Yes' : 'No'}}</li>
                <li>Is an administrator: {{ $user->administrator ? 'Yes' : 'No'}}</li>


			</ul>


		</div>
	</div>
    @if (Auth::user()->isAdmin())
    <div>

        <form method="POST"
              action="{{route ('users.destroy', ['id' => $user->id]) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-primary btn-lg" type="submit">Delete user</button>
            <a href="{{ route('users.edit', ['user' => $user]) }}" class="btn btn-primary btn-lg">Update user</a>
        </form>
    </div>
    @endif

@endsection
