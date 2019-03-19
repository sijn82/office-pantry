<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->integer('company_id');
            $table->integer('fruitbox_id')->nullable();
            $table->integer('milkbox_id')->nullable();
            $table->integer('snackbox_id')->nullable();
            $table->integer('drinkbox_id')->nullable();
            $table->integer('special_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
