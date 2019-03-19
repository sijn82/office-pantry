<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedRoute extends Model
{
    protected $fillable = [
        'name',
        'delivery_day',
        'tab_order'
    ];
    // old connection about to be replaced by one below
    public function routes()
    {
        return $this->hasMany(Route::class);
    }
    // new connection for the assigned route and company delivery details.
    public function company_routes()
    {
        return $this->hasMany(CompanyRoute::class);
    }

}
