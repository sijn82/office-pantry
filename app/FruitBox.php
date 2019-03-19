<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitBox extends Model
{
    //
    protected $fillable = [

        'is_active',
        'name',
        'company_id',
        'route_id',
        'type',
        'frequency',
        'delivery_day',
        'boxes_total',
        'deliciously_red_apples',
        'pink_lady_apples',
        'red_apples',
        'green_apples',
        'satsumas',
        'pears',
        'bananas',
        'nectarines',
        'limes',
        'lemons',
        'grapes',
        'seasonal_berries',
        'oranges',
        'cucumbers',
        'mint',
        'organic_lemons',
        'kiwis',
        'grapefruits',
        'avocados',
        'root_ginger',

    ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'password', 'remember_token',
        ];

        public function companies()
        {
            return $this->belongsTo(Company::class);
        }
        
        public function fruit_partners()
        {
            return $this->belongsTo(FruitPartner::class);
        }
}
