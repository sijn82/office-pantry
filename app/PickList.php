<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickList extends Model
{
    //
    protected $fillable = [
      'week_start',
      'company_name',
      'fruit_crates',
      'fruit_boxes',
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
      'assigned_to',
      'position_on_route',
      'delivery_day',
    ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'password', 'remember_token',
        ];

        // public function fod()
        // {
        //     return $this->belongsTo('FruitOrderingDocument', 'company_name','company_name');
        // }
}
