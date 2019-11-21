<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $casts = [
        'allergy' => 'array',
        'dietary_requirements' => 'array'
    ];

    protected $fillable = [
        'company_details_id',
        'allergy',
        'dietary_requirements'
    ];

    // I'm thinking of switching this out and making the connection between allergy/dietary requirements
    // and the snackbox they're associated with.
    // public function companies()
    // {
    //     return $this->belongsTo(CompanyDetails::class);
    // }

    public function snackbox()
    {
        return $this->belongsTo(SnackBox::class);
    }
}
