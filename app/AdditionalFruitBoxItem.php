<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalFruitBoxItem extends Model
{
    // While this doesn't need to be a polymorphic relationship, 
    // it's going to be the pivot between AdditionalFruits and the FruitBox they've been added too.

    // id, fruitbox_id, additional_fruit_id, quantity.
}
