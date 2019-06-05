@extends('layouts.app', ['title' => 'Update ' . $user->displayName] )

@section('content')



<form method="POST" action="{{ route('users.update', ['id' => $user->id]) }}">

    @csrf
    @method('PATCH')
    <div>

        <div class="form-group">
            Forename: <input type="text" class="form-control" name="forename" value="{{ $user->forename }}">
        </div>
        <div class="form-group">
            Surname: <input type="text" class="form-control" name="surname"  value="{{ $user->surname }}"/>
        </div>
        <div class="form-group">
            Email: <input type="email" class="form-control" name="email" value="{{ $user->email }}"/>
        </div>

        <div class="form-group">
            <label>Department</label>
            <select name="department" class="form-control">
                <option value="{{ $user->department }}" selected>
                    {{ $user->department }}
                </option>
                @foreach ($departments as $department)
                <option value="{{ $department }}" >
                    {{ $department }}
                </option>
                @endforeach
            </select>
        </div>

        @if ($user->manager_id != null)
        <div class="form-group">
            Line manager:
                <select name="manager_id" class="form-control">
                    <option value="no_manager">(no manager)</option>
                    <option value="{{ $user->manager_id }}" selected>
                        {{ $user->managerName()->forename . ' ' . $user->managerName()->surname }}
                    </option>
                    @foreach ($managers as $manager)
                    <option value="{{ $manager->staff_number }}">
                        {{ $manager->forename . ' ' . $manager->surname }}
                    </option>
                    @endforeach
                </select>
        </div>
        @endif

        <div class="form-group">
            Daily hours permitted: <input type="text" class="form-control" name="daily_hours_permitted"  value="{{ $user->daily_hours_permitted }}"/>
        </div>
        <div class="form-group">
            Weekly hours permitted: <input type="text" class="form-control" name="weekly_hours_permitted"  value="{{ $user->weekly_hours_permitted }}"/>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="manager"
                   {{ (! empty(old('manager')) ? 'checked' : '') }}>
            <label class="form-check-label">Manager</label>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="administrator"
                   {{ (! empty(old('manager')) ? 'checked' : '') }}>
            <label class="form-check-label">Administrator</label>
        </div>

    </div>


    <button type="submit" class="btn btn-primary">Update</button>
</form>

@endsection

