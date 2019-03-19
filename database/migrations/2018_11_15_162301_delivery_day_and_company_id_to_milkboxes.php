<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeliveryDayAndCompanyIdToMilkboxes extends Migration
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
            $table->integer('company_id')->after('id')->nullable();
            $table->string('delivery_day')->after('company_id')->nullable();
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
