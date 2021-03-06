<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function(){

    //Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //Tasks
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);

    //My Profile
    Route::view('profile', 'profile')->name('profile');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');
   
    //Calendar
    Route::get('calendar', [\App\Http\Controllers\CalendarController::class, 'index'])
        ->name('calendar.index');
    Route::post('calendar', [\App\Http\Controllers\CalendarController::class, 'store'])
        ->name('calendar.store');
    Route::patch('calendar/update/{id}', [\App\Http\Controllers\CalendarController::class, 'update'])
        ->name('calendar.update');
    Route::delete('calendar/delete/{id}', [\App\Http\Controllers\CalendarController::class, 'delete'])
        ->name('calendar.delete');


});

require __DIR__.'/auth.php';
