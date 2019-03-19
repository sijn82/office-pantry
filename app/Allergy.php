<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    
    protected $fillable = [
        'company_id',
        'allergy'
    ];
    
    public function companies()
    {
        return $this->belongsTo(Company::class);
    }
}
