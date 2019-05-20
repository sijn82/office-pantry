<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilkBoxArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milk_box_archives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active');
            $table->integer('fruit_partner_id');
            $table->integer('company_details_id');
            $table->date('previous_delivery');
            $table->date('next_delivery');
            $table->string('frequency');
            $table->string('week_in_month');
            $table->string('delivery_day');
            // Regular 2l Milk
            $table->integer('semi_skimmed_2l');
            $table->integer('skimmed_2l');
            $table->integer('whole_2l');
            // Regular 1l Milk
            $table->integer('semi_skimmed_1l');
            $table->integer('skimmed_1l');
            $table->integer('whole_1l');
            // Organic 1l Milk
            $table->integer('organic_semi_skimmed_1l');
            $table->integer('organic_skimmed_1l');
            $table->integer('organic_whole_1l');
            // Organic 2l Milk
            $table->integer('organic_semi_skimmed_2l');
            $table->integer('organic_skimmed_2l');
            $table->integer('organic_whole_2l');
            // Alternative Milk Options
            $table->integer('milk_1l_alt_coconut');
            $table->integer('milk_1l_alt_unsweetened_almond');
            $table->integer('milk_1l_alt_almond');
            // Alt pt2
            $table->integer('milk_1l_alt_unsweetened_soya');
            $table->integer('milk_1l_alt_soya');
            $table->integer('milk_1l_alt_oat');
            // Alt pt3
            $table->integer('milk_1l_alt_rice');
            $table->integer('milk_1l_alt_cashew');
            $table->integer('milk_1l_alt_lactose_free_semi');
            // Invoice & Time Stamp Information
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
        Schema::dropIfExists('milk_box_archives');
    }
}
