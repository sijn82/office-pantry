<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'allergen_info' => 'array'
    ];
    //
    protected $fillable = [

        'is_active',
        'brand',
        'flavour',
        'code',
        'buying_case_cost',
        'selling_case_price',
        'buying_case_size',
        'selling_case_size',
        'buying_unit_cost',
        'selling_unit_price',
        'vat',
        'supplier',
        'sales_nominal',
        'profit_margin',
        'stock_level',
        'allergen_info'
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
