<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilkBox extends Model
{
    //
    protected $fillable = [

      'milk_1l_alt_coconut',
      'milk_1l_alt_unsweetened_almond',
      'milk_1l_alt_almond',
      'milk_1l_alt_unsweetened_soya',
      'milk_1l_alt_soya',
      'milk_1l_alt_lactose_free_semi',
      'semi_skimmed_2l',
      'skimmed_2l',
      'whole_2l',
      'semi_skimmed_1l',
      'skimmed_1l',
      'whole_1l',
      'pint_semi_skimmed',
      'pint_whole',
      'organic_semi_skimmed_1l',
      'organic_skimmed_1l',

    ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'password', 'remember_token',
        ];
        
        // public function companies()
        // {
        //     return $this->belongsTo(Company::class);
        // }
        public function companies()
        {
            return $this->belongsTo(CompanyDetails::class);
        }
}
