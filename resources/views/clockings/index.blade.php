@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')

	@if($clocks->isEmpty())
		<p>No clockings exist yet.</p>
	@else 	  
		<ul class="list-group">
			@foreach ($clocks as $clock)
                @if ($clock->approved == true)
			        <li class="list-group-item" style="background-color: limegreen">{{ $clock->clocking_type }} - {{ $clock->clocking_time }} </li>
                @elseif ($clock->rejected == true)
            <li class="list-group-item" style="background-color: lightgray"><strike> {{ $clock->clocking_type }} - {{ $clock->clocking_time }} </strike></li>
                @elseif ($clock->approved == false)
                    <li class="list-group-item">{{ $clock->clocking_type }} - {{ $clock->clocking_time }} </li>
                @endif

			@endforeach
		</ul>	
	@endif
@endsection
