<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackBox extends Model
{
    // This is the new, fancy and super shiny version.

    protected $fillable = [

        // Snackbox Info
        'snackbox_id',
        'is_active',
        'name',
        'delivered_by',
        'no_of_boxes',
        'snack_cap',
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
        // 'brand',
        // 'flavour',
        // 'quantity',
        // 'selling_unit_price',
        // 'selling_case_price',
        'invoiced_at'
    ];

    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }

    // This is part of my new 2020 approach to snackboxes.
    public function products()
    {
        return $this->hasMany(Product::class,'id', 'product_id');
    }

    // // This relationship is untested but more a theoretical placeholder.
    // // EDIT : Actually I think it's now in use!
    // public function allergies_and_dietary_requirements()
    // {
    //     return $this->hasOne(Allergy::class, 'snackbox_id', 'snackbox_id');

    // }

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
