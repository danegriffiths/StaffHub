@extends('layouts.app', ['title' => 'Absences' ])

@section('content')
<a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>
@if($absences->isEmpty())
    <h4>Absence list is empty</h4>
@else
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
        @foreach ($absences as $absence)
            <tr>
                <td>{{ $absence->date }}</td>
                <td>{{ $absence->flexi_type }}</td>
                <td>{{ substr($absence->flexi_balance_used, 0, 5) }}</td>
                <td align="right">
                    <form method="POST"
                          action="{{route ('absences.destroy', ['id' => $absence->id]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-primary btn-sm" type="submit">Delete Absence</button>
                    </form>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <br>
    <div> {{ $absences->links() }} </div>
@endif
@endsection
