<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackBoxArchive extends Model
{
    // OK, so it looks as though If I specify a new primary key, I also need to destroy etc via that key.
    // This doesn't actually work for my use, so I need to scrap it.  If only I knew what problem I'd solved earlier specifying the snackbox_id as the primary key?!
    
    // protected $primaryKey = 'snackbox_id';
    // public $incrementing = false;
    
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
        'case_price',
        'invoiced_at'
    ];

    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
}
