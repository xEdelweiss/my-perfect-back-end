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

Route::group(['prefix' => 'api', 'middleware' => 'api.force'], function () {
    Route::resource('posts', 'PostsController');
    Route::resource('posts.revisions', 'PostsRevisionsController');
});

Route::group(['prefix' => 'auth', 'middleware' => 'api.force'], function () {
    // Authentication routes...
    Route::post('login', 'Auth\AuthController@postLogin');
    Route::get('logout', 'Auth\AuthController@getLogout');
    Route::get('user', 'Auth\AuthController@getUser');

    // Registration routes...
    Route::post('register', 'Auth\AuthController@postRegister');
});

/*
 * DEVELOPMENT
 */

if (config('app.debug')) {
    Route::group(['prefix' => 'dev'], function () {
        Route::get('fakelogin/{id?}', function($id = 1){
            Auth::login(\App\Models\User::whereId($id)->first());
            return Auth::user();
        });

        Route::get('users', function () {
            return \App\Models\User::findByRequest()
                ->with('posts')
                ->get();
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