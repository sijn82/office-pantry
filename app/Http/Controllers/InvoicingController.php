<?php

namespace App\Http\Controllers;

// Processing Week
use App\WeekStart;

// Company Details
use App\CompanyDetails;
use App\CompanyRoute;

// Orders
use App\FruitBox;
use App\MilkBox;
use App\SnackBox;
use App\DrinkBox;
use App\OtherBox;

// Delivery Info
use App\AssignedRoute;
use App\FruitPartner;

// Date/Time Library
use Carbon\Carbon;

class InvoicingController extends Controller
{
    public function __construct()
    {
        $week_start = WeekStart::findOrFail(1);
        $this->week_start = $week_start->current;
    }
    
    public function weekly_invoicing()
    {
        // We want to run through each company, looking for orders processed this week.
        
        // First check we could make is whether the company is still registered as active.
        // This might help to speed up the processing, should the following steps prove a laboured process.
        // Though otherwise we want to check every company on record for orders.
        
        // The loadMorph was an attempt to call all relational data at once but I may want to handle it seperately anyway. ~ WIP ~
        
        // $companies = CompanyDetails::where('is_active', 'Active')->with('invoicing_data')
        //                             ->get()
        //                             ->loadMorph('invoicing_data', [
        //                                 FruitBox::class => ['company_details'],
        //                                 MilkBox::class => ['company_details'],
        //                                 SnackBox::class => ['company_details'],
        //                                 DrinkBox::class => ['company_details'],
        //                                 OtherBox::class => ['company_details'],
        //                                 CompanyRoute::class => ['company_details'],
        //                             ]);
        
        
        // Actually this is more like what I want, if I want to pull all connected orders together first.
        // It first checks that the company is active(ly receiving orders), then that the associated orders are also active and due to (or have) receive(d) an order this processing week.
        
        $companies = CompanyDetails::where('is_active', 'Active')->with([
            'fruitbox' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery', $this->week_start);
            },
            'milkbox' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week_start', $this->week_start);
            },
            'snackboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            }, 
            'drinkboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            }, 
            'otherboxes' => function ($query) {
                $query->where('is_active', 'Active')->where('next_delivery_week', $this->week_start);
            }])->get();
        
        // Now each $company should just have the orders which need invoicing attached.
        foreach ($companies as $company) {
            
            // Each box type needs to be processed slightly differently, so we need 5 dedicated foreach loops.
            
            //----- Set some variables -----//
            
            $monday_total_boxes = (object) [ 
                'standard' => 0, 
                'berry' => 0, 
                'seasonal' => 0, 
                'tailored' => 0 
            ];
            
            $tuesday_total_boxes = (object) [ 
                'standard' => 0, 
                'berry' => 0, 
                'seasonal' => 0, 
                'tailored' => 0 
            ];
            
            $wednesday_total_boxes = (object) [ 
                'standard' => 0, 
                'berry' => 0, 
                'seasonal' => 0, 
                'tailored' => 0 
            ];
            
            $thursday_total_boxes = (object) [ 
                'standard' => 0, 
                'berry' => 0, 
                'seasonal' => 0, 
                'tailored' => 0 
            ];
            
            $friday_total_boxes = (object) [ 
                'standard' => 0, 
                'berry' => 0, 
                'seasonal' => 0, 
                'tailored' => 0 
            ];
            
            //----- End of - Set some variables -----//
            
            //----- Fruitbox -----//
            foreach ($company->fruitbox as $fruitbox) {
                
                // If this works as intended, each $fruitbox total should build an accurate total for box types which can then be tallied up for potential discounts
                // The only remaining question is how to handle tailored and additional berry/grape punnets.
                
                switch ($fruitbox->delivery_day) {
                    case 'Monday':
                        // Fingers crossed this will allow the order to be tallied up to the right types <-- currently untested, while I flesh out the others.
                        $monday_total_boxes->$fruitbox->type += $fruitbox->fruitbox_total;
                        break;
                    case 'Tuesday':
                        $tuesday_total_boxes->$fruitbox->type += $fruitbox->fruitbox_total;
                        break;
                    case 'Wednesday':
                        $wednesday_total_boxes->$fruitbox->type += $fruitbox->fruitbox_total;
                        break;
                    case 'Thursday':
                        $thursday_total_boxes->$fruitbox->type += $fruitbox->fruitbox_total;
                        break;
                    case 'Friday':
                        $friday_total_boxes->$fruitbox->type += $fruitbox->fruitbox_total;
                        break;
                }
                
            } // end of foreach ($company->fruitbox as $fruitbox)
            
            //----- Milkbox -----//
            foreach ($company->milkbox as $milkbox) {
                
                
                
            } // end of foreach ($company->milkbox as $milkbox)
            
            //----- Snackbox -----//
            foreach ($company->snackboxes as $snackbox) {
                
            } // end of foreach ($company->snackboxes as $snackbox)
            
            //----- Something to consider when working out discounts -----//
            
                // When working out drink discounts, I must remember to add the snackbox total to the drinkboxes as they are combined in working out the discount threshold.
                // Whether I need to do the same with the snackboxes as well, is something to be looked at too!
                
            //----- End of something to consider when working out discounts -----//
            
            //----- Drinkbox -----//
            foreach ($company->drinkboxes as $drinkbox) {
                
            } // end of foreach ($company->drinkboxes as $drinkbox)
            
            //----- Otherbox -----//
            foreach ($company->otherboxes as $otherbox) {
                
            } // end of foreach ($company->otherbox as $otherbox)
            
        } // end of foreach ($companies as $company)
    } // end of public function weekly_invoicing
} // end of class InvoicingController extends Controller
