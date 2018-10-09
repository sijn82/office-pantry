<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_lists', function (Blueprint $table) {
            $table->increments('id'); //not using this, not sure picklist_id should represent this either?
            $table->date('week_start');
            $table->string('company_name');
            $table->string('fruit_crates')->nullable();
            $table->string('fruit_boxes')->nullable();
            $table->integer('deliciously_red_apples')->nullable();
            $table->integer('pink_lady_apples')->nullable();
            $table->integer('red_apples')->nullable();
            $table->integer('green_apples')->nullable();
            $table->integer('satsumas')->nullable();
            $table->integer('pears')->nullable();
            $table->integer('bananas')->nullable();
            $table->integer('nectarines')->nullable();
            $table->integer('limes')->nullable();
            $table->integer('lemons')->nullable();
            $table->integer('grapes')->nullable();
            $table->integer('seasonal_berries')->nullable();
            $table->integer('oranges')->nullable();
            $table->integer('cucumbers')->nullable();
            $table->integer('mint')->nullable();
            $table->string('assigned_to')->default('TBC');
            $table->integer('position_on_route')->nullable();
            $table->string('delivery_day');
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
        Schema::dropIfExists('pick_lists');
    }
}
