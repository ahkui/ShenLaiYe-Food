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
    return view('welcome');
    return redirect('home');
});

Route::get('home', function () {
    return view('home');
});

Route::get('review', function () {
    return view('review');
});

Route::get('search', function () {
    return view('search');
});

Route::post('convert_place_id', 'RestaurantControlaler@convert_place_id');
Route::post('search', 'RestaurantController@search');
Route::post('search/near', 'RestaurantController@search_near');
Route::post('review', function () {
    return [request()->input(),request()->input()];
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');


Route::get('test', 'RestaurantController@test');

use App\Restaurant;
Route::get('geometry/{lng}/{lat}/{distance?}','RestaurantController@geometry_search');
Route::get('get/geometry/{lng}/{lat}/{distance?}','RestaurantController@geometry_search2');