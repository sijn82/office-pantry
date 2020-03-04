<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitPartner extends Model
{
    //
    protected $fillable = [

        'name',
        'email',
        'telephone',
        'url',
        'primary_contact_first_name',
        'primary_contact_surname',
        'secondary_contact_first_name',
        'secondary_contact_surname',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'region',
        'postcode',
        'alternative_telephone',
        'weekly_action',
        'changes_action',
        'status',
        'no_of_customers',
        'use_op_boxes',
        'finance',
        'additional_info',

    ];

    public function company()
    {
        return $this->hasMany(CompanyDetails::class, 'supplier_id');
    }

    public function fruitbox()
    {
        return $this->hasMany(FruitBox::class);
    }

    public function milkbox()
    {
        return $this->hasMany(MilkBox::class);
    }
}
