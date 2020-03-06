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
    public function up() // Migration Updated!
    {
        Schema::create('milk_boxes', function (Blueprint $table) {
            // Company & order details
            $table->increments('id');
            $table->string('is_active')->default('Active');
            $table->string('name');
            $table->integer('fruit_partner_id');
            $table->integer('company_details_id');
            $table->date('previous_delivery')->nullable();
            $table->date('delivery_week');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->string('delivery_day');
            // Regular 2l milk
            $table->integer('semi_skimmed_2l')->default(0);
            $table->integer('skimmed_2l')->default(0);
            $table->integer('whole_2l')->default(0);
            // Regular 1l milk
            $table->integer('semi_skimmed_1l')->default(0);
            $table->integer('skimmed_1l')->default(0);
            $table->integer('whole_1l')->default(0);
            // Organic 1l options
            $table->integer('organic_semi_skimmed_1l')->default(0);
            $table->integer('organic_skimmed_1l')->default(0);
            $table->integer('organic_whole_1l')->default(0);
            // Organic 2l options
            $table->integer('organic_semi_skimmed_2l')->default(0);
            $table->integer('organic_skimmed_2l')->default(0);
            $table->integer('organic_whole_2l')->default(0);
            // Alternative 1l milks
            $table->integer('milk_1l_alt_coconut')->default(0);
            $table->integer('milk_1l_alt_unsweetened_almond')->default(0);
            $table->integer('milk_1l_alt_almond')->default(0);
            // Alt pt2
            $table->integer('milk_1l_alt_unsweetened_soya')->default(0);
            $table->integer('milk_1l_alt_soya')->default(0);
            $table->integer('milk_1l_alt_soya_chocolate')->default(0);
            // Alt pt3
            $table->integer('milk_1l_alt_oat')->default(0);
            $table->integer('milk_1l_alt_cashew')->default(0);
            $table->integer('milk_1l_alt_hazelnut')->default(0);
            // Alt pt4
            $table->integer('milk_1l_alt_rice')->default(0);
            $table->integer('milk_1l_alt_lactose_free_semi')->default(0);
            // Invoice, created and updated at info
            $table->date('invoiced_at')->nullable();
            // Track changes to order, subject to change if we stop reusing id's           
            $table->string('order_changes')->nullable();
            $table->date('date_changed')->nullable();
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
