<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAndAddColumnsToMilkboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('milk_boxes', function (Blueprint $table) {
            // Commented out renaming columns needed for live migration, not local which I manually edited through heidisql (I know, sorry... blah)
            // $table->string('is_active')->first();
            // $table->integer('fruit_partner_id')->after('is_active');
            // $table->integer('company_details_id')->after('fruit_partner_id');
            // $table->date('previous_delivery')->after('previous_delivery');
            // $table->date('next_delivery')->after('next_delivery');
            // $table->string('frequency')->after('frequency');
            // $table->string('week_in_month')->after('week_in_month');
            // $table->string('delivery_day')->after('delivery_day');
            // $table->renameColumn('1l_milk_alt_coconut', 'milk_1l_alt_coconut');
            // $table->renameColumn('1l_milk_alt_unsweetened_almond', 'milk_1l_alt_unsweetened_almond');
            // $table->renameColumn('1l_milk_alt_almond', 'milk_1l_alt_almond');
            // $table->renameColumn('1l_milk_alt_unsweetened_soya', 'milk_1l_alt_unsweetened_soya');
            // $table->renameColumn('1l_milk_alt_soya', 'milk_1l_alt_soya');
            // $table->integer('milk_1l_alt_oat')->after('milk_1l_alt_soya');
            // $table->integer('milk_1l_alt_rice')->after('milk_1l_alt_oat');
            // $table->integer('milk_1l_alt_cashew')->after('milk_1l_alt_cashew');
            // $table->renameColumn('1l_milk_alt_lactose_free_semi', 'milk_1l_alt_lactose_free_semi');
            // $table->renameColumn('2l_semi_skimmed', 'semi_skimmed_2l');
            // $table->renameColumn('2l_skimmed', 'skimmed_2l');
            // $table->renameColumn('2l_whole', 'whole_2l');
            // $table->renameColumn('1l_semi_skimmed', 'semi_skimmed_1l');
            // $table->renameColumn('1l_skimmed', 'skimmed_1l');
            // $table->renameColumn('1l_whole', 'whole_1l');
            // $table->dropColumn('pint_semi_skimmed');
            // $table->dropColumn('pint_whole');
            // $table->renameColumn('1l_organic_semi_skimmed', 'organic_semi_skimmed_1l');
            // $table->renameColumn('1l_organic_skimmed', 'organic_skimmed_1l');
            // $table->integer('organic_whole_1l')->after('organic_skimmed_1l');
            // $table->integer('organic_semi_skimmed_2l')->after('organic_whole_1l');
            // $table->integer('organic_skimmed_2l')->after('organic_semi_skimmed_2l');
            // $table->integer('organic_whole_2l')->after('organic_skimmed_2l');
            // $table->date('invoiced_at')->nullable()->after('organic_whole_2l');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('milk_boxes', function (Blueprint $table) {
            //
        });
    }
}
