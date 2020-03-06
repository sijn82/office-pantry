<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    protected $fillable = [

        'product_id',
        'quantity',
        'box_id',
        'box_type',
        'delivery_week'
    ];

    public function orderable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
