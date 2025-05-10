<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');

});

Route::get('/test-mail', function () {
    return view('Email.test-mail');
    
});

Route::get('/hello', function () {
    return 'hello from web';
});



