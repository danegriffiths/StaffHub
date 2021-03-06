<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Routes for authenticated users
Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/users', 'UserController@userIndex')->name('users.index');
    Route::get('/managers', 'UserController@managerIndex')->name('managers.index');
    Route::get('/administrators', 'UserController@administratorIndex')->name('administrators.index');
    Route::get('/staff', 'UserController@staffIndex')->name('staff.index');
    Route::get('/approvals', 'UserController@clockingCreationsIndex')->name('creations.index');
    Route::get('/users/create', 'UserController@create')->name('users.create');
    Route::post('/users', 'UserController@store')->name('users.store');
    Route::get('/users/{user}', 'UserController@show')->name('users.show');
    Route::delete('/users/{user}', 'UserController@destroy')->name('users.destroy');
    Route::get('/users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::patch('users/update/{id}', 'UserController@update')->name('users.update');
    Route::get('/user-import', 'UserController@importCsv')->name('users.loadData');

    Route::get('/clockings', 'ClockingController@index')->name('clockings.index');
    Route::get('/clock-in', 'ClockingController@clockIn')->name('clock-in.store');
    Route::get('/clock-out', 'ClockingController@clockOut')->name('clock-out.store');
    Route::get('/clockings/create', 'ClockingController@create')->name('clockings.create');
    Route::delete('/clockings/{clocking}', 'ClockingController@destroy')->name('clockings.destroy');
    Route::get('/clockings/create-in-out', 'ClockingController@createInOut')->name('clockings.createinout');
    Route::get('/clockings/create-out-in', 'ClockingController@createOutIn')->name('clockings.createoutin');
    Route::post('/clockings-in-out', 'ClockingController@storeInOut')->name('clockings.storeinout');
    Route::post('/clockings-out-in', 'ClockingController@storeOutIn')->name('clockings.storeoutin');
    Route::get('/clockings-request', 'ClockingController@request')->name('clockings.request');
    Route::post('/clockings-download', 'ClockingController@download')->name('clockings.download');
    Route::get('/clocking/approve/{clocking}', 'ClockingController@approve')->name('clocking.approve');
    Route::get('/clocking/reject/{clocking}', 'ClockingController@reject')->name('clocking.reject');
    Route::get('/clocking/getBalance', 'ClockingController@getDailyBalance')->name('clocking.getBalance');

    Route::get('/absences', 'AbsenceController@index')->name('absences.index');
    Route::get('/absences-manager', 'AbsenceController@managerIndex')->name('absences.managerIndex');
    Route::get('/absences/create', 'AbsenceController@create')->name('absences.create');
    Route::post('/absences', 'AbsenceController@store')->name('absences.store');
    Route::delete('/absences/{absence}', 'AbsenceController@destroy')->name('absences.destroy');
    Route::get('/absences/approve/{absence}', 'AbsenceController@approve')->name('absences.approve');
    Route::get('/absences/reject/{absence}', 'AbsenceController@reject')->name('absences.reject');

});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
