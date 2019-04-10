<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficePantryProducts extends Model
{
    //
    protected $fillable = [
        'name',
        'price',
        'sales_nominal',
        'vat', //  I don't actually have any products to add here that aren't zero rated but just in case we do later on, it's an easy field to include but ignore.
        // Anything else? I can always add some later, so no need to over complicate, or dwell on them now.
    ];
}
