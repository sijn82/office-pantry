<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndFrequencyToFruitBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('fruit_boxes', function (Blueprint $table) {
            //
            $table->string('type')->after('route_id')->nullable();
            $table->string('frequency')->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('fruit_boxes', function (Blueprint $table) {
            
            $table->dropColumn('type');
            $table->dropColumn('frequency');
        });
    }
}
