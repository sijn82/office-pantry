<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalFruitBoxItem extends Model
{
    // While this doesn't need to be a polymorphic relationship, 
    // it's going to be the pivot between AdditionalFruits and the FruitBox they've been added too.

    // id, fruitbox_id, additional_fruit_id, quantity.

    // EDIT: 1/7/2020 (Evaluating where the system got to before lockdown)
    // Is the fruitbox id week specific, i.e is it refreshed or reused each week?  If reused we need to add the delivery week, otherwise we're all good to go (build it).
}
