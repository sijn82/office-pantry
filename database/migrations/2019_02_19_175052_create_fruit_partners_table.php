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
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('url')->nullable();
            $table->string('primary_contact')->nullable();
            $table->string('secondary_contact')->nullable();
            $table->string('alternative_telephone')->nullable();
            $table->string('location')->nullable();
            $table->string('coordinates')->nullable(); // This is subject to change but until we're using it I'm not going to worry about the best form to store the data in.
            $table->string('weekly_action')->nullable();
            $table->string('changes_action')->nullable();
            $table->integer('no_of_customers')->nullable();
            $table->string('additional_info')->nullable(); // This can hold box prices, specific reminders etc.
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
