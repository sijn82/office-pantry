<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHazelnutAndSoyaChocToMilkboxArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('milk_box_archives', function (Blueprint $table) {
            //
            $table->string('name')->nullable(); // Not ideal to make it nullable but will prevent all existing milkboxes
            $table->integer('milk_1l_alt_hazelnut')->default(0);
            $table->integer('milk_1l_alt_soya_chocolate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('milk_box_archives', function (Blueprint $table) {
            //
            $table->dropColumn('name', 'milk_1l_alt_hazelnut', 'milk_1l_alt_soya_chocolate');
        });
    }
}
