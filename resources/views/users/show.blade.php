@extends('layouts.app', ['title' => $user->displayName ])

@section('content')
@if (Auth::user()->isAdmin())
<div>

    <form method="POST"
          action="{{route ('users.destroy', ['id' => $user->id]) }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-primary mb-3" type="submit" style="width: 40%">Delete user</button>
        <a href="{{ route('users.edit', ['user' => $user]) }}" class="btn btn-primary mb-3" style="width: 40%">Update user</a>
        <a href="{{ url()->previous() }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br><br>

    </form>
</div>
@else
<a href="{{ url()->previous() }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br><br>
@endif
    <table class="table table-striped">
        <tbody>
            <tr>
                <td>Name</td>
                <td>{{ $user->forename }} {{ $user->surname }}</td>
            </tr>
            <tr>
                <td>Staff number</td>
                <td>{{ $user->staff_number }}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{ $user->department }}</td>
            </tr>
            <tr>
                <td>Flexi balance</td>
                @if ( $user->latest_flexi_balance == null )
                    <td>{{ $user->flexi_balance }}</td>
                @else
                    <td>{{ $user->latest_flexi_balance }}</td>
                @endif
            </tr>
            <tr>
                <td>Daily hours permitted</td>
                <td>{{ $user->daily_hours_permitted }}</td>
            </tr>
            <tr>
                <td>Weekly hours permitted</td>
                <td>{{ $user->weekly_hours_permitted }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $user->email }}</td>
            </tr>
            @if ($user->manager_id != null)
                <tr>
                    <td>Line manager</td>
                    <td>{{ $manager->forename }} {{ $manager->surname }}</td>
                </tr>
            @endif
            <tr>
                <td>Is a manager</td>
                <td>{{ $user->manager ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td>Is an administrator</td>
                <td>{{ $user->administrator ? 'Yes' : 'No' }}</td>
            </tr>
        </tbody>
    </table>
    <hr>

    @if (Auth::user()->isManager())

        @if($clockings->isEmpty())
        <h4>No clockings submitted yet.</h4>
        @else
        <h4>Clockings</h4>
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

    @endif

@endsection
