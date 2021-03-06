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
    return response()->json(['message' => 'Go to /api'], 404);
});

Route::get('/{any?}', function () {
    return response()->json(['message' => 'Go to /api'], 404);
})->where('any', '.*');
