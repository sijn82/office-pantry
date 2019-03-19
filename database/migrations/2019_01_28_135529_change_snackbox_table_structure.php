<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSnackboxTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('snack_boxes', function (Blueprint $table) {
            $table->increments('id');
            // Snackbox Info
            $table->string('snackbox_id'); // This isn't an auto increment field, so using a combination of company id and uniqid() to generate a unique id per snackbox.
            $table->string('delivered_by');
            $table->integer('no_of_boxes');
            $table->string('type');
            // Company Info
            $table->integer('company_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('previous_delivery_week');
            $table->string('next_delivery_week');
            // Product Information
            $table->string('code');
            $table->string('name');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('snack_boxes');
    }
}
