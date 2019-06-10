@extends('layouts.app', ['title' => 'Staffhub: Users' ])

@section('content')

@if($absences->isEmpty())
    <p>No absences submitted yet.</p>
@else
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">Hours Used</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($absences as $absence)
            <tr>
                <td>{{ $absence->date }}</td>
                <td>{{ $absence->flexi_type }}</td>
                <td>{{ substr($absence->flexi_balance_used, 0, 5) }}</td>
                <td><button>Delete</button></td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <br>
    <div> {{ $absences->links() }} </div>
@endif
@endsection
