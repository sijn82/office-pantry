<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitOrderingDocument extends Model
{
    //
    protected $fillable = [
      'week_start',
      'company_name',
      'company_supplier',
      'pointless',
      'delivery_notes',
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
      'milk_1l_alt_coconut',
      'milk_1l_alt_unsweetened_almond',
      'milk_1l_alt_almond',
      'milk_1l_alt_unsweetened_soya',
      'milk_1l_alt_soya',
      'milk_1l_alt_lactose_free_semi',
      'filter_coffee_250g',
      'expresso_coffee_250g',
      'muesli',
      'granola',
      'still_water',
      'sparkling_water',
      'milk_2l_semi_skimmed',
      'milk_2l_skimmed',
      'milk_2l_whole',
      'milk_1l_semi_skimmed',
      'milk_1l_skimmed',
      'milk_1l_whole',
      'milk_pint_semi_skimmed',
      'milk_pint_whole',
      'milk_pint_whole',
      'milk_1l_organic_semi_skimmed',
      'milk_1l_organic_skimmed',
      // 'snack_boxes',
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

    // public function updatePicklist()
    // {
    //   return $this->hasMany('App\PickList', 'company_name', 'company_name');
    // }

}
