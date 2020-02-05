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
    public function up() // THIS MIGRATION IS OUT OF DATE!
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active')->default('Active');
            $table->string('code')->unique();
            $table->string('brand');
            $table->string('flavour');
            $table->decimal('purchase_case_price', 8, 2);
            $table->decimal('retail_case_price', 8, 2)->nullable();
            $table->integer('case_size');
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->string('vat');
            $table->decimal('profit_margin', 8, 2)->nullable(); // While this field is automatically calculated, wholesale case prices may bypass the necessary unit price field.
            $table->integer('stock_level')->nullable();
            $table->date('shortest_stock_date')->nullable();
            $table->string('sales_nominal');
            // $table->string('cost_nominal')->nullable(); // Not used so may as well remove.
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
