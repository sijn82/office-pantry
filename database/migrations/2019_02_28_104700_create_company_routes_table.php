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
            $table->string('is_active')->default('Active');
            $table->integer('company_details_id');
            $table->string('route_name');
            $table->string('postcode');
            $table->longtext('address');
            $table->longtext('delivery_information')->nullable();
            $table->integer('fruit_crates')->default(0);
            $table->integer('fruit_boxes')->nullable(); // hmmn, ->default(0) or ->nullable() ???
            $table->integer('drinks')->nullable(); // hmmn, ->default(0) or ->nullable() ???
            $table->integer('snacks')->nullable(); // hmmn, ->default(0) or ->nullable() ???
            $table->longtext('other')->nullable();
            $table->integer('assigned_route_id');
            $table->integer('position_on_route')->nullable();
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
