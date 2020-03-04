<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackBoxDislike extends Model
{
    //

    protected $fillable = [

        'product_id',
    ];

    public function preference()
    {
        return $this->morphMany('App\Preference', 'preferable', 'connection_type', 'connection_id');
    }
}
