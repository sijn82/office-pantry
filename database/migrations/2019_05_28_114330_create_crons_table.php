<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crons', function (Blueprint $table) {
            // these are the fields we want in the db
            $table->string('command');
            $table->integer('next_run');
            $table->integer('last_run');
            $table->timestamps();
            // these are additional connections to affect the behaviour taken from tutorial - https://medium.com/@nicolasbistolfi/running-laravel-scheduled-jobs-on-heroku-3a7bd6fa2481
            $table->primary('command');
            $table->index('next_run');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crons');
    }
}
