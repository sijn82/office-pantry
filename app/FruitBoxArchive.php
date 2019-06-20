<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitBoxArchive extends Model
{
    // This table is going to be used to store old fruitboxes once they've been updated.
    // An update is any change other than the next_delivery date.
    // If they have an invoice address == to the update address, they've been invoice and can be set to 'inactive' status.
    // If the update address is newer than the invoice address, we need to keep them 'active' and ready to pull them into the next invoice.
    
    // I wish I'd written a quick thing explaining why I went for this.  I'm sure I did it for a reason, most likely connected to being able to retrieve and keep track of its original id in fruitbox.
    protected $primaryKey = 'fruitbox_id';
    
    protected $fillable = [
        
        'fruitbox_id',
        'is_active',
        'fruit_partner_id',
        'name',
        'company_details_id',
        //'route_id',
        'type',
        'previous_delivery',
        'next_delivery',
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
    ];
    
    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
    
    public function fruit_partners()
    {
        return $this->belongsTo(FruitPartner::class);
    }
}
