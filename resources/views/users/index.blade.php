@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')


	<a href="{{ route('users.create') }} "><button type="button" class="btn btn-primary mb-3">Create User</button></a>

	@if($users->isEmpty())
		<p>No users exist yet.</p>
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
