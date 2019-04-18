<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilkBoxArchive extends Model
{
    //
    protected $fillable = [
        'is_active',
        'fruit_partner_id',
        'company_details_id',
        'previous_delivery',
        'next_delivery',
        'frequency',
        'week_in_month',
        'delivery_day',
        'milk_1l_alt_coconut',
        'milk_1l_alt_unsweetened_almond',
        'milk_1l_alt_almond',
        'milk_1l_alt_unsweetened_soya',
        'milk_1l_alt_soya',
        'milk_1l_alt_lactose_free_semi',
        'semi_skimmed_2l',
        'skimmed_2l',
        'whole_2l',
        'semi_skimmed_1l',
        'skimmed_1l',
        'whole_1l',
        'organic_semi_skimmed_1l',
        'organic_skimmed_1l',
        'organic_whole_1l',
        'organic_semi_skimmed_2l',
        'organic_skimmed_2l',
        'organic_whole_2l',
        'invoiced_at',
    ];
    
    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
}
