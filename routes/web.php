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

Route::get('dashboard', 'DashboardController@index')->name('dashboard');


Route::get('/users', 'UserController@userIndex')->name('users.index');
Route::get('/managers', 'UserController@managerIndex')->name('managers.index');
Route::get('/administrators', 'UserController@administratorIndex')->name('administrators.index');
Route::get('/staff', 'UserController@staffIndex')->name('staff.index');
Route::get('/approvals', 'UserController@clockingCreationsIndex')->name('creations.index');
Route::get('/users/create', 'UserController@create')->name('users.create');
Route::post('/users', 'UserController@store')->name('users.store');
Route::get('/users/{user}', 'UserController@show')->name('users.show');
Route::get('/user', 'UserController@importCsv')->name('users.loadData');

Route::get('/clock-in', 'ClockingController@clockIn')->name('clock-in.store');
Route::get('/clock-out', 'ClockingController@clockOut')->name('clock-out.store');
Route::get('/clockings', 'ClockingController@getClockings')->name('clockings.index');
Route::get('/clockings/create', 'ClockingController@create')->name('clockings.create');
Route::post('/clockings', 'ClockingController@store')->name('clockings.store');
Route::get('/clocking/approve/{clocking}', 'ClockingController@approve')->name('clocking.approve');
Route::get('/clocking/reject/{clocking}', 'ClockingController@reject')->name('clocking.reject');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
