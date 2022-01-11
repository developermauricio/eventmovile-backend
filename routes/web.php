<?php

use Illuminate\Support\Facades\Route;

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

Route::get('zoom', function () {
    return view('zoom');
});
Route::get('emailTest', function () {
    return view('testICS');
});

Route::get('eventExternalToken', function () {
    return view('eventExternalToken');
});

Route::get('editor', function (Request $request) {
    if($request->action == "gen-certificate" && $request->route == "certificate")
        return view('download');  
    else
        return view('editor');
});