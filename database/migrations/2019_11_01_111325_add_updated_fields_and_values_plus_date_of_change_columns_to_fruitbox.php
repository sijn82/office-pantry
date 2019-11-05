<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedFieldsAndValuesPlusDateOfChangeColumnsToFruitbox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fruit_boxes', function (Blueprint $table) {
            //
            $table->string('order_changes')->nullable();
            $table->date('date_changed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fruit_boxes', function (Blueprint $table) {
            //
        });
    }
}
