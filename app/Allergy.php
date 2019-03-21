<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    
    protected $fillable = [
        'company_details_id',
        'allergy'
    ];
    
    // public function companies()
    // {
    //     return $this->belongsTo(Company::class);
    // }
    public function companies()
    {
        return $this->belongsTo(CompanyDetails::class);
    }
}
