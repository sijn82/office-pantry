<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrinkBox extends Model
{
    protected $fillable = [

        // DrinkBox Info
        'drinkbox_id',
        'is_active',
        'delivered_by_id',
        // 'no_of_boxes', <-- as this is for wholesale only, we don't actually need a 'no. of boxes' field, but commenting out for now in case i'm wrong, or find a new purpose.
        'type',
        // Company Info
        'company_details_id',
        'delivery_day',
        'frequency',
        'previous_delivery_week',
        'next_delivery_week',
        // Product Information
        'product_id',
        'code',
        'name',
        'quantity',
        'unit_price',
        'case_price',
        'invoiced_at',
    ];

    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }

    public function box_items()
    {
        return $this->morphMany('App\OrderItem', 'orderable');
    }
}
