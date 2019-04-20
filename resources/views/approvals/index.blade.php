@extends('layouts.app', ['title' => 'Staffhub: Approve Clockings' ])

@section('content')


@foreach ($users as $user)
    @if ( $user->clocking_corrections == null )
    @else



        @foreach ($user->clocking_corrections as $clocking)
<div class="row">
            <li class="list-group-item col-8">{{ $user->forename }} {{ $user->surname }}
                <div>{{ $clocking->clocking_type }}</div>
                <div style="float:right;">{{ $clocking->clocking_time }} </div>
            </li>
    <a href="{{ route('clock-in.store') }}" class="btn btn-success col-2" style="width: 50%">Approve</a>

    <a href="{{ route('clock-out.store') }}" class="btn btn-danger col-2" style="width: 50%">Reject</a>

</div>
        @endforeach

    @endif
@endforeach

@endsection
