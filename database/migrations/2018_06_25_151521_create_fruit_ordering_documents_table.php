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
            $table->string('company_supplier')->nullable();
            $table->string('pointless')->nullable();
            $table->longText('delivery_notes')->nullable();
            $table->integer('fruit_crates')->nullable();
            $table->integer('fruit_boxes')->nullable();
            $table->integer('deliciously_red_apples')->nullable();
            $table->integer('pink_lady_apples')->nullable();
            $table->integer('red_apples')->nullable();
            $table->integer('green_apples')->nullable();
            $table->integer('satsumas')->nullable();
            $table->integer('pears')->nullable();
            $table->integer('bananas')->nullable();
            $table->integer('nectarines')->nullable();
            $table->integer('limes')->nullable();
            $table->integer('lemons')->nullable();
            $table->integer('grapes')->nullable();
            $table->integer('seasonal_berries')->nullable();
            $table->integer('milk_1l_alt_coconut')->nullable();
            $table->integer('milk_1l_alt_unsweetened_almond')->nullable();
            $table->integer('milk_1l_alt_almond')->nullable();
            $table->integer('milk_1l_alt_unsweetened_soya')->nullable();
            $table->integer('milk_1l_alt_soya')->nullable();
            $table->integer('milk_1l_alt_lactose_free_semi')->nullable();
            $table->integer('filter_coffee_250g')->nullable();
            $table->integer('expresso_coffee_250g')->nullable();
            $table->integer('muesli')->nullable();
            $table->integer('granola')->nullable();
            $table->integer('still_water')->nullable();
            $table->integer('sparkling_water')->nullable();
            $table->integer('milk_2l_semi_skimmed')->nullable();
            $table->integer('milk_2l_skimmed')->nullable();
            $table->integer('milk_2l_whole')->nullable();
            $table->integer('milk_1l_semi_skimmed')->nullable();
            $table->integer('milk_1l_skimmed')->nullable();
            $table->integer('milk_1l_whole')->nullable();
            $table->integer('milk_pint_semi_skimmed')->nullable();
            $table->integer('milk_pint_skimmed')->nullable();
            $table->integer('milk_pint_whole')->nullable();
            $table->integer('milk_1l_organic_semi_skimmed')->nullable();
            $table->integer('milk_1l_organic_skimmed')->nullable();
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
