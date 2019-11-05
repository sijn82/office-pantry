<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilkBox extends Model
{
    protected $casts = [
        'order_changes' => 'object',
    ];

    //
    protected $fillable = [
        // Company order details
        'is_active',
        'fruit_partner_id',
        'company_details_id',
        'previous_delivery',
        'next_delivery',
        'frequency',
        'week_in_month',
        'delivery_day',
        // Regular 2l
        'semi_skimmed_2l',
        'skimmed_2l',
        'whole_2l',
        // Regular 1l
        'semi_skimmed_1l',
        'skimmed_1l',
        'whole_1l',
        // Organic 1l
        'organic_semi_skimmed_1l',
        'organic_skimmed_1l',
        'organic_whole_1l',
        // Organic 2l
        'organic_semi_skimmed_2l',
        'organic_skimmed_2l',
        'organic_whole_2l',
        // Alternative 1l options
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
        // Invoice details, currently keeping timestamps out of the editable range.
        'invoiced_at',
        // Newly added fields to track changes
        'order_changes',
        'date_changed'

    ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'password', 'remember_token',
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
