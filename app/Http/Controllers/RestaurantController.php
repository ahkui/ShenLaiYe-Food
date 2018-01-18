<?php

namespace App\Http\Controllers;

use App\Jobs\FetchNextPage;
use App\Restaurant;
use App\RestaurantComment;
use App\RestaurantRate;
use App\SearchResult;
use App\Service\PlacesApi;
use Cache;
use Carbon\Carbon;
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

    /**
     * 搜尋地址keyword，利用google API尋找、資料庫內尋找得到資料後，比對兩者資料，回傳資料。
     *
     * @return [type] [description]
     */
    public function search()
    {
        $keyword = request()->name;
        $response = $this->googlePlaces->placeAutocomplete($keyword); //google資料
        $data = collect($response['predictions']);
        if ($data->count() > 0) {
            $data = $data->map(function ($item) use ($keyword) {
                return $this->convert_place_id($item['place_id'], $item['terms'][0]['value'], $keyword);
            });
            $db = SearchResult::where('keyword', 'like', "%{$keyword}%")->get(); //資料庫資料
            $union = $db->union($data);
            $data = $union->unique('place_id')->values();
        }
        if (request()->is_shop == 'true') { //is_shop 店家
            // $data->map(function($value,$key){
            //     $res = Restaurant::firstOrCreate([
            //         'place_id'=> $value['place_id'],
            //     ], [
            //         'location'=> [
            //             'type'       => 'Point',
            //             'coordinates'=> [
            //                 $value['geometry']['location']['lng'],
            //                 $value['geometry']['location']['lat'],
            //             ],
            //         ],
            //         'name'    => $value['name'],
            //         'place_id'=> $value['place_id'],
            //         'vicinity'=> $value['vicinity'],
            //     ]);
            //     if (!$res->rating && isset($value['rating'])) {
            //         $recent_rate = new RestaurantRate();
            //         $recent_rate->rate = $value['rating'];
            //         $res->restaurant_rates()->save($recent_rate);
            //         $this->calculate_rating($res);
            //     }
            //     return $res;
            // });
            return ['data'=>$data];
        }

        return $data;
    }

    /**
     * [convert_place_id description]
     * 搜尋資料庫內有無紀錄，無則跟google拿placeDetails
     * 透過place_id得到資料.
     *
     * @param [type] $place_id [description]
     * @param [type] $name     [description]
     * @param [type] $keyword  [description]
     *
     * @return [type] [description]
     */
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

    /**
     * [searchByGps description]
     * 如果有GPS則用GPS獲得的座標.
     *
     * @return [type] [description]
     */
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

    /**
     * @param  [type]
     *
     * @return [type]
     */
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
                $res = Restaurant::firstOrCreate([
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
                    'vicinity'=> $value['vicinity'],
                ]);
                if (!$res->rating && isset($value['rating'])) {
                    $recent_rate = new RestaurantRate();
                    $recent_rate->rate = $value['rating'];
                    $res->restaurant_rates()->save($recent_rate);
                    $this->calculate_rating($res);
                }
                $data->push($res);
            }

            if (isset($response['next_page_token'])) {
                FetchNextPage::dispatch($location, $radius, $type, $response['next_page_token'])->delay(now()->addSecond(5));
            }
        }
        $db = $this->geometry_search($search->location, $radius);

        $union = $data->union($db);
        $data = $union->unique('place_id');

        return ['data'=>$data->values()->sortByDesc('rating')->values(), 'center'=>$search->location['coordinates']];
    }

    public function geometry_search($location, $distance = 1000)
    {
        $data = Restaurant::where('location', 'near', [
            '$geometry'    => $location,
            '$maxDistance' => (int) $distance,
        ])->get();

        return $data;
    }

    public function get_review()
    {
        $restaurant = Restaurant::find(request()->id);
        $restaurant->restaurant_comments->map(function ($item, $key) {
            $item->user;

            return $item;
        });
        $restaurant->reviews_count = $restaurant->restaurant_rates->count();

        $restaurant->user_rate = $restaurant->restaurant_rates->where('user_id', auth()->user()->id)->first();

        if ($restaurant->user_rate) {
            $restaurant->user_rate = $restaurant->user_rate->rate;
        }

        return $restaurant;
    }

    public function submit_review()
    {
        $restaurant = Restaurant::find(request()->id);
        if (request()->comment) {
            $comment = new RestaurantComment();
            $comment->user_id = auth()->user()->id;
            $comment->comment = request()->comment;
            $restaurant->restaurant_comments()->save($comment);
        }
        $recent_rate = $restaurant->restaurant_rates->where('user_id', auth()->user()->id)->first();
        if (!$recent_rate) {
            $recent_rate = new RestaurantRate();
            $recent_rate->user_id = auth()->user()->id;
        }
        $recent_rate->rate = request()->rate;
        $restaurant->restaurant_rates()->save($recent_rate);

        return $this->calculate_rating($restaurant);
    }

    public function get_suggest()
    {
        if (!Cache::has('suggest')) {
            $suggest = Restaurant::orderBy('rating', 'dces')->first();
            if ($suggest) {
                Cache::put('suggest', $suggest->_id, Carbon::tomorrow());
            }
        }

        return Cache::get('suggest', null);
    }

    private function calculate_rating(Restaurant $item)
    {
        $item->rating = round($item->restaurant_rates->avg('rate'), 1);
        $item->save();

        return $item;
    }
}
