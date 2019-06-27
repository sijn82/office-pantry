<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FruitPartner extends Model
{
    //
    protected $fillable = [
    
        'id',
        'name',
        'email',
        'telephone',
        'url',
        'primary_contact',
        'secondary_contact',
        'alternative_telephone',
        'location',
        'coordinates', // This is subject to change but until we're using it I'm not going to worry about the best form to store the data in.
        'weekly_action',
        'changes_action',
        'no_of_customers',
        'additional_info',
        
    ];
    
    
    public function fruitbox()
    {
        return $this->hasMany(FruitBox::class);
    }
    
    public function milkbox()
    {
        return $this->hasMany(MilkBox::class);
    }
}
