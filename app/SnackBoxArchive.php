<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackBoxArchive extends Model
{
    protected $fillable = [
        
        // Snackbox Info
        'snackbox_id',
        'is_active',
        'delivered_by',
        'no_of_boxes',
        'snack_cap',
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
        'invoiced_at'
    ];

    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
}
