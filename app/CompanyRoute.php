<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyRoute extends Model
{
    //
    protected $fillable = [
        'is_active',
        'company_details_id',
        'route_name',
        'postcode',
        'address',
        'delivery_information',
        'fruit_crates',
        'fruit_boxes',
        'drinks',
        'snacks',
        'other',
        'assigned_route_id',
        'position_on_route',
        'delivery_day',
      ];

          /**
           * The attributes that should be hidden for arrays.
           *
           * @var array
           */
          protected $hidden = [
              // 'password', 'remember_token',
          ];
          
          // public function companies()
          // {
          //     return $this->belongsTo(Company::class);
          // }
          public function companies()
          {
              return $this->belongsTo(CompanyDetails::class);
          }
          public function assigned_route()
          {
              return $this->belongsTo(AssignedRoute::class);
          }
}
