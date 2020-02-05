<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'allergen_info' => 'array',
        'dietary_requirements' => 'array'
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
        'allergen_info',
        'dietary_requirements'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];

    // public function snackbox()
    // {
    //     return $this->hasMany(SnackBox::class);
    // }

    // This will replace the allergies part of the allergies_and_dietary_requirements() function above.
    public function allergy_info()
    {   // In this instance connection_type/connection_id are the expected column names so don't really need declaring.
        return $this->morphMany('App\AllergyInfo', 'connectable', 'connection_type', 'connection_id');
    }

}
