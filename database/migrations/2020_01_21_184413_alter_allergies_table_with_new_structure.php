<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAllergiesTableWithNewStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allergies', function (Blueprint $table) {
            //
            $table->dropColumn('company_details_id');
            $table->dropColumn('dietary_requirements');
            $table->dropColumn('snackbox_id');
            $table->renameColumn('allergy', 'name');
            $table->string('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allergies', function (Blueprint $table) {
            //
            $table->renameColumn('name', 'allergy');
            $table->dropColumn('slug');
        });
    }
}
