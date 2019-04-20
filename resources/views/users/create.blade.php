@extends('layouts.app', ['title' => 'Create User' ])

@section('content')
<form method="POST" action="{{ route('users.store' )}}" >
    @csrf
    <div class="form-group">
        <label>Staff Number</label>
        <input type="text" class="form-control" name="staff_number" placeholder="Enter staff number" value="{{ old('staff_number') }}">
    </div>

    <div class="form-group">
        <label>Forename</label>
        <input type="text" class="form-control" name="forename" placeholder="Enter forename" value="{{ old('forename') }}">
    </div>

    <div class="form-group">
        <label>Surname</label>
        <input type="text" class="form-control" name="surname" placeholder="Enter Surname" value="{{ old('surname') }}">
    </div>

    <div class="form-group">
        <label>Department</label>
        <input type="text" class="form-control" name="department" placeholder="Select Department" value="{{ old('department') }}">
    </div>

    <div class="form-group">
        <label>Daily Hours Permitted</label>
        <input type="text" class="form-control" name="daily_hours_permitted" placeholder="Enter daily hours permitted" value="{{ old('daily_hours_permitted') }}">
    </div>

    <div class="form-group">
        <label>Weekly Hours Permitted</label>
        <input type="text" class="form-control" name="weekly_hours_permitted" placeholder="Enter weekly hours permitted" value="{{ old('weekly_hours_permitted') }}">
    </div>

    <div class="form-group">
        <label>Flexi Balance</label>
        <input type="text" class="form-control" name="flexi_balance" placeholder="Enter current flexi balance" value="{{ old('flexi_balance') }}">
    </div>

    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" name="manager"
               {{ (! empty(old('manager')) ? 'checked' : '') }}>
        <label class="form-check-label">Manager</label>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" name="administrator"
               {{ (! empty(old('administrator')) ? 'checked' : '') }}>
        <label class="form-check-label">Administrator</label>
    </div>

    <div class="form-group">
        <label>Email Address</label>
        <input type="email" class="form-control" name="email" placeholder="Enter email address" value="{{ old('email') }}">
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" class="form-control" name="password" placeholder="Enter password" value="{{ old('password') }}">
    </div>

    <div class="form-group">
        <label>Password Confirmation</label>
        <input type="password" class="form-control" name="password_confirmation" placeholder="Enter password again" value="{{ old('password_confirmation') }}">
    </div>

    <div class="form-group">
        <label>Manager ID</label>
        <input type="text" class="form-control" name="manager_id" placeholder="Select users manager" value="{{ old('manager_id') }}">
    </div>

    <button type="submit" class="btn btn-primary">Create User</button>
</form>
@endsection
