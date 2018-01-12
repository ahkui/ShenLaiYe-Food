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

Route::get('home', function () {
    return view('welcome');
});


Route::get('review', function () {
    return view('review');
});

Route::get('search', function () {
    return view('search');
});

Route::get('/', 'RestaurantController@home')->name('home');
Route::post('search', 'RestaurantController@search');
Route::post('search/near', 'RestaurantController@search_near');
Route::post('review', function () {
    return [request()->input(),request()->input()];
});

Auth::routes();
