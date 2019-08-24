<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'], function () {

    Route::group(['prefix' => '/contact'], function () {
        Route::post('/', 'ContactController@submit')->middleware('throttle:2,5');

        // Route::get('/{id}', 'PlayerController@show');
        // Route::post('/', 'PlayerController@store');
        // Route::put('/', 'PlayerController@update');
        // Route::delete('/', 'PlayerController@destroy');
    });

    Route::group(['prefix' => '/convert'], function () {
        Route::post('/', 'YoutubeVideoController@store');
    });
});



Route::get('/{any?}', function () {
    return response()->json(['message' => 'Api not found!'], 404);
})->where('any', '.*');
