@extends('layouts.app', ['title' => 'Create New Clocking' ])

@section('content')
<a href="{{ route('dashboard') }} "><button type="button" class="btn btn-primary mb-3" style="float: right">Back</button></a><br>

<h2 class="text-center">Click here to submit a new clocking:</h2>
<div class="btn-group btn-group-lg" style="width:100%; align-items: center; justify-content: center">
    <a href="{{ route('clockings.createinout') }}"class="btn btn-primary" style="width: 50%">Add new clocking</a>
</div>
<br><br>
<hr>
<br>
<h2 class="text-center">Click here to submit a new mid-day clocking:</h2>
<div class="btn-group btn-group-lg" style="width:100%; align-items: center; justify-content: center">
    <a href="{{ route('clockings.createoutin') }}" class="btn btn-primary" style="width: 50%">Add mid-day clocking</a>
</div>
<br><br>
@endsection
