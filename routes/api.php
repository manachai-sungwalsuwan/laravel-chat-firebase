<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/rooms', 'FCMController@getRooms');
Route::post('/get-room', 'FCMController@getRoom');
Route::post('/check-room', 'FCMController@checkRoom');
Route::post('/save-room', 'FCMController@saveRoom');