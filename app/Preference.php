<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $table = 'company_preferences';
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    
        'company_details_id',
        'snackbox_likes',
        'snackbox_dislikes',
        'snackbox_essentials',
        'snackbox_essentials_quantity',
        // 'allergies',
        // 'additional_notes',
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
