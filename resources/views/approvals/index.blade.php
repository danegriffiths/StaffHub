@extends('layouts.app', ['title' => 'Approve Clockings' ])

@section('content')
    <a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>
    @php
        $noneRemaining = 0;
    @endphp

    @foreach ($users as $user)
        @if ( $user->clocking_corrections == null )
        @else
            <h4>Employee: {{ $user->forename }} {{ $user->surname }}</h4>
            <table class="table table-striped" style="vertical-align: center">
                <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Time</th>
                        <th scope="col">Type</th>
                        <th scope="col">Time</th>
                        <th scope="col">Approve/Reject</th>
                    </tr>
                </thead>
                @for ($i = 0; $i < count($user->clocking_corrections); $i++)
                    <tbody>
                        <td>{{ $user->clocking_corrections[$i]->clocking_type }}</td>
                        <td>{{ $user->clocking_corrections[$i]->clocking_time }}</td>
                        @php
                            $j = $i + 1;
                        @endphp
                        <td>{{ $user->clocking_corrections[$j]->clocking_type }}</td>
                        <td>{{ $user->clocking_corrections[$j]->clocking_time }}</td>
                        <td>
                            <a href="{{ route('clocking.approve', ['clocking1' => $user->clocking_corrections[$i], 'clocking2' => $user->clocking_corrections[$j]] ) }}" class="btn btn-success" style="width: 30%">Approve</a>
                            <a href="{{ route('clocking.reject', ['clocking1' => $user->clocking_corrections[$i], 'clocking2' => $user->clocking_corrections[$j]] ) }}" class="btn btn-danger" style="width: 30%">Reject</a>
                        </td>
                    </tbody>
                    @php
                        $i++;
                    @endphp
                @endfor
            </table>
            @php
            $noneRemaining = $noneRemaining + 1;
            @endphp
        @endif
    @endforeach

    @if ( $noneRemaining == 0 )
        <div class="alert alert-danger fade-message" role="alert">
            No clockings remaining!
        </div>
    @endif
@endsection
