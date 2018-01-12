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
});

Route::get('home', function () {
    return view('home');
});

Route::get('review', function () {
    return view('review');
});

Route::post('review', function () {
    return [request()->input(),request()->input()];
});

Route::get('search', function () {
    return view('search');
});

Route::post('search', 'RestaurantController@search');
Route::get('get/search/{name}', 'RestaurantController@search');

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');


Route::get('test', 'RestaurantController@test');

use App\Restaurant;
Route::get('geometry/{lng}/{lat}/{distance?}',function($lng,$lat,$distance = 1000){
    dump(Restaurant::all());
    dump(
        Restaurant::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    (float)$lng,
                    (float)$lat,
                ],
            ],
            '$maxDistance' => (integer)$distance,
        ])->get()
    );
});