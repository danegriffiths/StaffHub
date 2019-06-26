@extends('layouts.app', ['title' => $title])

@section('content')


	<a href="{{ route('users.create') }} "><button type="button" class="btn btn-primary mb-3">Create User</button></a>
    <a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a>
	@if($users->isEmpty())
		<h4>No staff are currently assigned to you</h4>
	@else 	  
		<ul class="list-group">
			@foreach ($users as $user)
			    <li class="list-group-item"><a href="{{ route('users.show', ['user' => $user] ) }}">{{ $user->forename }} {{ $user->surname }}</a></li>
			@endforeach
		</ul>

        <br>
        <div> {{ $users->links() }} </div>
	@endif

@endsection
