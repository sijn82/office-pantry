<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public function milk()
    {
      return this->hasOne('App\MilkBox');
    }
}
