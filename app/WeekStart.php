<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeekStart extends Model
{
    //
    protected $table = 'week_start';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'current',
        'delivery_days'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
