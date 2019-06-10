@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')

	@if($clocks->isEmpty())
		<p>No clockings exist yet.</p>
	@else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">In/Out</th>
                    <th scope="col">Time</th>
                    <th scope="col">Manual Entry</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($clocks as $clock)
            <tr>
                <td>{{ $clock->clocking_type }}</td>
                <td>{{ $clock->clocking_time }}</td>
                @if ($clock->manual == false)
                    <td>No</td>
                @else
                    <td>Yes</td>
                @endif
                @if ($clock->rejected == true)
                    <td style="color: red">Rejected</td>
                @elseif ($clock->approved == false)
                    <td>Unapproved</td>
                @else
                    <td></td>
                @endif
                <td><button>Delete</button></td>
            </tr>
            @endforeach

            </tbody>
        </table>

    <br>
    <div> {{ $clocks->links() }} </div>
	@endif

@endsection
