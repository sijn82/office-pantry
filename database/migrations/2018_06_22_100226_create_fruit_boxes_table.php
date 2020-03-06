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
    public function up() // Migration Updated!
    {
        Schema::create('fruit_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('is_active')->default('Active');
            $table->integer('fruit_partner_id');
            $table->string('name');
            $table->integer('company_details_id');
            // $table->integer('route_id'); // not sure i need this, i'm certainly not using it currently!
            $table->string('type');
            $table->date('previous_delivery')->nullable();
            $table->date('delivery_week');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->string('delivery_day');
            $table->integer('fruitbox_total');
            $table->integer('deliciously_red_apples')->default(0);
            $table->integer('pink_lady_apples')->default(0);
            $table->integer('red_apples')->default(0);
            $table->integer('green_apples')->default(0);
            $table->integer('satsumas')->default(0);
            $table->integer('pears')->default(0);
            $table->integer('bananas')->default(0);
            $table->integer('nectarines')->default(0);
            $table->integer('limes')->default(0);
            $table->integer('lemons')->default(0);
            $table->integer('grapes')->default(0);
            $table->integer('seasonal_berries')->default(0);
            $table->integer('oranges')->default(0);
            $table->integer('cucumbers')->default(0);
            $table->integer('mint')->default(0);
            $table->integer('organic_lemons')->default(0);
            $table->integer('kiwis')->default(0);
            $table->integer('grapefruits')->default(0);
            $table->integer('avocados')->default(0);
            $table->integer('root_ginger')->default(0);
            $table->decimal('tailoring_fee', 4, 2)->nullable();
            $table->string('discount_multiple');
            $table->date('invoiced_at')->nullable(); // This is a new field to hopefully keep track of when orders have been processed.
            // If we stop reusing the same id - plan a - then we'll need another way to track changes.
            $table->string('order_changes')->nullable();
            $table->date('date_changed')->nullable();
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
