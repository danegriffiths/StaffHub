@extends('layouts.app', ['title' => 'Approve Clockings' ])

@section('content')
    @php
        $noneRemaining = 0;
    @endphp
    @foreach ($users as $user)
        @if ( $user->clocking_corrections == null )
        @else
            @for ($i = 0; $i < count($user->clocking_corrections) - 1; $i++)
                <p>Employee: {{ $user->forename }} {{ $user->surname }}</p>
                <div class="row" >
                    <div class="column col-1" style="margin: auto">{{ $user->clocking_corrections[$i]->clocking_type }}</div>
                    <div class="column col-2" style="margin: auto">{{ $user->clocking_corrections[$i]->clocking_time }}</div>
                    @php
                        $j = $i+1;
                    @endphp
                    <div class="column col-1" style="margin: auto">{{ $user->clocking_corrections[$j]->clocking_type }}</div>
                    <div class="column col-2" style="margin: auto">{{ $user->clocking_corrections[$j]->clocking_time }}</div>
                    <a href="{{ route('clocking.approve', ['clocking' => $user->clocking_corrections[$i]] ) }}" class="btn btn-success col-2" style="width: 50%; margin: 3px"">Approve</a>
                    <a href="{{ route('clocking.reject', ['clocking' => $user->clocking_corrections[$i]]) }}" class="btn btn-danger col-2" style="width: 50%; margin: 3px">Reject</a>
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
