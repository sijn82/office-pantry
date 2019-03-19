<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrinkBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drink_boxes', function (Blueprint $table) {
            $table->increments('id');
            // OtherBox Info
            $table->string('drinkbox_id');
            $table->string('is_active');
            $table->integer('delivered_by_id');
            $table->integer('no_of_boxes');
            // $table->'type',
            // Company Info
            $table->integer('company_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('week_in_month');
            $table->date('previous_delivery_week');
            $table->date('next_delivery_week');
            // Product Information
            $table->string('code');
            $table->string('name');
            $table->integer('quantity');
            $table->integer('unit_price');
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
        Schema::dropIfExists('drink_boxes');
    }
}
