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
            $table->string('name'); // Everything but the name is a nullable field to allow us to submit the form with the minimum of details, but if they're more forthcoming, to hold as much as we can.
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('url')->nullable();
            $table->string('primary_contact_first_name')->nullable();
            $table->string('primary_contact_surname')->nullable();
            $table->string('secondary_contact_first_name')->nullable();
            $table->string('secondary_contact_surname')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postcode')->nullable();
            $table->string('alternative_telephone')->nullable();
            $table->string('weekly_action')->nullable();
            $table->string('changes_action')->nullable();
            $table->string('status')->nullable();
            $table->integer('no_of_customers')->nullable();
            $table->string('use_op_boxes')->nullable();
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
