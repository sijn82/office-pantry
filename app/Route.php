<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    //

    protected $fillable = [
      'week_start',
      'company_name',
      'postcode',
      'address',
      'delivery_information',
      'fruit_crates',
      'fruit_boxes',
      'milk_2l_semi_skimmed',
      'milk_2l_skimmed',
      'milk_2l_whole',
      'milk_1l_semi_skimmed',
      'milk_1l_skimmed',
      'milk_1l_whole',
      'milk_1l_alt_coconut',
      'milk_1l_alt_unsweetened_almond',
      'milk_1l_alt_almond',
      'milk_1l_alt_unsweetened_soya',
      'milk_1l_alt_soya',
      'milk_1l_alt_oat',
      'milk_1l_alt_rice',
      'milk_1l_alt_cashew',
      'milk_1l_alt_lactose_free_semi',
      'drinks',
      'snacks',
      'other',
      'assigned_to',
      'delivery_day',
      'position_on_route',
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
        public function routes()
        {
            return $this->belongsTo(AssignedRoute::class);
        }
}
