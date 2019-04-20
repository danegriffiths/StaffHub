@extends('layouts.app', ['title' => 'Create New Clocking' ])

@section('content')
<form method="POST" action="{{ route('clockings.store' )}}" >
    @csrf

    <div class="form-group">
        <label for="appt">Date:</label>
        <input type="date" class="form-control" name="date" value="{{ old('date') }}">
    </div>

    <div class="form-group">
        <label for="appt">Time:</label>
        <input type="time" class="form-control" name="time" value="{{ old('time') }}">
    </div>

    <div class="form-group">
        <input type="radio" name="type"
               value="IN">  Clock-In
        <br>
        <input type="radio" name="type"
               value="OUT">  Clock-Out
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection
