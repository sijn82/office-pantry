<?php

namespace App;

use App\WeekStart;

use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
    protected $casts = [
        'order_changes' => 'object',
    ];

    //
    protected $fillable = [

        'is_active',
        // Company Name(s)
        'invoice_name',
        'route_name',
        // Contact Details
        'primary_contact_first_name',
        'primary_contact_surname',
        'primary_contact_job_title',
        'primary_email',
        'primary_tel',
        'secondary_contact_first_name',
        'secondary_contact_surname',
        'secondary_contact_job_title',
        'secondary_email',
        'secondary_tel',
        'delivery_information',
        // Route Address
        'route_address_line_1',
        'route_address_line_2',
        'route_address_line_3',
        'route_city',
        'route_region',
        'route_postcode',
        // Invoice Address
        'invoice_address_line_1',
        'invoice_address_line_2',
        'invoice_address_line_3',
        'invoice_city',
        'invoice_region',
        'invoice_postcode',
        'invoice_email',
        // Billing and Delivery
        'branding_theme',
        'surcharge',
        'supplier_id',
        'model',
        'monthly_surprise',
        'no_of_surprises',
        // Newly added fields to track changes
        'order_changes',
        'date_changed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];

    public function __construct()
    {
        $week_start = WeekStart::first();

        if ($week_start !== null) {
            $this->week_start = $week_start->current;
            $this->delivery_days = $week_start->delivery_days;
        }

    }

    // These declare the relationships between models (tables within the database).

    // Company Details

    // This is just the default fruitpartner used to pre-populate forms with.
    // Can be overridden by the relationship between each box and fruitpartner.
    
    public function fruit_partner()
    {
        return $this->belongsTo(FruitPartner::class, 'supplier_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function company_routes()
    {
        return $this->hasMany(CompanyRoute::class);
    }

    // Current Orders

    public function fruitbox()
    {
        return $this->hasMany(FruitBox::class);
    }
    public function milkbox()
    {
        return $this->hasMany(MilkBox::class);
    }
    public function snackboxes()
    {
        return $this->hasMany(SnackBox::class);
    }
    public function drinkboxes()
    {
        return $this->hasMany(DrinkBox::class);
    }
    public function otherboxes()
    {
        return $this->hasMany(OtherBox::class);
    }

    // Preferences

    public function preference()
    {
        return $this->morphMany('App\Preference', 'preferable', 'connection_type', 'connection_id');
    }

    public function allergy_info()
    {   // In this instance connection_type/connection_id are the expected column names so don't really need declaring.
        return $this->morphMany('App\AllergyInfo', 'connectable', 'connection_type', 'connection_id');
    }

    public function additional_info()
    {
        return $this->hasMany(AdditionalInfo::class);
    }

}
