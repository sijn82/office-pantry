<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalFruit extends Model
{
    // These are the fruits we don't typically include in FruitBoxes but can be included additionally to a box.
    // As I think we order these in for the demand alone, we don't need to keep a stock level for these items but will need
    // a way to show how many have been ordered for the coming week so we can ensure they're bought.

    // id, name, price
}