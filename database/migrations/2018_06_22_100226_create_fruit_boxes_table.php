<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFruitBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fruit_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active')->default('Active');
            $table->string('name');
            $table->integer('company_details_id');
            $table->integer('route_id'); // not sure i need this, i'm certainly not using it currently!
            $table->string('type');
            $table->string('frequency');
            $table->string('delivery_day');
            $table->integer('boxes_total');
            $table->integer('deliciously_red_apples');
            $table->integer('pink_lady_apples');
            $table->integer('red_apples');
            $table->integer('green_apples');
            $table->integer('satsumas');
            $table->integer('pears');
            $table->integer('bananas');
            $table->integer('nectarines');
            $table->integer('limes');
            $table->integer('lemons');
            $table->integer('grapes');
            $table->integer('seasonal_berries');
            $table->integer('oranges');
            $table->integer('cucumbers');
            $table->integer('mint');
            $table->integer('organic_lemons');
            $table->integer('kiwis');
            $table->integer('grapefruits');
            $table->integer('avocados');
            $table->integer('root_ginger');
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
        Schema::dropIfExists('fruit_boxes');
    }
}
