<?php

use Sunra\PhpSimple\HtmlDomParser;

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
});

//Envía el número diario del Mediodía y la Noche
Route::get('/parser', 'ParserController@sendNumber')->name('send_number');

