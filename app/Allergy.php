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
        'snackbox_id',
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
        // What I want to represent - Allergy belongs to many Snackboxes but only where the (Allergy) snackbox_id matches (snackbox) snackbox_id, or (if Allergy->snackbox_id == null) companydetails_id.

        return $this->belongsToMany(SnackBox::class, 'snackbox_id', 'snackbox_id');
    }

    public function allergy()
    {
        return $this->belongsToMany(AllergyInfo::class);
    }
}
