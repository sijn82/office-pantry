<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRouteIdToMilkBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('milk_boxes', function (Blueprint $table) {
            //
            $table->string('is_active')->after('id')->nullable();
            $table->integer('route_id')->after('company_details_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('milk_boxes', function (Blueprint $table) {
            //
        });
    }
}
