<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilkBox extends Model
{
    //
    protected $fillable = [

      '1l_milk_alt_coconut',
      '1l_milk_alt_unsweetened_almond',
      '1l_milk_alt_almond',
      '1l_milk_alt_unsweetened_soya',
      '1l_milk_alt_soya',
      '1l_milk_alt_lactose_free_semi',
      '2l_semi_skimmed',
      '2l_skimmed',
      '2l_whole',
      '1l_semi_skimmed',
      '1l_skimmed',
      '1l_whole',
      'pint_semi_skimmed',
      'pint_whole',
      '1l_organic_semi_skimmed',
      '1l_organic_skimmed',

    ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'password', 'remember_token',
        ];
}
