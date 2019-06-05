@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')

@if($users->isEmpty())
<p>No users exist yet.</p>
@else
<ul class="list-group">
    @foreach ($users as $user)
    <li class="list-group-item">{{ $user->forename }} {{ $user->surname }}</li>
    @endforeach
</ul>

<br>
<div> {{ $users->links() }} </div>
@endif

@endsection
