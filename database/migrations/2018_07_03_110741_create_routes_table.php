<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id'); // This may not work as I'd like because in an ideal world I'd have an id of 1 for each route/day.
            $table->date('week_start');
            $table->string('company_name');
            $table->string('postcode')->nullable();
            $table->longText('address')->nullable();
            $table->longText('delivery_information')->nullable();
            $table->integer('fruit_crates')->nullable();
            $table->integer('fruit_boxes')->nullable();
            $table->integer('milk_2l_semi_skimmed')->nullable();
            $table->integer('milk_2l_skimmed')->nullable();
            $table->integer('milk_2l_whole')->nullable();
            $table->integer('milk_1l_semi_skimmed')->nullable();
            $table->integer('milk_1l_skimmed')->nullable();
            $table->integer('milk_1l_whole')->nullable();
            $table->integer('milk_1l_alt_coconut')->nullable();
            $table->integer('milk_1l_alt_unsweetened_almond')->nullable();
            $table->integer('milk_1l_alt_almond')->nullable();
            $table->integer('milk_1l_alt_unsweetened_soya')->nullable();
            $table->integer('milk_1l_alt_soya')->nullable();
            $table->integer('milk_1l_alt_oat')->nullable();
            $table->integer('milk_1l_alt_rice')->nullable();
            $table->integer('milk_1l_alt_cashew')->nullable();
            $table->integer('milk_1l_alt_lactose_free_semi')->nullable();
            $table->string('drinks')->nullable();
            $table->string('snacks')->nullable();
            $table->string('other')->nullable();
            $table->string('assigned_to')->default('TBC');
            $table->string('delivery_day'); // The mispositioning of this field upsets me but for now it's much quicker to keep it and not corrupt the current data for live upload.
            $table->integer('position_on_route')->nullable();
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
        Schema::dropIfExists('routes');
    }
}
