<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnackBoxArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snack_box_archives', function (Blueprint $table) {
            $table->bigIncrements('id'); // Looks like Laravel 5.8 introduced BigIncrements by default. I could change it back to Increments but I'd then I'd forever be altering the natural behaviour. Time to evolve!
            // $table->increments('id');
            // Snackbox Info
            $table->string('is_active'); // Unlike the 'SnackBox' model, I don't think we want them saved as 'Active' by default?  Though maybe I do? Urgh, I won't for now.
            $table->string('snackbox_id'); // This isn't an auto increment field, so using a combination of company id and uniqid() to generate a unique id per snackbox.
            $table->string('delivered_by');
            $table->integer('no_of_boxes')->nullable();
            $table->decimal('snack_cap', 4, 2); // I could allow greater numbers but a snack cap for an individual box costing more than £9999.99 seems highly unlikely!
            $table->string('type');
            // Company Info
            $table->integer('company_details_id');
            $table->string('delivery_day');
            $table->string('frequency');
            $table->string('week_in_month')->nullable();
            $table->string('previous_delivery_week')->nullable();
            $table->string('next_delivery_week');
            // Product Information
            $table->integer('product_id')->default(0);
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->date('invoiced_at')->nullable();
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
        Schema::dropIfExists('snack_box_archives');
    }
}
