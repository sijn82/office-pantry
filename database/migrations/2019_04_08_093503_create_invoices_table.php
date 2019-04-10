<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('contact_name'); // Company Invoice Name
            $table->string('email_address'); // Company Invoice Email
            $table->string('po_address_line_1'); // Invoice Address Line 1
            $table->string('po_address_line_2'); //  Invoice Address Line 2
            $table->string('po_city'); // Invoice Address City
            $table->string('po_region'); // Invoice Address Region
            $table->string('po_post_code'); // Invoice Address PostCode
            $table->string('invoice_number'); // YYMMDD-000
            $table->date('invoice_date'); // Week Start
            $table->date('due_date'); // Due Date (Processing date, usually the day of processing but I want to add an option to set a day, or two delay).
            $table->string('description'); // Main Box Groups (1 x Fruitbox Price, 2 x Fruitbox Price, 1l Milk, Snacks) & (Otherbox, Product Names)
            $table->integer('quantity'); // Quantity of particular box group or particular product.
            $table->string('account_code'); // Sales Nominal i.e 4010, 4020, 4040, 4050, 4090, 4100.
            $table->integer('unit_amount'); // Cost (Price Sold For)
            $table->integer('tax_amount'); // How much of that is Taxed
            $table->string('tax_type'); // Whether item was Zero Rated Income Or 20% (VAT on Income)
            $table->string('branding_theme'); // Company Branding Theme (Payment Method)
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
        Schema::dropIfExists('invoices');
    }
}
