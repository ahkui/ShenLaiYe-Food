<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PlacesApi;
use App\Restaurant;
use GuzzleHttp\Client;

class RestaurantController extends Controller
{
    public function __construct(){

        $this->key = 'AIzaSyCDADeOH-8PmS0Nu5fqbbKsR3EZT1FAtSw';
        // $this->key = 'AIzaSyCU8D8CL7EkRjDnfhFBJRHoNTpM0pOqE6Q';

        $this->googlePlaces = new PlacesApi($this->key);
    }

    public function search(){
        $response = $this->googlePlaces->placeAutocomplete(request()->name);
        return $response['predictions'];
    }


    public function search2(){
        dump($this->search());
    }

    public function geometry_search($lng,$lat,$distance = 1000){
        $data = Restaurant::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    (float)$lng,
                    (float)$lat,
                ],
            ],
            '$maxDistance' => (integer)$distance,
        ])->get();
        return $data;
    }

    public function geometry_search2($lng,$lat,$distance = 1000){
        dd($this->geometry_search($lng,$lat,$distance));
    }

    public function convert_place_id(){
        return "qweqweqwe";
    }
    // public function place_id_to_
    /**
     * 測試頁面
     * @return \Illuminate\Http\Response
     */
    public function test(){
        $type = 'food';
        $location = '24.178829, 120.646438';
        $radius = 1000;
        $response = $this->googlePlaces->nearbySearch($location,$radius,[
            // 'language'=>'zh-TW',
            'language'=>'en',
            'type'=>$type,
        ]);
        $data = collect();
        if ($response['status']=='OK')
        foreach ($response['results'] as $value) 
            $data->push(Restaurant::firstOrCreate(['place_id'=>$value['place_id']],[
                            'location'=>['type'=>'Point', 'coordinates'=>[$value['geometry']['location']['lng'],$value['geometry']['location']['lat']]],
                            'name'=>$value['name'],
                            'place_id'=>$value['place_id'],
                            'rating'=>isset($value['rating']) ? $value['rating']: 0,
                            'vicinity'=>$value['vicinity'],
                        ]));
        dump($data);
    }
}
