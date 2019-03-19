<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFruitPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fruit_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('telephone');
            $table->string('url');
            $table->string('primary_contact');
            $table->string('secondary_contact');
            $table->string('alternative_telephone');
            $table->string('location');
            $table->string('coordinates'); // This is subject to change but until we're using it I'm not going to worry about the best form to store the data in.
            $table->string('weekly_action');
            $table->string('changes_action');
            $table->integer('no_of_customers');
            $table->string('additional_info'); // This can hold box prices, specific reminders etc.
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
        Schema::dropIfExists('fruit_partners');
    }
}
