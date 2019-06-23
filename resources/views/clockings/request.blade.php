@extends('layouts.app', ['title' => 'Download Clockings' ])

@section('content')
<a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>
<form method="POST" action="{{ route('clockings.download' )}}" >
    @csrf

    <h4>Date From:</h4>
    <div class="form-group">
        <input type="date" class="form-control" name="date_from" value="{{ old('date') }}">
    </div>

    <h4>Date To:</h4>
    <div class="form-group">
        <input type="date" class="form-control" name="date_to" value="{{ old('date') }}">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection
