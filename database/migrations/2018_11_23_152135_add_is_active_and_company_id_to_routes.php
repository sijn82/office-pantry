<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsActiveAndCompanyIdToRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            //
            $table->string('is_active')->after('id')->default('Active');
            $table->integer('company_details_id')->after('week_start'); // When we get rid of the week_start (if we do entirely?) this will break.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // an additional and technically unnecessary if statement, more a test than anything to keep and reproduce
        if (Schema::hasColumn('routes', 'company_id'))
        {
            Schema::table('routes', function (Blueprint $table) {
                //
                $table->dropColumn('company_id');
            });
        }
    }
}
