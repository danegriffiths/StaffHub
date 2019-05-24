@extends('layouts.app', ['title' => 'Create New Clocking' ])

@section('content')
<p>Click here to submit a new mid-day clocking:</p>
<div class="btn-group btn-group-lg" style="width:100%; align-items: center; justify-content: center">
    <a href="{{ route('clockings.createoutin') }}" class="btn btn-primary" style="width: 50%">Add mid-day clocking</a>
</div>
<br><br>

<p>Click here to submit a new clocking:</p>
<div class="btn-group btn-group-lg" style="width:100%; align-items: center; justify-content: center">
    <a href="{{ route('clockings.createinout') }}"class="btn btn-primary" style="width: 50%">Add new clocking</a>
</div>
@endsection
