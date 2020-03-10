<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitBox extends Model
{
    //
    protected $casts = [
        'order_changes' => 'object',
    ];

    protected $fillable = [

        'is_active',
        'fruit_partner_id',
        'name',
        'company_details_id',
        //'route_id',
        'type',
        'previous_delivery_week',
        'delivery_week',
        'frequency',
        'week_in_month',
        'delivery_day',
        'fruitbox_total',
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
        'tailoring_fee',
        'discount_multiple',
        'invoiced_at',
        // Newly added fields to track changes
        'order_changes',
        'date_changed'


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
        // Looks like I've only added this relationship with fruitboxes and fruitpartners,
        // although the other boxes also use fruitpartners for delivery, dpd, apc etc are also classed as fruitpartners.
        public function fruit_partner()
        {
            return $this->belongsTo(FruitPartner::class, 'fruit_partner_id');
        }
}
