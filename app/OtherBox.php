<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherBox extends Model
{
    protected $fillable = [

        // OtherBox Info
        'otherbox_id',
        'is_active',
        'name',
        'delivered_by_id',
        // 'no_of_boxes',
        'type',
        // Company Info
        'company_details_id',
        'delivery_day',
        'frequency',
        'previous_delivery_week',
        'delivery_week',
        // Product Information
        // 'product_id',
        // 'code',
        // 'name',
        // 'quantity',
        // 'unit_price',
        // 'case_price',
        'invoiced_at',
    ];

    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }

    public function box_items()
    {
        return $this->morphMany('App\OrderItem', 'orderable', 'box_type', 'box_id');
    }
    
    // This will replace the allergies part of the allergies_and_dietary_requirements() function above.
    public function allergy_info()
    {   // In this instance connection_type/connection_id are the expected column names so don't really need declaring.
        return $this->morphMany('App\AllergyInfo', 'connectable', 'connection_type', 'connection_id');
    }
}
