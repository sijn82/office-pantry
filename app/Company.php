<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //

    use Notifiable;

    // protected $primaryKey = 'company_id';

    protected $casts = [
      'box_names' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_active',            // we can turn off individual elements but this wouldn't keep a record as such that they've now left our services.
        'invoice_name',         // No new home for this yet
        'route_name',           // This is pulled into the routes and without it being re-homed new routes wont be named, or probably even saved.
        'box_names',
        'primary_contact',      // Or this
        'primary_email',        // this
        'secondary_email',      // and this.
        'delivery_information', // Same issue as the route name and
        'route_summary_address',// route summary address come to think about it.
        'address_line_1',       // These address fields aren't currently in use by anything in the new system, 
        'address_line_2',       // however seperating the address for targeted recall is useful/important.
        'city',                 // Especially if 
        'region',               // we're looking for 
        'postcode',             // the postcode.
        'branding_theme',       // Not to mention this and any subsequent fields we'll need for invoicing and future developments.
        'supplier',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];

    public function users()
    {
        return hasMany(User::class);
    }
    // Let's be honest, this seemed like a nice idea but what purpose are orders() going to serve?
    public function orders()
    {
        return $this->hasMany('App\Order', 'id');
    }
    public function fruitbox()
    {
        return $this->hasMany(FruitBox::class);
    }
    public function milkbox()
    {
        return $this->hasMany(MilkBox::class);
    }
    // this is the old connection about to be replaced by the one below for the new system.
    public function route()
    {
        return $this->hasMany(Route::class);
    }
    // new system connection between company details (id) and route details.
    public function company_routes()
    {
        return $this->hasMany(CompanyRoute::class);
    }
    public function snackboxes()
    {
        return $this->hasMany(SnackBox::class);
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
    public function drinkboxes()
    {
        return $this->hasMany(DrinkBox::class);
    }
    public function otherboxes()
    {
        return $this->hasMany(OtherBox::class);
    }
}
