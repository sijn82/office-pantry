<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active');
            $table->integer('company_id');
            $table->string('route_name');
            $table->string('postcode');
            $table->longtext('address');
            $table->longtext('delivery_information');
            $table->integer('fruit_crates');
            $table->integer('fruit_boxes');
            $table->integer('drinks');
            $table->integer('snacks');
            $table->longtext('other');
            $table->integer('assigned_route_id');
            $table->integer('position_on_route');
            $table->string('delivery_day');
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
        Schema::dropIfExists('company_routes');
    }
}
