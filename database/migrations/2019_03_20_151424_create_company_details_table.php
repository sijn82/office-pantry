<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() // Migration Updated!
    {
        Schema::create('company_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active')->default('Active');
            // Company Name(s)
            $table->string('invoice_name');
            $table->string('route_name');
            // Contact Details
            $table->string('primary_contact_first_name')->nullable();
            $table->string('primary_contact_surname')->nullable();
            $table->string('primary_contact_job_title')->nullable();
            $table->string('primary_email')->nullable();
            $table->string('primary_tel')->nullable();
            $table->string('secondary_contact_first_name')->nullable();
            $table->string('secondary_contact_surname')->nullable();
            $table->string('secondary_contact_job_title')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('secondary_tel')->nullable();
            $table->longtext('delivery_information')->nullable(); // This may not have worked, character limit was still 255.
            // Route Address
            $table->string('route_address_line_1');
            $table->string('route_address_line_2')->nullable();
            $table->string('route_address_line_3')->nullable(); 
            $table->string('route_city');
            $table->string('route_region')->nullable();
            $table->string('route_postcode');
            // Invoice Address
            $table->string('invoice_address_line_1')->nullable();
            $table->string('invoice_address_line_2')->nullable();
            $table->string('invoice_address_line_3')->nullable(); 
            $table->string('invoice_city')->nullable();
            $table->string('invoice_region')->nullable();
            $table->string('invoice_postcode')->nullable();
            $table->string('invoice_email');
            // Billing and Delivery
            $table->string('branding_theme');
            $table->integer('surcharge')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('model')->nullable();
            $table->string('monthly_special')->nullable()->default('TBC'); // Not sure chaining these worked - null broke not null on import.
            $table->string('no_of_surprises')->nullable()->default(0);
            // Track change to Company Details
            $table->longtext('order_changes')->nullable();
            $table->date('date_changed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('company_details_user');
        Schema::dropIfExists('company_details');
    }
}
