<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllergyInfo extends Model
{
    //
    protected $fillable = [
        'allergy_id',
        'connection_id',
        'connection_type'
    ];

    public function connectable()
    {
        return $this->morphTo();
    }

    public function allergy()
    {
        return $this->hasOne(Allergy::class, 'id', 'allergy_id');
    }
}
