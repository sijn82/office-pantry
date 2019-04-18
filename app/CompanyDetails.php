<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
    //
    protected $fillable = [
        
        'is_active',
        // Company Name(s)
        'invoice_name',
        'route_name',
        // Contact Details
        'primary_contact',
        'primary_contact_job_title',
        'primary_email',
        'primary_tel',
        'secondary_contact',
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
        // Billing and Delivery
        'branding_theme',
        'surcharge',
        'supplier_id',
        'model',
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];

    // These declare the relationships between models (tables within the database).
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function company_routes()
    {
        return $this->hasMany(CompanyRoute::class);
    }
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
    
    public function fruitbox_archive()
    {
        return $this->hasMany(FruitBoxArchive::class);
    }
    public function milkbox_archive()
    {
        return $this->hasMany(MilkBoxArchive::class);
    }
    
    // This is a test to see if I can pull a list of relationships into 1 statement request
    public function invoicing_data()
    {
        return $this->morphTo();
    }
}
