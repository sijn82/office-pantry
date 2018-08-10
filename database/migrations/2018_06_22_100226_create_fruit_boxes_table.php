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
            $table->string('is_active');
            $table->string('name');
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
