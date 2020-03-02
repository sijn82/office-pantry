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
        return $this->hasMany(Preference::class);
    }
    public function allergy()
    {
        return $this->hasMany(Allergy::class);
    }
    public function additional_info()
    {
        return $this->hasMany(AdditionalInfo::class);
    }
    // Archived Orders <-- I either don't need these or I could reference FruitBox::class with constraints (if that's a thing?).
    public function fruitbox_archive()
    {
        // i.e return $this->hasMany(FruitBox::class)->where('next_delivery', '<', $this->week_start)->where('invoiced_at', 'null')->get();
        return $this->hasMany(FruitBoxArchive::class);
    }
    public function milkbox_archive()
    {
        return $this->hasMany(MilkBoxArchive::class);
    }
    public function snackbox_archive()
    {
        return $this->hasMany(SnackBoxArchive::class);
    }
    public function drinkbox_archive()
    {
        return $this->hasMany(DrinkBoxArchive::class);
    }
    public function otherbox_archive()
    {
        return $this->hasMany(OtherBoxArchive::class);
    }

    // This will replace the allergies part of the allergies_and_dietary_requirements() function.
    public function allergy_info()
    {   // In this instance connection_type/connection_id are the expected column names so don't really need declaring.
        return $this->morphMany('App\AllergyInfo', 'connectable', 'connection_type', 'connection_id');
    }
}
