<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherBoxArchive extends Model
{
    
    protected $primaryKey = 'otherbox_id';
    
    protected $fillable = [
        
        // OtherBox Info
        'otherbox_id',
        'is_active',
        'delivered_by_id',
        'no_of_boxes',
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
}
