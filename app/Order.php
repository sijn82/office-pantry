<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    
    // 
    // //
    // public function company()
    // {
    //     return this->hasOne('App\Company');
    // }
    // 
    public function fruitbox()
    {
        $active_orders = App\FruitBox::find(1);
    }
}
