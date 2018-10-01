<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFruitOrderingDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fruit_ordering_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->date('week_start');
            $table->string('company_name');
            $table->string('company_supplier');
            $table->string('pointless')->nullable();
            $table->longText('delivery_notes')->nullable();
            $table->integer('fruit_crates')->default(0);
            $table->integer('fruit_boxes')->default(0);
            $table->integer('deliciously_red_apples')->default(0);
            $table->integer('pink_lady_apples')->default(0);
            $table->integer('red_apples')->default(0);
            $table->integer('green_apples')->default(0);
            $table->integer('satsumas')->default(0);
            $table->integer('pears')->default(0);
            $table->integer('bananas')->default(0);
            $table->integer('nectarines')->default(0);
            $table->integer('limes')->default(0);
            $table->integer('lemons')->default(0);
            $table->integer('grapes')->default(0);
            $table->integer('seasonal_berries')->default(0);
            $table->integer('milk_1l_alt_coconut')->default(0);
            $table->integer('milk_1l_alt_unsweetened_almond')->default(0);
            $table->integer('milk_1l_alt_almond')->default(0);
            $table->integer('milk_1l_alt_unsweetened_soya')->default(0);
            $table->integer('milk_1l_alt_soya')->default(0);
            $table->integer('milk_1l_alt_lactose_free_semi')->default(0);
            $table->integer('filter_coffee_250g')->default(0);
            $table->integer('expresso_coffee_250g')->default(0);
            $table->integer('muesli')->default(0);
            $table->integer('granola')->default(0);
            $table->integer('still_water')->default(0);
            $table->integer('sparkling_water')->default(0);
            $table->integer('milk_2l_semi_skimmed')->default(0);
            $table->integer('milk_2l_skimmed')->default(0);
            $table->integer('milk_2l_whole')->default(0);
            $table->integer('milk_1l_semi_skimmed')->default(0);
            $table->integer('milk_1l_skimmed')->default(0);
            $table->integer('milk_1l_whole')->default(0);
            $table->integer('milk_pint_semi_skimmed')->default(0);
            $table->integer('milk_pint_skimmed')->default(0);
            $table->integer('milk_pint_whole')->default(0);
            $table->integer('milk_1l_organic_semi_skimmed')->default(0);
            $table->integer('milk_1l_organic_skimmed')->default(0);
            // $table->integer('snack_boxes')->default(0);
            $table->string('delivery_day');
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
        Schema::dropIfExists('fruit_ordering_documents');
    }
}
