<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

if (config('app.debug')) {
    Route::group(['prefix' => 'dev'], function () {
        Route::get('users', function () {
            return \App\Models\User::findByRequest()->get();
        })->middleware('api.force');

        Route::get('formatter-test', function(){
            throw new Exception('hello');

            return response()->json([
                'hello' => 'world',
                'a' => 123,
            ]);

            return 'hello';

            return \App\Models\User::all()->first();

            return \App\Models\User::all();

            return \App\Models\User::all()->toArray();
        });
    });
}