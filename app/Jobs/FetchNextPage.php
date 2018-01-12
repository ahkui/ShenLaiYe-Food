<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Service\PlacesApi;
use App\Restaurant;

class FetchNextPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $location; 
    protected $radius; 
    protected $type; 
    protected $next_token; 
    protected $key; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($location,$radius,$type,$next_token = null)
    {
        $this->key = 'AIzaSyCDADeOH-8PmS0Nu5fqbbKsR3EZT1FAtSw';
        // $this->key = 'AIzaSyDWK5BodNliXvHgUmlxeMFMv8jUFvKTMrY';
        $this->location = $location;
        $this->radius = $radius;
        $this->type = $type;
        $this->next_token = $next_token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dump("{$this->location} start queue!");
        $googlePlaces = new PlacesApi($this->key);
        $response = $googlePlaces->nearbySearch($this->location,$this->radius,[
            'language'=>'zh-TW',
            'type'=>$this->type,
        ]+(
            ($this->next_token == null)?
                []:
                ['pagetoken'=>$this->next_token])
        );
        dump("{$this->location} {$response['status']}");
        if($response['status'] == 'OK'){
            foreach ($response['results'] as $value) {
                Restaurant::firstOrCreate(['place_id'=>$value['place_id']],[
                    'location'=>['type'=>'Point', 'coordinates'=>[$value['geometry']['location']['lng'],$value['geometry']['location']['lat']]],
                    'name'=>$value['name'],
                    'place_id'=>$value['place_id'],
                    'rating'=>isset($value['rating']) ? $value['rating']: 0,
                    'vicinity'=>$value['vicinity'],
                ]);
            }
            if(isset($response['next_page_token'])) {
                dump("{$this->location} queue next!");
                FetchNextPage::dispatch($this->location,$this->radius,$this->type,$response['next_page_token'])->delay(now()->addSecond(5));
            }
            else{
                dump("{$this->location} queue done");
            }
        }
    }
}
