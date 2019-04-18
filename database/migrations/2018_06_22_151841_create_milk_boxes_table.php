<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilkBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milk_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active');
            $table->integer('fruit_partner_id');
            $table->integer('company_details_id');
            $table->date('previous_delivery');
            $table->date('next_delivery');
            $table->string('frequency');
            $table->string('week_in_month');
            $table->string('delivery_day');
            $table->integer('1l_milk_alt_coconut');
            $table->integer('1l_milk_alt_unsweetened_almond');
            $table->integer('1l_milk_alt_almond');
            $table->integer('1l_milk_alt_unsweetened_soya');
            $table->integer('1l_milk_alt_soya');
            $table->integer('milk_1l_alt_oat');
            $table->integer('milk_1l_alt_rice');
            $table->integer('milk_1l_alt_cashew');
            $table->integer('1l_milk_alt_lactose_free_semi');
            $table->integer('2l_semi_skimmed');
            $table->integer('2l_skimmed');
            $table->integer('2l_whole');
            $table->integer('1l_semi_skimmed');
            $table->integer('1l_skimmed');
            $table->integer('1l_whole');
            $table->integer('pint_semi_skimmed');
            $table->integer('pint_whole');
            $table->integer('1l_organic_semi_skimmed');
            $table->integer('1l_organic_skimmed');
            $table->date('invoiced_at');
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
        Schema::dropIfExists('milk_boxes');
    }
}
