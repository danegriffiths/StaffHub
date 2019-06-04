@extends('layouts.app', ['title' => 'Update ' . $user->displayName] )

@section('content')



<form method="POST" action="{{ route('users.update', ['id' => $user->id]) }}">

    @csrf
    @method('PATCH')
    <div>
        Forename: <input type="text" name="forename"  value="{{ $user->forename }}"/><br>
        Surname: <input type="text" name="surname"  value="{{ $user->surname }}"/><br>
        Email: <input type="email" name="email"  value="{{ $user->email }}"/><br>
        Department: <input type="text" name="department"  value="{{ $user->department }}"/><br>
        @if ($user->manager_id != null)
        Line manager: <input type="text" name="manager_id" value="{{ $user->managerName() }}"/><br>
        @endif
        @if ($user->manager_id != null)
        Line manager:
            <select name="manager">
                <option value="no_manager">(no manager)</option>
                @foreach ($managers as $manager)
                <option value="{{ $manager->id }}">
                    {{ $manager->forename . ' ' . $manager->surname }}
                </option>
                @endforeach
            </select>
        @endif<br>
        Daily hours permitted: <input type="text" name="daily_hours_permitted"  value="{{ $user->daily_hours_permitted }}"/><br>
        Weekly hours permitted: <input type="text" name="weekly_hours_permitted"  value="{{ $user->weekly_hours_permitted }}"/><br>
        Manager: <input type="checkbox" class="form-check-input" name="manager" value="{{ $user->manager }}"/><br>
        Administrator: <input type="checkbox" class="form-check-input" name="administrator" value="{{ $user->administrator }}"/><br>
    </div>


    <button type="submit">Send</button>
</form>

@endsection

