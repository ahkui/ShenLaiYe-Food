<?php

namespace App\Http\Controllers;

use App\Jobs\FetchNextPage;
use App\Restaurant;
use App\SearchResult;
use App\Service\PlacesApi;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    protected $googlePlaces;

    public function __construct()
    {
        $this->middleware('auth');
        $this->googlePlaces = new PlacesApi(env('GOOGLE_PLACE_API_KEY'));
    }

    public function home()
    {
        return view('home');
    }

    public function search()
    {
        $keyword = request()->name;
        $response = $this->googlePlaces->placeAutocomplete($keyword);
        $data = collect($response['predictions']);
        if ($data->count() > 0) {
            $data = $data->map(function ($item) use ($keyword) {
                return $this->convert_place_id($item['place_id'], $item['terms'][0]['value'], $keyword);
            });
            $db = $data;
            $data = SearchResult::where('keyword', 'like', "%{$keyword}%")->get();
            $union = $data->union($db);
            $data = $union->unique('place_id')->values();
        }
        if (request()->is_shop == 'true')
            return ['data'=>$data];
        return $data;
    }

    public function convert_place_id($place_id, $name, $keyword = null)
    {
        $data = SearchResult::where('place_id', '=', $place_id)->first();
        if ($data) {
            return $data;
        }
        $response = $this->googlePlaces->placeDetails($place_id, [
            'language'=> 'zh-TW',
        ]);
        $value = $response['result'];
        $data = SearchResult::create([
            'location'=> ['type'=>'Point', 'coordinates'=>[$value['geometry']['location']['lng'], $value['geometry']['location']['lat']]],
            'name'    => $name,
            'place_id'=> $place_id,
            'rating'  => isset($value['rating']) ? $value['rating'] : 0,
            'vicinity'=> isset($value['vicinity']) ? $value['vicinity'] : null,
            'keyword' => $keyword,
        ]);

        return $data;
    }

    public function search_near($temp = null)
    {
        $search = $temp ? $temp : SearchResult::find(request()->id);
        $type = 'food';
        $location = "{$search->location['coordinates'][1]}, {$search->location['coordinates'][0]}";
        $radius = request()->radius ? request()->radius : 1000;
        $response = $this->googlePlaces->nearbySearch($location, $radius, [
            'language'=> 'zh-TW',
            'type'    => $type,
        ]);
        $data = collect();
        if ($response['status'] == 'OK') {
            foreach ($response['results'] as $value) {
                $data->push(
                    Restaurant::firstOrCreate([
                        'place_id'=> $value['place_id'],
                    ], [
                        'location'=> [
                            'type'       => 'Point',
                            'coordinates'=> [
                                $value['geometry']['location']['lng'],
                                $value['geometry']['location']['lat'],
                            ],
                        ],
                        'name'    => $value['name'],
                        'place_id'=> $value['place_id'],
                        'rating'  => isset($value['rating']) ? $value['rating'] : 0,
                        'vicinity'=> $value['vicinity'],
                    ])
                );
            }

            if (isset($response['next_page_token'])) {
                FetchNextPage::dispatch($location, $radius, $type, $response['next_page_token'])->delay(now()->addSecond(5));
            }
        }
        $db = $this->geometry_search($search->location, $radius);

        $union = $data->union($db);
        $data = $union->unique('place_id');
        $data = ['data'=>$data->values(), 'center'=>$search->location['coordinates']];

        return $data;
    }

    public function searchByGps()
    {
        $data = new SearchResult();
        $data->location = [
            'type'        => 'Point',
            'coordinates' => [
                (float) request()->longitude,
                (float) request()->latitude,
            ],
        ];

        return $this->search_near($data);
    }

    public function geometry_search($location, $distance = 1000)
    {
        $data = Restaurant::where('location', 'near', [
            '$geometry'    => $location,
            '$maxDistance' => (int) $distance,
        ])->get();

        return $data;
    }

    public function get_review(){
        return [request()->input()];
    }
}
