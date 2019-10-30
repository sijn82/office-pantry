<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewStructureToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->dropUnique('products_name_unique'); // <-- drop unique index from name as we're about to rename it to brand, which will not be unique.
            $table->renameColumn('name', 'brand'); // Now we can rename it to part 1 of the new name i.e brand.
            $table->string('flavour')->after('brand'); // And add part 2 of the new name, being flavour.
            $table->renameColumn('purchase_case_price', 'buying_case_cost');
            $table->renameColumn('retail_case_price', 'selling_case_price');
            $table->renameColumn('case_size', 'buying_case_size');
            $table->integer('selling_case_size')->after('buying_case_size');
            $table->renameColumn('unit_cost', 'buying_unit_cost');
            $table->renameColumn('unit_price', 'selling_unit_price');
            $table->string('supplier')->after('vat');
        });
    }

    // Existing table structure

    // $table->increments('id');
    // $table->string('is_active')->default('Active');
    // $table->string('code')->unique();
    // $table->string('name')->unique();
    // $table->decimal('purchase_case_price', 8, 2);
    // $table->decimal('retail_case_price', 8, 2)->nullable();
    // $table->integer('case_size');
    // $table->decimal('unit_cost', 8, 2)->nullable();
    // $table->decimal('unit_price', 8, 2)->nullable();
    // $table->string('vat');
    // $table->decimal('profit_margin', 8, 2)->nullable(); // While this field is automatically calculated, wholesale case prices may bypass the necessary unit price field.
    // $table->integer('stock_level')->nullable();
    // $table->date('shortest_stock_date')->nullable();
    // $table->string('sales_nominal');
    // // $table->string('cost_nominal')->nullable(); // Not used so may as well remove.
    // $table->timestamps();

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
