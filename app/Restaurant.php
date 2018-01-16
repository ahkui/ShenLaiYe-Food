<?php

namespace App;
use App\RestaurantComment;
use App\RestaurantRate;

use Jenssegers\Mongodb\Eloquent\Model;

class Restaurant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'place_id',
        'name',
        'rating',
        'vicinity',
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $casts = [
        'name'     => 'string',
        'vicinity' => 'string',
    ];

    public function restaurant_comments()
    {
        return $this->embedsMany(RestaurantComment::class);
    }

    public function restaurant_rates()
    {
        return $this->embedsMany(RestaurantRate::class);
    }
}
