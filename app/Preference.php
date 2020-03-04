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
    
        'connection_type',
        'connection_id',
        'product_id',

    ];
    
    // public function companies()
    // {
    //     return $this->belongsTo(CompanyDetails::class);
    // }

    public function preferable()
    {
        return $this->morphTo();
    }
    
}
