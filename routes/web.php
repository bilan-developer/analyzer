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

Route::get('/', 'BotController@index');
//Route::get('/analysis', 'BotController@analysis');
Route::post('/analysis', 'BotController@analysis');
Route::get('/save/{name}', 'BotController@save');
