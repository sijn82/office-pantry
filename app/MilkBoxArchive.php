<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilkBoxArchive extends Model
{
    protected $primaryKey = 'milkbox_id';
    public $incrementing = false;
    
    protected $fillable = [
        'is_active',
        'fruit_partner_id',
        'company_details_id',
        'previous_delivery',
        'next_delivery',
        'frequency',
        'week_in_month',
        'delivery_day',
        // Regular 2l milk
        'semi_skimmed_2l',
        'skimmed_2l',
        'whole_2l',
        // Regular 1l milk
        'semi_skimmed_1l',
        'skimmed_1l',
        'whole_1l',
        // Organic 1l milk
        'organic_semi_skimmed_1l',
        'organic_skimmed_1l',
        'organic_whole_1l',
        // Organic 2l milk
        'organic_semi_skimmed_2l',
        'organic_skimmed_2l',
        'organic_whole_2l',
        // Alternative Milk Options
        'milk_1l_alt_coconut',
        'milk_1l_alt_unsweetened_almond',
        'milk_1l_alt_almond',
        // Alt pt2
        'milk_1l_alt_unsweetened_soya',
        'milk_1l_alt_soya',
        'milk_1l_alt_oat',
        // Alt pt3
        'milk_1l_alt_rice',
        'milk_1l_alt_cashew',
        'milk_1l_alt_lactose_free_semi',
        // Invoice date, leaving timestamps out of editable range.
        'invoiced_at',
    ];
    
    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
}
