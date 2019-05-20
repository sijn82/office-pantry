<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_details_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade'); // Adding cascade here to prevent the foreign key issue when refreshing (rollback) migrations out of a natural sequence.
            $table->integer('company_details_id')->unsigned();
            $table->foreign('company_details_id')->references('id')->on('company_details')
                ->onDelete('cascade'); // Adding cascade here to prevent the foreign key issue when refreshing (rollback) migrations out of a natural sequence.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_details_user');
    }
}
