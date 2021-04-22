<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('concerts/{concert}', 'ConcertsController@show');
Route::post('/concerts/{concert}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmation_order}', 'OrderController@show');
