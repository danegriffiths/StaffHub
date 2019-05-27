@extends('layouts.app', ['title' => 'Staffhub: Approve Clockings' ])

@section('content')
    @php
        $noneRemaining = 0;
    @endphp
    @foreach ($users as $user)
        @if ( $user->clocking_corrections == null )
        @else
            @for ($i = 1; $i < count($user->clocking_corrections); $i++)
                <p>{{$i}} of {{count($user->clocking_corrections)}}</p>



                /////////CARRY ON FROM HERE ***************************
                <div class="row" >
                    <div class="column col-3" style="margin: auto">{{ $user->forename }} {{ $user->surname }}</div>
                    <div class="column col-1" style="margin: auto">{{ $clocking->clocking_type }}</div>
                    <div class="column col-3" style="margin: auto">{{ $clocking->clocking_time }} </div>
                    <a href="{{ route('clocking.approve', ['clocking' => $clocking] ) }}" class="btn btn-success col-2" style="width: 50%; margin: 3px"">Approve</a>
                    <a href="{{ route('clocking.reject', ['clocking' => $clocking]) }}" class="btn btn-danger col-2" style="width: 50%; margin: 3px">Reject</a>
                </div>
            @endfor
            @php
            $noneRemaining = $noneRemaining + 1;
            @endphp
        @endif
    @endforeach
    @if ( $noneRemaining == 0 )
        <div class="alert alert-danger" role="alert">
            No clockings remaining!
        </div>
    @endif
@endsection
