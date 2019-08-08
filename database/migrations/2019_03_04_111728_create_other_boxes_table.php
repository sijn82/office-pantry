<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_boxes', function (Blueprint $table) {
            $table->increments('id');
            // OtherBox Info
            $table->string('otherbox_id');
            $table->string('is_active')->default('Active');
            $table->integer('delivered_by_id');
            $table->string('type');
            // $table->integer('no_of_boxes')->nullable(); //  Do we need no_of_boxes here?  I don't think we want them for drinkboxes and these boxes similar.
            // Company Info
            $table->integer('company_details_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->date('previous_delivery_week')->nullable();
            $table->date('next_delivery_week');
            // Product Information
            $table->integer('product_id')->default(0);
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->decimal('case_price')->nullable();
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
        Schema::dropIfExists('other_boxes');
    }
}
