<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active');
            $table->string('code');
            $table->string('name');
            $table->string('case_price');
            $table->string('case_size');
            $table->string('unit_cost');
            $table->string('unit_price');
            $table->string('vat');
            $table->string('sales_nominal');
            $table->string('cost_nominal');
            $table->integer('stock_level');
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
        Schema::dropIfExists('products');
    }
}
