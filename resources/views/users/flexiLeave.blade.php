@extends('layouts.app', ['title' => 'Create Flexi Leave' ])

@section('content')
<form method="POST" action="{{ route('users.store-leave' )}}" >
    @csrf

    <h4>Current flexi balance:</h4>
    @if ( substr(auth()->user()->getFlexiBalance(),0,1) == "-" )
    <p>{{ auth()->user()->getFlexiBalance() }}</p>
    @else
    <p>{{ auth()->user()->getFlexiBalance() }}</p>
    @endif

    <h4>Date:</h4>
    <div class="form-group">
        <input type="date" class="form-control" name="date" value="{{ old('date') }}">
    </div>

    <div class="form-group">
        <label>Type:</label>
        <select name="flexi-type" class="form-control">
            <option value="" disabled selected>Select Full or Half day</option>
            <option value="full">Full Day</option>
            <option value="half">Half Day</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection
