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
        'delivered_by',
        'no_of_boxes',
        'type',
        // Company Info
        'company_details_id',
        'delivery_day',
        'frequency',
        'previous_delivery_week',
        'next_delivery_week',
        // Product Information
        'code',
        'name',
        'quantity'
    ];
    
    // public function companies()
    // {
    //     return $this->belongsTo(Company::class);
    // }
    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
    
}
