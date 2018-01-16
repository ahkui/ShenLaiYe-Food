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

Route::get('/', 'RestaurantController@home')->name('home');
Route::post('search', 'RestaurantController@search')->name('search');
Route::post('search/gps', 'RestaurantController@searchByGps')->name('search.gps');
Route::post('search/near', 'RestaurantController@search_near')->name('search.near');
Route::post('review', 'RestaurantController@get_review')->name('review');
Route::put('review', 'RestaurantController@submit_review')->name('review.save');

Auth::routes();
