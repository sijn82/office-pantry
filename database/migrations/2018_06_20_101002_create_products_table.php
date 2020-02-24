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
    public function up() // This migration has been updated!
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active')->default('Active');
            $table->string('code')->unique();
            $table->string('brand');
            $table->string('flavour');
            $table->decimal('buying_case_cost', 8, 2);
            $table->decimal('buying_case_size', 8, 2);
            $table->decimal('buying_unit_cost', 8, 2)->nullable(); // even though it should always have a value, allowing nullable, or should i?
            $table->decimal('selling_case_price', 8, 2);
            $table->decimal('selling_case_size', 8, 2);
            $table->decimal('selling_unit_price', 8, 2)->nullable();
            $table->string('vat');
            $table->decimal('profit_margin', 8, 2)->nullable(); // While this field is automatically calculated, wholesale case prices may bypass the necessary unit price field.
            $table->integer('stock_level')->nullable();
            $table->date('shortest_stock_date')->nullable();
            $table->string('sales_nominal');
            // Temporary fields needed to populate allergies and dietary requirements tables, then can be deleted.
            $table->string('allergen_info')->nullable();
            $table->string('dietary_requirements')->nullable();
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
