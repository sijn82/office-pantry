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
            $table->string('is_active')->default('Active');
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->decimal('case_price', 8, 2);
            $table->integer('case_size');
            $table->decimal('unit_cost', 8, 4);
            $table->decimal('unit_price', 8, 2);
            $table->string('vat');
            $table->string('sales_nominal');
            $table->string('cost_nominal')->nullable();
            $table->decimal('profit_margin', 8, 4);
            $table->integer('stock_level');
            $table->date('shortest_stock_date')->nullable();
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
