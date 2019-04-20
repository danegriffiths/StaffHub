@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')

	@if($clocks->isEmpty())
		<p>No clockings exist yet.</p>
	@else 	  
		<ul class="list-group">
			@foreach ($clocks as $clock)
			    <li class="list-group-item">{{ $clock->clocking_type }} - {{ $clock->clocking_time }} </li>
			@endforeach
		</ul>	
	@endif
@endsection
