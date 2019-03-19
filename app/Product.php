<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [

        'is_active',
        'code',
        'name',
        'case_price',
        'case_size',
        'unit_cost',
        'unit_price',
        'vat',
        'sales_nominal',
        'cost_nominal',
        'profit_margin',
        'stock_level'
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
