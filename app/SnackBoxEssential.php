<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackBoxEssential extends Model
{
    //

    protected $fillable = [

        'product_id',
        'quantity',
    ];

    public function preference()
    {
        return $this->morphMany('App\Preference', 'preferable', 'connection_type', 'connection_id');
    }
}
