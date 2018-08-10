<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //

    use Notifiable;

    protected $casts = [
      'box_names' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_active',
        'invoice_name',
        'route_name',
        'box_names',
        'primary_contact',
        'primary_email',
        'secondary_email',
        'delivery_information',
        'route_summary_address',
        'address_line_1',
        'address_line_2',
        'city',
        'region',
        'postcode',
        'branding_theme',
        'supplier',
        'delivery_monday',
        'delivery_tuesday',
        'delivery_wednesday',
        'delivery_thursday',
        'delivery_friday',
        'assigned_to_monday',
        'assigned_to_tuesday',
        'assigned_to_wednesday',
        'assigned_to_thursday',
        'assigned_to_friday',
        
        // 'order_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];
}
