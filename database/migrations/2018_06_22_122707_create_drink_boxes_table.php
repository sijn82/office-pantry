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
    public function up() // Migration Updated!
    {
        Schema::create('drink_boxes', function (Blueprint $table) {
            $table->increments('id');
            // OtherBox Info
            // $table->string('drinkbox_id');
            $table->string('is_active')->default('Active');
            $table->string('name');
            $table->integer('delivered_by');
            $table->string('type');
            // $table->integer('no_of_boxes'); // Don't think we're going to use this for drinkboxes.
            // Company Info
            $table->integer('company_details_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->date('previous_delivery_week')->nullable();
            $table->date('delivery_week');
            // Product Information
            // $table->integer('product_id')->default(0);
            // $table->string('code')->nullable();
            // $table->string('name')->nullable();
            // $table->integer('quantity')->nullable();
            // $table->decimal('unit_price')->nullable();
            // $table->decimal('case_price')->nullable();
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
        Schema::dropIfExists('drink_boxes');
    }
}
