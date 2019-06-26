@extends('layouts.app', ['title' => 'Absences' ])

@section('content')
    <a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>
    @if ( count($users) == 0 )
        <h4>No staff are currently assigned to you</h4>
    @else
        @foreach ($users as $user)
            @if ( count($user->absences) == 0 )
            @else
                <h4>Employee: {{ $user->forename }} {{ $user->surname }}</h4>
                <table class="table table-striped" style="vertical-align: center">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Hours Used</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($user->absences as $absence)
                        <tr>
                            <td>{{ $absence->date }}</td>
                            <td>{{ $absence->flexi_type }}</td>
                            <td>{{ substr($absence->flexi_balance_used, 0, 5) }}</td>
                            <td>
                                <a href="{{ route('absences.approve', ['id' => $absence->id]) }}" class="btn btn-success" style="width: 30%">Approve</a>
                                <a href="{{ route('absences.reject', ['id' => $absence->id]) }}" class="btn btn-danger" style="width: 30%">Reject</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            @endif
        @endforeach
    @endif
@endsection
