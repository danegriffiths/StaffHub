@extends('layouts.app', ['title' => 'Create New Clocking' ])

@section('content')
<form method="POST" action="{{ route('clockings.storeoutin' )}}" >
    @csrf

    <h4>Date:</h4>
    <div class="form-group">
        <input type="date" class="form-control" name="date" value="{{ old('date') }}">
    </div>

    <h4>Clock-Out Time:</h4>
    <div class="form-group">
        <input type="time" class="form-control" name="time_out" value="{{ old('time_out') }}">
    </div>

    <h4>Clock-In Time:</h4>
    <div class="form-group">
        <input type="time" class="form-control" name="time_in" value="{{ old('time_in') }}">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection
