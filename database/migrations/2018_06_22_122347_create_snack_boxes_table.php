<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


// This was only going to be a quick fix option but I found a better way and am now in the process of replacing permanently.
// EDIT: The new snackbox table structure has now been moved into this migration.
class CreateSnackBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snack_boxes', function (Blueprint $table) {
            $table->increments('id');
            // Snackbox Info
            $table->string('is_active')->default('Active'); //  I'm thinking this should be the default, even if the box is created empty.
            $table->string('snackbox_id'); // This isn't an auto increment field, so using a combination of company id and uniqid() to generate a unique id per snackbox.
            $table->string('delivered_by');
            $table->integer('no_of_boxes')->nullable();
            $table->decimal('snack_cap', 4, 2); // I could allow greater numbers but a snack cap for an individual box costing more than £9999.99 seems highly unlikely!
            $table->string('type');
            // Company Info
            $table->integer('company_details_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->string('previous_delivery_week')->nullable();
            $table->string('next_delivery_week');
            // Product Information
            $table->integer('product_id')->default(0);
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->date('invoiced_at')->nullable();
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
        Schema::dropIfExists('snack_boxes');
    }
}
