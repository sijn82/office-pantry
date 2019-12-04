<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandAndFlavourToReplaceNameInDrinkboxArchives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drink_box_archives', function (Blueprint $table) {
            //
            $table->renameColumn('name', 'brand');
            $table->renameColumn('unit_price', 'selling_unit_price');
            $table->renameColumn('case_price', 'selling_case_price');
            $table->string('flavour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drink_box_archives', function (Blueprint $table) {
            //
        });
    }
}
