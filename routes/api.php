<?php

use Illuminate\Http\Request;
use Illuminate\Support\Fascades\Route;
use App\Http\Controllers\AuthController;



Route::controller(AuthController::class)->prefix("auth")->middleware('api')->group(function() {
    Route::post('login', 'login')->name('auth.login');
    Route::post('register', 'register')->name('auth.register');
    Route::post('refresh', 'refresh')->name('auth.refresh');


    Route::middleware('jwt.auth.token')->group(function(){
        Route::post("logout", "logout")->name("auth.logout");
    Route::get('user-profile', 'userProfile')->name('auth.user.profile');
    });

});