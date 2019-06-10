@extends('layouts.app', ['title' => 'Clockings' ])

@section('content')

	@if($clockings->isEmpty())
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
            @foreach ($clockings as $clocking)
            <tr>
                <td>{{ $clocking->clocking_type }}</td>
                <td>{{ $clocking->clocking_time }}</td>
                @if ($clocking->manual == false)
                    <td>No</td>
                @else
                    <td>Yes</td>
                @endif
                @if ($clocking->rejected == true)
                    <td style="color: red">Rejected</td>
                @elseif ($clocking->approved == false)
                    <td>Unapproved</td>
                @else
                    <td></td>
                @endif
                @if ($clocking->manual == true)
                    <td align="right">
                        <form method="POST"
                              action="{{route ('clockings.destroy', ['id' => $clocking->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-primary btn-sm" type="submit">Delete Clocking</button>
                        </form>
                    </td>
                @else
                <td></td>
                @endif
            </tr>
            @endforeach

            </tbody>
        </table>

    <br>
    <div> {{ $clockings->links() }} </div>
	@endif

@endsection
