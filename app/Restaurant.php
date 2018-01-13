<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'place_id',
        'name',
        'rating',
        'vicinity',
        'location',
    ];
    protected $casts = [
        'name' => 'string',
        'rating' => 'integer',
        'vicinity' => 'string',
    ];

    public function restaurant_comments()
    {
        return $this->embedsMany('RestaurantComment');
    }

    public function restaurant_rates()
    {
        return $this->embedsMany('RestaurantRate');
    }
}
