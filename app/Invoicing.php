<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoicing extends Model
{
    protected $table = 'invoices';
    //
    protected $fillable =[
    
        'contact_name', // Company Invoice Name
        'email_address', // Company Invoice Email
        'po_address_line_1', // Invoice Address Line 1
        'po_address_line_2', //  Invoice Address Line 2
        'po_city', // Invoice Address City
        'po_region', // Invoice Address Region
        'po_post_code', // Invoice Address PostCode
        'invoice_number', // YYMMDD-000
        'invoice_date', // Week Start
        'due_date', // Due Date (Processing date, usually the day of processing but I want to add an option to set a day, or two delay).
        'description', // Main Box Groups (1 x Fruitbox Price, 2 x Fruitbox Price, 1l Milk, Snacks) & (Otherbox, Product Names)
        'quantity', // Quantity of particular box group or particular product.
        'account_code', // Sales Nominal i.e 4010, 4020, 4040, 4050, 4090, 4100.
        'unit_amount', // Cost (Price Sold For)
        'tax_amount', // How much of that is Taxed
        'tax_type', // Whether item was Zero Rated Income Or 20% (VAT on Income)
        'branding_theme', // Company Branding Theme (Payment Method)
    ]
}
