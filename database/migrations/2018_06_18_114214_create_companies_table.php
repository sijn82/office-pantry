<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('company_id');
            $table->string('is_active');
            $table->string('invoice_name')->nullable();
            $table->string('route_name')->nullable();
            $table->string('box_names')->nullable();
            $table->string('primary_contact')->nullable();
            $table->string('primary_email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('delivery_information')->nullable();
            $table->string('route_summary_address')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postcode')->nullable();
            $table->string('branding_theme')->nullable();
            $table->string('supplier')->nullable();
            $table->string('delivery_monday')->nullable();
            $table->string('delivery_tuesday')->nullable();
            $table->string('delivery_wednesday')->nullable();
            $table->string('delivery_thursday')->nullable();
            $table->string('delivery_friday')->nullable();
            $table->string('assigned_to_monday')->nullable();
            $table->string('assigned_to_tuesday')->nullable();
            $table->string('assigned_to_wednesday')->nullable();
            $table->string('assigned_to_thursday')->nullable();
            $table->string('assigned_to_friday')->nullable();
            $table->timestamps();
            // $table->integer('order_id'); // I'm not sure it makes much sense to have this in here?
                                            // Better to include the company_id in orders table.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
