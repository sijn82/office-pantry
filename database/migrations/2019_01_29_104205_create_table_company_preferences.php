<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCompanyPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_details_id');
            $table->string('snackbox_likes')->nullable();
            $table->string('snackbox_dislikes')->nullable();
            $table->string('snackbox_essentials')->nullable();
            $table->string('snackbox_essentials_quantity')->nullable();
            // $table->string('allergies')->nullable(); // moving to its own table, so deleting a like, dislike or essential can be cleaner.
            // $table->string('additional_notes')->nullable(); // probably moving this to the company table for the same reason.
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
        Schema::dropIfExists('company_preferences');
    }
}
