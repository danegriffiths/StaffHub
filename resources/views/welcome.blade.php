@extends('layouts.app', ['title' => 'DVLA StaffHub' ])

@section('content')
<h1 class="text-center">Welcome to the Staff Hub</h1>

@guest
<h4 class="text-center" style="color:teal">
    Please <a href="{{ route('login') }}" style="color:teal"><strong>login</strong></a> to get started.
</h4>
@endguest

@endsection
