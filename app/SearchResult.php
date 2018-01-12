<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class SearchResult extends Model
{
    protected $fillable = [
        'place_id',
        'location',
        'name',
        'vicinity',
        'keyword',
        'rating',
    ];
}
